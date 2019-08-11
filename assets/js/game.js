/*
 * QuePenny v1.0
 *
 * Copyright (c) 2019 Paul Ogbeiwi;
 *
 * This uncompressed file is only viewable by quepenny developers; it contains
 *  vital game secrets.
 *
 * Note: The properties of modules have been stored in object variables on
 *  purpose; it enables their obfuscation.
 *
 * Coding Guidelines
 *
 * - There are two master modules: Game first, then Input (inside the Game
 *   module). Game is the master coordinator/mediator between modules. Input
 *   only triggers other module functions to handle events.
 *
 * - The other modules are helpers; they don't call functions in other
 *   modules. They only have functions that help the masters store, retrieve,
 *   update, and display game data.
 *
 * - 'use strict' should be placed at the start of every module instead of the
 *   start of this file. It messes up obfuscation.
 *
 * Manual Obfuscation CLI commands
 *
 * Note: obfuscation is triggered automatically in controllers/Webhooks.php
 * via assets/obfuscate-game.js whenever github tags are pushed. But if you
 * insist:
 *
 * 1. Install javascript-obfuscator
 *   - install node.js
 *   - then run: npm install --save-dev javascript-obfuscator
 *   - or run: npm link javascript-obfuscator (if the above doesn't work)
 *
 * 2. Navigate to the assets/js folder and run:
 *   - node obfuscate-game.js (can edit onfuscation config in this file)
 */

$(_=> Game.load());

var Game = (_=>
{ 'use strict';
  let _score,
      _status,
      _level,
      _startTime,
      _gameStarted,
      _loading,
      _timerInterval,
      _questionInterval;

  function _gameIsOver()
  {
    return _status['lost'] || _status['won'];
  }

  function _checkGameStatus(moved)
  {
    if (!Grid.movesAvailable(Input.vectorMap)) _status['lost'] = true;
    if (_gameIsOver()) _endGame();
  }

  function _loadGame()
  {
    _score = 0;
    _level = 1;
    _status = { lost: false, won: false };
    _gameStarted = false;
    Md5();
    clearInterval(_timerInterval);
    clearInterval(_questionInterval);
    GridDisplay.updateTimer();
    Grid.build();
    Grid.addStartTiles();
    GridDisplay.refresh(Grid.eachCell, _level, _score);
    GridDisplay.message('start-off');
    _loading = GridDisplay.message('load-on');
    Questions.array(false);

    $.ajax({
      url: `${SITE_URL}game/load_game`,
      dataType: 'JSON',
      success: data => data.forEach(question => Questions.array(question))
    })
    .done(data =>
    {
      _loading = GridDisplay.message('load-off');
      GridDisplay.message('start-on');
    });
  }

  function _loadQuestions()
  {
    _level++;

    let getQuestionsAjax = $.ajax({
      url: `${SITE_URL}game/get_questions`,
      dataType: 'JSON',
      success: data => data.forEach(question => Questions.array(question))
    });
  }

  function _startGame()
  {
    if (_gameStarted) return true;

    $.ajax({ url: `${SITE_URL}game/start_game` });

    GridDisplay.message('start-off');

    _startTime = Date.now();
    let timeDelta;

    _timerInterval = setInterval(_=>
    {
      timeDelta = Date.now() - _startTime;
      GridDisplay.updateTimer(timeDelta);
    }, 1000);

    _gameStarted = true;
  }

  function _endGame()
  {
    let status = _status['lost'] ? 'lost' : 'won';

    setTimeout(_=> Modal.gameOver(status), 1500);
    clearInterval(_timerInterval);
    clearInterval(_questionInterval);

    let timeDelta = Math.floor((Date.now() - _startTime) / 1000)

    $.ajax({ url: `${SITE_URL}game/end_game/${_score}/${timeDelta}` });
  }

  function _move(vector)
  {
    let preventMove = _gameIsOver() || !Grid.allTileValuesSet();
    if (preventMove) return;

    let moved = Grid.move(vector);

    if (moved)
    {
      Grid.addRandomTile();

      let maxMergeValue = Grid.getMaxMerge(true);

      if (maxMergeValue >= 9999) _status['won'] = true;
      _score = _status['won'] ? 9999 : maxMergeValue;

      GridDisplay.refresh(Grid.eachCell, _level, _score);
      _checkGameStatus(moved);
    }
  }

  function _showQuestion()
  {
    if (_loading || _gameIsOver() || Grid.allTileValuesSet()) return;

    if (Questions.array().length === 2) _loadQuestions();

    let question = Questions.get();

    let displayQuestionModal = _=>
    {
      Modal.showQuestion(question);
      Modal.onAnswer(_answerQuestion);
    };

    if (question) displayQuestionModal();
    else
    {
      _loading = GridDisplay.message('load-on');

      _questionInterval = setInterval(_=>
      {
        question = Questions.get();

        if (question)
        {
          clearInterval(_questionInterval);
          _loading = GridDisplay.message('load-off');
          displayQuestionModal();
        }
      }, 500);
    }
  }

  function _answerQuestion(direction)
  {
    let score = Questions.scoreAnswer(direction, Md5);
    GridDisplay.setTileValue(parseFloat(score), Grid.cells);
    _checkGameStatus();
  }

  var Input = (_=>
  {
    let _touchStartX,
        _touchStartY,
        _isSwipe = false,
        _$newGameBtn = $('.btn-new-game'),
        _inputDirectionMap = { 38: 0, 39: 1, 40: 2, 37: 3 },
        _inputVectorMap = {
          0: { x: 0,  y: -1 },
          1: { x: 1,  y: 0 },
          2: { x: 0,  y: 1 },
          3: { x: -1, y: 0 }
        };

    _$newGameBtn.click(e =>
    {
      $('#game-section').focus();
      _loadGame();
    });

    $('.modal').on('hidden.bs.modal', _=> $('#game-section').focus());

    $(document).on('keydown touchstart touchmove touchend', _inputHandler);

    function _inputHandler(e)
    {
      switch (e.type)
      {
        case 'keydown': _keydownInput(e); break;

        case 'touchstart':
          let touchObj = e.changedTouches[0];
          _touchStartX = touchObj.pageX;
          _touchStartY = touchObj.pageY;
          break;

        case 'touchmove': _isSwipe = true; break;

        case 'touchend':
          if (_isSwipe)
          {
            _swipeInput(e);
            _isSwipe = false;
          }
          else _clickInput(e);
      }
    }

    function _clickInput(e)
    {
      let $target = $(e.target);

      if ($target.is('.instruction-btn')) return;

      let isGridClick = $target.is('#game-container') ||
      $target.parents('#game-container').length !== 0;

      let isModalClick = $target.is('.modal') ||
        $target.parents('.modal').length !== 0;

    	if (isGridClick) _startGame() && _showQuestion();
      else if (isModalClick)
        $(e.target).is('.carousel-indicators li') || Modal.nextAnswer();
    }

    function _swipeInput(e)
    {
      let $target = $(e.target);

      let isGridGesture = $target.is('#game-section') ||
        $target.parents('#game-section').length !== 0;

      let isModalGesture = $target.is('.modal') ||
        $target.parents('.modal').length !== 0;

      let threshold = 50, // min distance traveled to be considered swipe
          restraint = 100, // max distance allowed in perpendicular direction
          touchObj = e.changedTouches[0],
          distX = touchObj.pageX - _touchStartX,
          distY = touchObj.pageY - _touchStartY;

      if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint)
        e.direction = (distX < 0) ? 3 : 1; // left : right
      else if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint)
        e.direction = (distY < 0) ? 0 : 2; // up : down

      if (isGridGesture)
      {
        let vector = _inputVectorMap[e.direction];
        vector && _move(vector);
      }
      else if (isModalGesture)
        e.direction !== undefined && Modal.move(e.direction);
    }

    function _keydownInput(e)
    {
      if ( $(e.target).is('.modal') )
      {
        switch(e.which)
        {
          case 32: Modal.nextAnswer(); break;

          case 38: case 37: case 40: case 39:
            let direction = _inputDirectionMap[e.which];
            Modal.move(direction);
        }
        return;
      }

      switch(e.which)
      {
        case 32:
          if ($(e.target).is('button')) break;
          _startGame() && _showQuestion(); break;

        case 38: case 37: case 40: case 39:
          let direction = _inputDirectionMap[e.which],
              vector = _inputVectorMap[direction];
          vector && _move(vector);
      }
    }

    let _Input = { vectorMap: _inputVectorMap };

    return _Input;
  })();

  let _Game = { load: _loadGame };

  return _Game;
})();

var Grid = (_=>
{ 'use strict';
  let _size, _cells = [];

  function Tile(position, value)
  {
    this.x = position.x;
    this.y = position.y;
    this.floatValue = value;
    this.isMaxMerge = false;
    this.previousPosition = null;
    this.parentTiles = null;
  }

  Tile.prototype.savePosition = function()
  {
    this.previousPosition = {x: this.x, y: this.y};
  };

  Tile.prototype.updatePosition = function(position)
  {
    this.x = position.x;
    this.y = position.y;
  };

  Tile.prototype.getIntValue = function()
  {
    return Math.floor(this.floatValue);
  };

  function _allTileValuesSet()
  {
    let valuesAreSet = true;

    _eachCell((x, y, tile) =>
    {
      if (tile && tile.floatValue === undefined)
      {
        valuesAreSet = false;
        return false;
      }
    });

    return valuesAreSet;
  }

  function _addRandomTile()
  {
    let cell = _randomAvailableCell();

    if (cell)
    {
      let tile = new Tile(cell);
      _insertTile(tile);
    }
  }

  function _addStartTiles()
  {
    for (let i = 0; i < 2; i++)
    {
      _addRandomTile();
    }
  }

  function _buildGrid()
  {
    _size = 4;

    for (let x = 0; x < _size; x++)
    {
      let row = _cells[x] = [];

      for (let y = 0; y < _size; y++)
      {
        row.push(null);
      }
    }
  }

  function _buildTraversals(vector)
  { // Build a list of positions to traverse in the right order
    let traversals = { x: [], y: [] };

    for (let pos = 0; pos < _size; pos++)
    {
      traversals.x.push(pos);
      traversals.y.push(pos);
    }
    // Always traverse from the farthest cell in the chosen direction
    if (vector.x === 1) traversals.x = traversals.x.reverse();
    if (vector.y === 1) traversals.y = traversals.y.reverse();

    return traversals;
  }

  function _movesAvailable(inputVectorMap)
  {
  	if (_availableCells(true)) return true;

    let cell, tile, otherTile, tileIntValue,
        vector, isMergeable, matchesAvailable = false;

    _eachCell((x, y, tile) =>
    {
      tileIntValue = tile.getIntValue();

      for (let direction = 0; direction < 4; direction++)
      {
        vector = inputVectorMap[direction];
        cell = {
          x: x + vector.x,
          y: y + vector.y
        };

        otherTile = _cellContent(cell);

        if (tile.floatValue) isMergeable = otherTile &&
          _isSameNumberClass(tileIntValue, otherTile.getIntValue());
        else if (tile.floatValue === undefined)
           isMergeable = otherTile && otherTile.floatValue;

        if (isMergeable) matchesAvailable = true;
      }

      if (matchesAvailable) return false;
    });

    return matchesAvailable;
  }

  function _isSameNumberClass(val1, val2)
  {
    if (!val1 || !val2) return false;

    let mod1 = val1 % 2 === 0,
        mod2 = val2 % 2 === 0;

    return mod1 === mod2;
  }

  function _withinBounds(position)
  {
    return position.x >= 0 && position.x < _size &&
  		position.y >= 0 && position.y < _size;
  }

  function _cellContent(cell)
  {
    if (_withinBounds(cell)) return _cells[cell.x][cell.y];
    return null;
  }

  function _findFarthestPosition(cell, vector)
  {
    let previous;
    // Progress towards the vector direction until an obstacle is found
    do {
      previous = cell;
      cell = {
        x: previous.x + vector.x,
        y: previous.y + vector.y
      };
    } while (_withinBounds(cell) && _cellContent(cell) === null);

    return {
      farthest: previous,
      next: cell // Used to check if a merge is required
    };
  }

  function _prepareTiles()
  {
    _eachCell((x, y, tile) =>
    {
      if (tile)
      {
        tile.parentTiles = null;
        tile.savePosition();
      }
    });
  }

  function _getMaxMerge(returnIntValue)
  {
    let maxMerge = null;

    _eachCell((x, y, tile) =>
    {
      if (tile && tile.isMaxMerge) maxMerge = tile;
    });

    return returnIntValue ? (maxMerge ? maxMerge.getIntValue() : 0) : maxMerge;
  }

  function _eachCell(callback)
  {
    let result;

    for (let x = 0; x < _size; x++)
    {
      for (let y = 0; y < _size; y++)
      {
        result = callback(x, y, _cells[x][y]);
        if (result === false) return;
      }
    }
  }

  function _availableCells(returnBoolean)
  {
    let availableCells = returnBoolean ? false : [];

    _eachCell((x, y, tile) =>
    {
      if (!tile)
      {
        if (returnBoolean) availableCells = true;
        else availableCells.push({x: x, y: y});
        return false;
      }
    });

    return availableCells;
  }

  function _randomAvailableCell()
  {
    let cells = _availableCells();

    if (cells.length)
    {
      return cells[Math.floor(Math.random() * cells.length)];
    }
  }

  function _insertTile(tile)
  {
    _cells[tile.x][tile.y] = tile;
  }

  function _removeTile(tile)
  {
    _cells[tile.x][tile.y] = null;
  }

  function _moveTile(tile, cell)
  {
    _cells[tile.x][tile.y] = null;
    _cells[cell.x][cell.y] = tile;
    tile.updatePosition(cell);
  }

  function _updateMaxMerged(merged)
  {
    let maxMerge = _getMaxMerge();

    if (maxMerge)
    {
      if (merged.floatValue > maxMerge.floatValue)
      {
        maxMerge.isMaxMerge = false;
        merged.isMaxMerge = true;
        maxMerge = merged;
      }
    }
    else
    {
      merged.isMaxMerge = true;
      maxMerge = merged;
    }
  }

  function _move(vector)
  {
    let cell, tile, moved = false,
        traversals = _buildTraversals(vector);

    _prepareTiles();
    // Traverse the grid in the right direction and move tiles
    traversals.x.forEach(x =>
    {
      traversals.y.forEach(y =>
      {
        cell = {x: x, y: y};
			  tile = _cellContent(cell);

        if (tile)
        {
          let positions = _findFarthestPosition(cell, vector),
              next = _cellContent(positions.next);
          // Only one merger per row traversal?
          let isMergeable = next && !next.parentTiles &&
            _isSameNumberClass(next.getIntValue(), tile.getIntValue());

          if (!isMergeable) _moveTile(tile, positions.farthest);
          else
          {
            let mergeValue = tile.floatValue + next.floatValue;
            if (mergeValue > 9999) mergeValue = 9999;
            let merged = new Tile(positions.next, mergeValue);

            merged.parentTiles = [tile, next];

            _insertTile(merged);
            _removeTile(tile);
            // Converge the two tiles' positions
            tile.updatePosition(positions.next);
            _updateMaxMerged(merged);
          }

          moved = moved || ! (cell.x === tile.x && cell.y === tile.y);
        }
      });
    });

    return moved;
  }

  let _Grid = {
    cells: _cells,
    build: _buildGrid,
    getMaxMerge: _getMaxMerge,
    move: _move,
    movesAvailable: _movesAvailable,
    eachCell: _eachCell,
    addRandomTile: _addRandomTile,
    addStartTiles: _addStartTiles,
    allTileValuesSet: _allTileValuesSet
  };

  return _Grid;
})();

var GridDisplay = (_=>
{ 'use strict';
  let _score = 0,
      _level = 1,
      _newTile = '.tile-new',
      _$tileTemplate = $('#tile-template'),
      _$tileContainer = $('#tile-container'),
      _$gameSection = $('#game-section'),
      _gameSection = '#game-section',

      _$msgGame = $('#game-message'),
      _$msgLoading = $('#loading-msg'),
      _$msgStart = $('.start-msg'),
      _$msgGameLevel = $('#game-level-msg'),

      _$gameLevel = $('#game-level'),
      _$gameScore = $('#game-score'),
      _$gameTimer = $('#game-timer'),
      _$currentNewTile;

  function _refresh(gridIterator, gameLevel, gameScore)
  {
    window.requestAnimationFrame(_=>
    {
      _$tileContainer.empty();

      gridIterator((x, y, tile) =>
      {
        tile && _addTile(tile);
      });

      _updateLevel(gameLevel);
      _updateScore(gameScore);
    });
  }

  function _message(message, param)
  {
    switch (message)
    {
      case 'load-on':
        _$msgLoading.removeClass('d-none');
        _$msgGame.removeClass('d-none');
        return true;

      case 'load-off':
        _$msgGame.addClass('d-none');
        _$msgLoading.addClass('d-none');
        return false;

      case 'start-on':
        _$msgGame.removeClass('d-none');
        _$msgStart.removeClass('d-none');
        break;

      case 'start-off':
        _$msgStart.addClass('d-none');
        _$msgGame.addClass('d-none');
        break;
    }
  }

  function _bounce($element)
  {
    let interval = 100, distance = 20, times = 6, damping = 0.8;

    for (let i = 0; i < (times + 1); i++)
    {
      let amt = Math.pow(-1, i) * distance / (i * damping);
      $element.animate({ top: amt }, 100);
    }

    $element.animate({ top: 0 }, interval);
  }

  function _updateLevel(gameLevel)
  {
    if (gameLevel > _level) _bounce(_$gameLevel);
    _level = gameLevel;
    _$gameLevel.text(_level);
  }

  function _updateScore(gameScore)
  {
    if (gameScore > _score) _bounce(_$gameScore);
    _score = gameScore;
    _$gameScore.text(_score.toString().padStart(4, '0'));
  }

  function _updateTimer(timeDelta)
  {
    if (!timeDelta) return void(_$gameTimer.text('00:00'));

    let seconds = Math.floor(timeDelta / 1000),
        minutes = Math.floor(seconds / 60);
    seconds = seconds - (minutes * 60);

    seconds = seconds.toString().padStart(2, '0');
    minutes = minutes.toString().padStart(2, '0');

    _$gameTimer.text(`${minutes}:${seconds}`);
  }

  function _positionClass(position)
  {
    let positionX = position.x + 1,
        positionY = position.y + 1;

    return 'tile-position-' + positionX + '-' + positionY;
  }

  function _setTileValue(score, gridCells)
  {
    let intValue = Math.floor(score),
        $newTile = $(_newTile).eq(0),
        tileX = $newTile.data('x'),
        tileY = $newTile.data('y');

    let tile = gridCells[tileX][tileY];
    tile.floatValue = score;

    $newTile.text(score === 0 ? 'X' : intValue);
    $newTile.data('val-set', '1');
    $newTile.addClass(
      score === 0 ? 'tile-zero btn-light focus disabled' :
        intValue % 2 === 0 ? 'tile-even btn-primary focus disabled' :
          'tile-odd btn-danger focus disabled'
    );
    $newTile.removeClass('tile-new');
  }

  function _addTile(tile)
  {
    let positionClass = _positionClass(tile.previousPosition || tile);

    let statusClass = tile.floatValue === undefined ? 'tile-new' :
      tile.floatValue === 0 ? 'tile-zero' :
      tile.getIntValue() % 2 === 0 ? 'tile-even' : 'tile-odd';

    let classes = ['tile', statusClass, positionClass],
        textContent = tile.floatValue === undefined ? '?' :
          tile.floatValue === 0 ? 'X' : tile.getIntValue();

    if (tile.isMaxMerge) classes.push('tile-max');

    let $newTile = _$tileTemplate.clone();
    $newTile.attr('class', classes.join(' '));
    $newTile.attr('id', '');
    $newTile.data('x', tile.x);
    $newTile.data('y', tile.y);
    $newTile.text(textContent);

    if (tile.previousPosition)
    { // After rendering tile in previous position...
      window.requestAnimationFrame(_=>
      {
        classes[2] = _positionClass(tile);
        $newTile.attr('class', classes.join(' '));
      });
    }
    else if (tile.parentTiles)
    {
      classes.push('tile-merged');
      $newTile.attr('class', classes.join(' '));
      // Render the tiles that merged
      tile.parentTiles.forEach(tile =>
      {
        _addTile(tile);
      });
    }

    _$tileContainer.append($newTile);
  }

  let _GridDisplay = {
    refresh: _refresh,
    message: _message,
    updateTimer: _updateTimer,
    setTileValue: _setTileValue
  };

  return _GridDisplay;
})();

var Questions = (_=>
{ 'use strict';
  let _questions,
      _currentQuestion,
      _questionAnswered;

  function _getQuestion()
  {
    if (_questionAnswered)
    {
      _currentQuestion = _questions.shift();
      if (_currentQuestion) _questionAnswered = false;
    }

    return _currentQuestion;
  }

  function _getAnswerHash(ansCode, hashFn)
  {
    let ansHash = _currentQuestion.ah;

    switch (_currentQuestion.type)
    {
      case 'boolean':
        switch (ansCode)
        {
          case 1: return hashFn(ansHash, 'True');
          case 0: return hashFn(ansHash, 'False');
          default: return hashFn(ansHash);
        }

      case 'multiple':
        switch (ansCode)
        {
          case 0: case 1: case 2: case 3:
            return hashFn(ansHash, _currentQuestion.optionsTrim[ansCode]);
          default: return hashFn(ansHash);
        }
    }
  }

  function _getAnswerCode(direction)
  {
    switch (_currentQuestion.type)
    {
      case 'boolean':
        switch (direction)
        {
          case 'top': case 'right': return 1;
          case 'bottom': case 'left': return 0;
        }

      case 'multiple':
        switch (direction)
        {
          case 'top': return 0;
          case 'right': return 1;
          case 'bottom': return 2;
          case 'left': return 3;
        }
    }
  }

  function _scoreAnswer(direction, hashFn)
  {
    let ansCode = direction ? _getAnswerCode(direction) : '',
        ansHash = _getAnswerHash(ansCode, hashFn),
        id = _currentQuestion.id,
        score = 0;

    $.ajax({ url: `${SITE_URL}game/score_user_answer/${id}/${ansCode}` });

    // TODO: game.js: remove answer scoring test
    let ans = _currentQuestion.type === 'boolean' ?
      ( ansCode === 1 ? 'True' : ansCode === 0 ?  'False' : undefined ) :
      _currentQuestion.optionsTrim[ansCode];

    console.log('chose: '+ans);
    console.log('right: '+_currentQuestion.correct);
    console.log('nhash: '+ansHash);
    console.log('chash: '+_currentQuestion.hashes[ansCode]);
    console.log('ahash: '+_currentQuestion.ah);
    if (ansHash === _currentQuestion.hashes[ansCode])
      console.log('correct scoring');
    else alert('incorrect scoring');
    console.log('-------------');

    if (ansHash === _currentQuestion.ah) score = _currentQuestion.score;
    _questionAnswered = true;

    return score;
  }

  function _questionsArray(question)
  {
    if (question) _questions.push(question);
    else if (question === false)
    {
      _questions = [];
      _questionAnswered = true;
    }
    else return _questions;
  }

  let _Questions = {
    array: _questionsArray,
    current: _=> _currentQuestion,
    get: _getQuestion,
    scoreAnswer: _scoreAnswer
  };

  return _Questions;
})();

var Modal = (_=>
{ 'use strict';
  let _$modalQtns = $('.modal-qtn'),
      _$modalQtnsContent = _$modalQtns.find('.modal-content'),

      _modalSwiped = false,
      _swipeDirection,
      _modalProgressTimerFinished = false,
      _$modalProgress = $('.progress-bar'),

      _$modalBoolean = $('#modal-qtn-boolean'),
      _$modalBooleanQ = _$modalBoolean.find('.modal-body'),

      _$modalOptions = $('#modal-qtn-options'),
      _$modalOptionsQ = _$modalOptions.find('.modal-body').eq(0),
      _$modalOptions1 = _$modalOptions.find('.modal-body').eq(1),
      _$modalOptions2 = _$modalOptions.find('.modal-body').eq(2),
      _$modalOptions3 = _$modalOptions.find('.modal-body').eq(3),
      _$modalOptions4 = _$modalOptions.find('.modal-body').eq(4),
      _$modalOptionsProgress = $('#modal-options-progress'),
      _$modalOptionsCarousel = $('#modal-qtn-carousel'),

      _$modalGameOver = $('#modal-game-over'),
      _$modalGameWon = $('#modal-game-won'),

      _$modalInstructions = $('#modal-instructions'),
      _$modalMode = $('#modal-select-mode'),
      _$modalModeCarousel = $('#modal-select-mode-carousel');

  _$modalQtns.on('show.bs.modal', _resetModalQtnOptions);
  _$modalMode.on('show.bs.modal', _=> _$modalModeCarousel.carousel(0));

  function _nextAnswerOption()
  {
    _$modalOptionsCarousel.carousel('next');
  }

  function _resetModalQtnOptions()
  {
    _$modalQtnsContent.removeAttr('style');
    _$modalOptionsCarousel.carousel(0);
  }

  function _showQuestion(question)
  {
    switch (question.type)
    {
      case 'multiple':
        _$modalOptionsQ.html(question.question);
        _$modalOptions1.html(question.options[0]);
        _$modalOptions2.html(question.options[1]);
        _$modalOptions3.html(question.options[2]);
        _$modalOptions4.html(question.options[3]);
        _$modalOptions.modal('show');
        _startProgressTimer(10);
        break;

      case 'boolean':
        _$modalBooleanQ.html(question.question);
        _$modalBoolean.modal('show');
        _startProgressTimer(5);
    }
  }

  function _startProgressTimer(runTimeSeconds)
  {
    let runTimeMilliSeconds = runTimeSeconds * 1000,
        timeStamp = Date.now(),
        timeEnd = timeStamp + runTimeMilliSeconds + 700,
        progress = 0,
        percentage = 0,
        progressMod = 0;

    _modalSwiped = false;
    _modalProgressTimerFinished = false;

    let progressTimer = _=>
    {
      if (_modalSwiped) return;

      progress++;
      progressMod = progress % 6;

      if (progressMod === 0)
      {
        _$modalProgress.css('width', `${percentage}%`);
        percentage = ((progress * 16.667)  / runTimeMilliSeconds) * 100;
        timeStamp = Date.now();
      }

      if (timeStamp >= timeEnd)
      {
        _modalProgressTimerFinished = true;
        _$modalQtns.modal('hide');
        _$modalProgress.css('width', 0);
      }
      else window.requestAnimationFrame(progressTimer);
    };

    window.requestAnimationFrame(progressTimer);
  }

  function _gameOver(status)
  {
    switch (status)
    {
      case 'lost': _$modalGameOver.modal('show'); break;
      case 'won': _$modalGameWon.modal('show');
    }
  }

  function _move(direction)
  {
    if (_modalProgressTimerFinished) return;

    _modalSwiped = true;

    if (_$modalQtnsContent.attr('style')) return;

    switch (direction)
    {
      case 0: _swipeDirection = 'top'; break;
      case 1: _swipeDirection = 'right'; break;
      case 2: _swipeDirection = 'bottom'; break;
      case 3: _swipeDirection = 'left';
    }

    let swipeAnimation = {};
    swipeAnimation[_swipeDirection] = '-500px';
    swipeAnimation['opacity'] = '0';

    _$modalQtnsContent.animate(swipeAnimation, 'fast').promise().then(_=>
    {
      _$modalQtns.modal('hide');
    });
  }

  function _onAnswer(callback)
  {
    _$modalQtns.on('hide.bs.modal', _=> callback(_swipeDirection));
    _$modalQtns.on('hidden.bs.modal', _=>
    {
      _$modalQtns.off('hide.bs.modal');
      _$modalQtns.off('hidden.bs.modal');
      _swipeDirection = null;
    });
  }

  let _Modal = {
    nextAnswer: _nextAnswerOption,
    move: _move,
    showQuestion: _showQuestion,
    onAnswer: _onAnswer,
    gameOver: _gameOver
  };

  return _Modal;
})();

var Md5 = (_=>
{ 'use strict';
  let _oldHash = '';

  function _run(answerHash, string)
  {
    if (!answerHash) return void(_oldHash = '');
    if (!string) return void(_oldHash = answerHash);

    let newHash = _md5(string),
        test = _md5(_oldHash + newHash);

    _oldHash = answerHash;

    return test;
  }

  function _md5(e)
  {
    function f(a,b){return a<<b|a>>>32-b}function g(a,b){var c,d,e,f,g;return e=2147483648&a,f=2147483648&b,c=1073741824&a,d=1073741824&b,g=(1073741823&a)+(1073741823&b),c&d?2147483648^g^e^f:c|d?1073741824&g?3221225472^g^e^f:1073741824^g^e^f:g^e^f}function h(a,b,c){return a&b|~a&c}function i(a,b,c){return a&c|b&~c}function j(a,b,c){return a^b^c}function l(a,b,c){return b^(a|~c)}function m(e,i,b,c,d,j,k){return e=g(e,g(g(h(i,b,c),d),k)),g(f(e,j),i)}function n(e,h,b,c,d,j,k){return e=g(e,g(g(i(h,b,c),d),k)),g(f(e,j),h)}function o(e,h,b,c,d,i,k){return e=g(e,g(g(j(h,b,c),d),k)),g(f(e,i),h)}function p(e,h,b,c,d,i,j){return e=g(e,g(g(l(h,b,c),d),j)),g(f(e,i),h)}function q(a){for(var b,c=a.length,d=c+8,e=16*((d-d%64)/64+1),f=Array(e-1),g=0,h=0;h<c;)b=(h-h%4)/4,g=8*(h%4),f[b]|=a.charCodeAt(h)<<g,h++;return b=(h-h%4)/4,g=8*(h%4),f[b]|=128<<g,f[e-2]=c<<3,f[e-1]=c>>>29,f}function r(a){var b,c,d="",e="";for(c=0;3>=c;c++)b=255&a>>>8*c,e="0"+b.toString(16),d+=e.substr(e.length-2,2);return d}function s(a){a=a.replace(/\r\n/g,"\n");for(var b,d="",e=0;e<a.length;e++)b=a.charCodeAt(e),128>b?d+=String.fromCharCode(b):127<b&&2048>b?(d+=String.fromCharCode(192|b>>6),d+=String.fromCharCode(128|63&b)):(d+=String.fromCharCode(224|b>>12),d+=String.fromCharCode(128|63&b>>6),d+=String.fromCharCode(128|63&b));return d}var t,u,v,w,y,z,A,B,C,D=[],E=7,F=12,G=17,H=22,I=5,J=9,K=14,L=20,M=4,N=11,O=16,P=23,Q=6,R=10,S=15,T=21;for(e=s(e),D=q(e),z=1732584193,A=4023233417,B=2562383102,C=271733878,t=0;t<D.length;t+=16)u=z,v=A,w=B,y=C,z=m(z,A,B,C,D[t+0],E,3614090360),C=m(C,z,A,B,D[t+1],F,3905402710),B=m(B,C,z,A,D[t+2],G,606105819),A=m(A,B,C,z,D[t+3],H,3250441966),z=m(z,A,B,C,D[t+4],E,4118548399),C=m(C,z,A,B,D[t+5],F,1200080426),B=m(B,C,z,A,D[t+6],G,2821735955),A=m(A,B,C,z,D[t+7],H,4249261313),z=m(z,A,B,C,D[t+8],E,1770035416),C=m(C,z,A,B,D[t+9],F,2336552879),B=m(B,C,z,A,D[t+10],G,4294925233),A=m(A,B,C,z,D[t+11],H,2304563134),z=m(z,A,B,C,D[t+12],E,1804603682),C=m(C,z,A,B,D[t+13],F,4254626195),B=m(B,C,z,A,D[t+14],G,2792965006),A=m(A,B,C,z,D[t+15],H,1236535329),z=n(z,A,B,C,D[t+1],I,4129170786),C=n(C,z,A,B,D[t+6],J,3225465664),B=n(B,C,z,A,D[t+11],K,643717713),A=n(A,B,C,z,D[t+0],L,3921069994),z=n(z,A,B,C,D[t+5],I,3593408605),C=n(C,z,A,B,D[t+10],J,38016083),B=n(B,C,z,A,D[t+15],K,3634488961),A=n(A,B,C,z,D[t+4],L,3889429448),z=n(z,A,B,C,D[t+9],I,568446438),C=n(C,z,A,B,D[t+14],J,3275163606),B=n(B,C,z,A,D[t+3],K,4107603335),A=n(A,B,C,z,D[t+8],L,1163531501),z=n(z,A,B,C,D[t+13],I,2850285829),C=n(C,z,A,B,D[t+2],J,4243563512),B=n(B,C,z,A,D[t+7],K,1735328473),A=n(A,B,C,z,D[t+12],L,2368359562),z=o(z,A,B,C,D[t+5],M,4294588738),C=o(C,z,A,B,D[t+8],N,2272392833),B=o(B,C,z,A,D[t+11],O,1839030562),A=o(A,B,C,z,D[t+14],P,4259657740),z=o(z,A,B,C,D[t+1],M,2763975236),C=o(C,z,A,B,D[t+4],N,1272893353),B=o(B,C,z,A,D[t+7],O,4139469664),A=o(A,B,C,z,D[t+10],P,3200236656),z=o(z,A,B,C,D[t+13],M,681279174),C=o(C,z,A,B,D[t+0],N,3936430074),B=o(B,C,z,A,D[t+3],O,3572445317),A=o(A,B,C,z,D[t+6],P,76029189),z=o(z,A,B,C,D[t+9],M,3654602809),C=o(C,z,A,B,D[t+12],N,3873151461),B=o(B,C,z,A,D[t+15],O,530742520),A=o(A,B,C,z,D[t+2],P,3299628645),z=p(z,A,B,C,D[t+0],Q,4096336452),C=p(C,z,A,B,D[t+7],R,1126891415),B=p(B,C,z,A,D[t+14],S,2878612391),A=p(A,B,C,z,D[t+5],T,4237533241),z=p(z,A,B,C,D[t+12],Q,1700485571),C=p(C,z,A,B,D[t+3],R,2399980690),B=p(B,C,z,A,D[t+10],S,4293915773),A=p(A,B,C,z,D[t+1],T,2240044497),z=p(z,A,B,C,D[t+8],Q,1873313359),C=p(C,z,A,B,D[t+15],R,4264355552),B=p(B,C,z,A,D[t+6],S,2734768916),A=p(A,B,C,z,D[t+13],T,1309151649),z=p(z,A,B,C,D[t+4],Q,4149444226),C=p(C,z,A,B,D[t+11],R,3174756917),B=p(B,C,z,A,D[t+2],S,718787259),A=p(A,B,C,z,D[t+9],T,3951481745),z=g(z,u),A=g(A,v),B=g(B,w),C=g(C,y);var U=r(z)+r(A)+r(B)+r(C);return U.toLowerCase()
  }

  return _run;
})();
