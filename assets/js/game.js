'use strict';

$(_=> Game.start());

var Game = (_=>
{
  let _score, _status, _level;

  function _gameLevel(increment)
  {
    if (increment) _level++;
    return _level;
  }

  function _gameScore(value)
  {
    if (value) _score = value;
    else return _score;
  }

  function _gameOver()
  {
    return  _status['lost'] || _status['won'];
  }

  function _checkGameStatus(moved)
  {
    if (!Grid.movesAvailable()) _status['lost'] = true;

    if (_gameOver())
    {
      let status = _status['lost'] ? 'lost' : 'won';
      setTimeout(_=> Modal.gameOver(status), 1500);
    }
  }

  function _startGame()
  {
    _score = 0;
    _level = 0;
    _status = { lost: false, won: false };
    md5();
    Questions.load();
    Grid.build();
    Grid.addStartTiles();
    GridDisplay.refresh();
  }

  function _move(vector)
  {
    let preventMove = _gameOver() || !GridDisplay.tilesValuesAreSet();
    if (preventMove) return;

    let moved = Grid.move(vector);

    if (moved)
    {
      Grid.addRandomTile();

      let maxMergeValue = Grid.getMaxMerge(true);

      if (maxMergeValue >= 9999) _status['won'] = true;
      _score = _status['won'] ? 9999 : maxMergeValue;

      GridDisplay.refresh();
      _checkGameStatus(moved);
    }
  }

  return {
    score: _gameScore,
    start: _startGame,
    move: _move,
    level: _gameLevel,
    checkStatus: _checkGameStatus
  }
})();

var Grid = (_=>
{
  let _size, _cells = [];

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

  function _movesAvailable()
  {
  	return _availableCells(true) || _tileMatchesAvailable();
  }

  function _isSameNumberClass(val1, val2)
  {
    if (val1 === 0 || val2 === 0) return false;

    let mod1 = val1 % 2 === 0,
      mod2 = val2 % 2 === 0;

    return mod1 === mod2;
  }

  function _tileMatchesAvailable()
  { // only gets called if no space is available
    let tile, matchesAvailable = false;

    _eachCell((x, y, tile) =>
    { // all cells have tiles
      if (tile.floatValue === undefined) matchesAvailable = true;

      for (let direction = 0; direction < 4; direction++)
      {
        let vector = Input.vectorMap[direction];
        let cell = {
          x: x + vector.x,
          y: y + vector.y
        };

        let otherTile = _cellContent(cell),
          isMergeable = otherTile && ( otherTile.floatValue === undefined ||
            _isSameNumberClass(otherTile.getIntValue(), tile.getIntValue()) );

        if (isMergeable) matchesAvailable = true;
      }
    });

    return matchesAvailable;
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
    for (let x = 0; x < _size; x++)
    {
      for (let y = 0; y < _size; y++)
      {
        callback(x, y, _cells[x][y]);
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
    let
      cell, tile, moved = false,
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

  return {
    cells: _cells,
    build: _buildGrid,
    getMaxMerge: _getMaxMerge,
    move: _move,
    movesAvailable: _movesAvailable,
    eachCell: _eachCell,
    addRandomTile: _addRandomTile,
    addStartTiles: _addStartTiles
  }
})();

var Modal = (_=>
{
  let
    _$modalQtns = $('.modal-qtn'),
    _$modalQtnsContent = _$modalQtns.find('.modal-content'),

    _$modalBoolean = $('#modal-qtn-boolean'),
    _$modalBooleanQ = _$modalBoolean.find('.modal-body'),

    _$modalOptions = $('#modal-qtn-options'),
    _$modalOptionsQ = _$modalOptions.find('.modal-body').eq(0),
    _$modalOptions1 = _$modalOptions.find('.modal-body').eq(1),
    _$modalOptions2 = _$modalOptions.find('.modal-body').eq(2),
    _$modalOptions3 = _$modalOptions.find('.modal-body').eq(3),
    _$modalOptions4 = _$modalOptions.find('.modal-body').eq(4),
    _$modalOptionsCarousel = $('#modal-qtn-carousel'),

    _$modalGameOver = $('#modal-game-over'),
    _$modalGameWon = $('#modal-game-won'),

    _$modalInstructions = $('#modal-instructions'),
    _$modalSelectMode = $('#modal-select-mode'),
    _$modalSelectModeCarousel = $('#modal-select-mode-carousel');

  _$modalQtns.on('show.bs.modal', _resetModalQtnOptions);
  _$modalSelectMode.on('show.bs.modal', _=> _$modalSelectModeCarousel.carousel(0));

  function _nextAnswerOption()
  {
    _$modalOptionsCarousel.carousel('next');
  }

  function _resetModalQtnOptions()
  {
    _$modalQtnsContent.removeAttr('style');
    _$modalOptionsCarousel.carousel(0);
  }

  function _showQuestion()
  {
    let question = Questions.get();

    switch (question.type)
    {
      case 'multiple':
        _setModalOptions(question);
        _$modalOptions.modal('show');
        break;

      case 'boolean':
        _setModalBoolean(question);
        _$modalBoolean.modal('show');
        break;
    }
  }

  function _setModalBoolean(question)
  {
    _$modalBooleanQ.html(question.question);
  }

  function _setModalOptions(question)
  {
    _$modalOptionsQ.html(question.question);
    _$modalOptions1.html(question.options[0]);
    _$modalOptions2.html(question.options[1]);
    _$modalOptions3.html(question.options[2]);
    _$modalOptions4.html(question.options[3]);
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
    if (_$modalQtnsContent.attr('style')) return;

    switch (direction)
    {
      case 0: direction = 'top'; break;
      case 1: direction = 'right'; break;
      case 2: direction = 'bottom'; break;
      case 3: direction = 'left';
    }

    let swipeAnimation = {};
    swipeAnimation[direction] = '-500px';
    swipeAnimation['opacity'] = '0';

    _$modalQtnsContent.animate(swipeAnimation, 'fast').promise().then(_=>
    {
      Questions.scoreAnswer(direction);
    });
  }

  return {
    $questions: _$modalQtns,
    nextAnswer: _nextAnswerOption,
    move: _move,
    showQuestion: _showQuestion,
    gameOver: _gameOver
  }
})();

var Questions = (_=>
{
  let
    _questions,
    _currentQuestion,
    _questionAnswered;

  function _loadQuestions()
  {
    let level = Game.level(true);

    if (level === 1)
    {
      _questions = [];
      _questionAnswered = true;
      GridDisplay.message('start-off');
      GridDisplay.message('load-on');
    }

    $.ajax({
      url: `${SITE_URL}game/get_questions/${level}`,
      dataType: 'JSON',
      success: data =>
      {
        data.forEach(question =>
        {
          _questions.push(question);
        });

        if (level === 1)
        {
          GridDisplay.message('load-off');
          GridDisplay.message('start-on');
        }
      },
      error: e => console.log(e)
    });
  }

  function _getQuestion()
  {
    if (_questionAnswered)
    {
      _questions.length === 2 && _loadQuestions();

      _currentQuestion = _questions.shift();

      if (!_currentQuestion)
      {
        GridDisplay.message('load-on');

        let getQuestion = setInterval(_=>
        {
          _currentQuestion = _questions.shift();
          if (_currentQuestion)
          {
            clearInterval(getQuestion);
            GridDisplay.message('load-off');
          }
        }, 100);
      }

      _questionAnswered = false;
    }

    return _currentQuestion;
  }

  function _getAnswerHash(ansCode)
  {
    let ansHash = _currentQuestion.answerHash;

    switch (_currentQuestion.type)
    {
      case 'boolean':
        switch (ansCode)
        {
          case 1: return md5('True', ansHash);
          case 0: return md5('False', ansHash);
        }

      case 'multiple':
        return md5(_currentQuestion.optionsTrim[ansCode], ansHash);
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

  function _scoreAnswer(direction)
  {
    let
      ansCode = _getAnswerCode(direction),
      ansHash = _getAnswerHash(ansCode),
      id = _currentQuestion.id,
      score = 0;

    $.ajax({
      url: `${SITE_URL}game/score_user_answer/${ansCode}/${id}`,
      error: e => console.log(e)
    });

    Modal.$questions.modal('hide');

    if (ansHash === _currentQuestion.answerHash) score = _currentQuestion.score;
    GridDisplay.setTileValue(parseFloat(score));

    _questionAnswered = true;

    Game.checkStatus();
  }

  return {
    current: _=> _currentQuestion,
    load: _loadQuestions,
    get: _getQuestion,
    scoreAnswer: _scoreAnswer
  }
})();

var GridDisplay = (_=>
{
  let
    _score = 0,
    _level = 1,
    _loading = true,
    _newTile = '.tile-new',
    _$tileContainer = $('#tile-container'),
    _$gameSection = $('#game-section'),
    _gameSection = '#game-section',

    _$msgGame = $('#game-message'),
    _$msgLoading = $('#loading-msg'),
    _$msgStart = $('.start-msg'),
    _$msgGameLevel = $('#game-level-msg'),

    _$gameLevel = $('#game-level'),
    _$gameScore = $('#game-score'),
    _$newGameBtn = $('.btn-new-game'),
    _$currentNewTile;

  _$newGameBtn.click(Game.start);

  function _tilesValuesAreSet()
  {
    let valuesAreSet = true;

    $(_newTile).each(function()
    {
      if (!$(this).data('val-set')) valuesAreSet = false;
      return false;
    });

    return valuesAreSet;
  }

  function _refresh()
  {
    window.requestAnimationFrame(_=>
    {
      _$tileContainer.empty();

      Grid.eachCell((x, y, tile) =>
      {
        tile && _addTile(tile);
      });

      _updateScore();
      _updateLevel();
    });
  }

  function _message(message)
  {
    switch (message)
    {
      case 'load-on':
        _$msgLoading.removeClass('d-none');
        _$msgGame.removeClass('d-none');
        _loading = true;
        break;

      case 'load-off':
        _$msgGame.addClass('d-none');
        _$msgLoading.addClass('d-none');
        _loading = false;
        break;

      case 'start-on':
        _$msgGame.removeClass('d-none');
        _$msgStart.removeClass('d-none');
        break;

      case 'start-off':
        _$msgStart.addClass('d-none');
        _$msgGame.addClass('d-none');
        break;

      case 'level-up':
        _$msgGame.removeClass('d-none');
        _$msgGameLevel.removeClass('d-none');
        _$levelNumber.html(Game.level());
        setTimeout(_=>
        {
          _$msgGameLevel.addClass('d-none');
          _$msgGame.addClass('d-none');
        }, 1500);
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

  function _updateLevel()
  {
    if (Game.level() > _level) _bounce(_$gameLevel);
    _level = Game.level();
    _$gameLevel.text(_level);
  }

  function _updateScore()
  {
    if (Game.score() > _score) _bounce(_$gameScore);
    _score = Game.score();
    _$gameScore.text(_score.toString().padStart(4, '0'));
  }

  function _positionClass(position)
  {
    let
      positionX = position.x + 1,
      positionY = position.y + 1;

    return 'tile-position-' + positionX + '-' + positionY;
  }

  function _openTile()
  {
    if (_loading) return;

    _$currentNewTile = $(_newTile).eq(0);
		if (!_$currentNewTile.length) return;

    _message('start-off');
    Modal.showQuestion();
  }

  function _setTileValue(score)
  {
    let
      intValue = Math.floor(score),
      tileX = _$currentNewTile.data('x'),
      tileY = _$currentNewTile.data('y');

    let tile = Grid.cells[tileX][tileY];
    tile.floatValue = score;

    _$currentNewTile.text(score === 0 ? 'X' : intValue);
    _$currentNewTile.data('val-set', '1');
    _$currentNewTile.addClass(
      score === 0 ? 'tile-zero btn-light focus disabled' :
        intValue % 2 === 0 ? 'tile-even btn-primary focus disabled' :
          'tile-odd btn-danger focus disabled'
    );
    _$currentNewTile.removeClass('tile-new');
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

    let $element = $(`
      <div class="${classes.join(' ')}"
        data-x="${tile.x}" data-y="${tile.y}" data-val-set="">
        ${textContent}
      </div>
    `);

    if (tile.previousPosition)
    { // After rendering tile in previous position...
      window.requestAnimationFrame(_=>
      {
        classes[2] = _positionClass(tile);
        $element.attr('class', classes.join(' '));
      });
    }
    else if (tile.parentTiles)
    {
      classes.push('tile-merged');
      $element.attr('class', classes.join(' '));
      // Render the tiles that merged
      tile.parentTiles.forEach(tile =>
      {
        _addTile(tile);
      });
    }

    _$tileContainer.append($element);
  }

  return {
    openTile: _openTile,
    refresh: _refresh,
    message: _message,
    tilesValuesAreSet: _tilesValuesAreSet,
    setTileValue: _setTileValue
  }
})();

var Input = (_=>
{
  let
    _touchStartX,
    _touchStartY,
    _isSwipe = false,
    _inputDirectionMap = {
      up: 0, right: 1, down: 2, left: 3,
      38: 0, 39: 1, 40: 2, 37: 3
    },
    _inputVectorMap = {
      0: {x: 0,  y: -1}, 1: {x: 1,  y: 0},
      2: {x: 0,  y: 1}, 3: {x: -1, y: 0}
    };

  $(document).on('keydown touchstart touchmove touchend', _input);

  function _input(e)
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

  	if (isGridClick) GridDisplay.openTile();
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

    let
      threshold = 25, restraint = 12,
      touchObj = e.changedTouches[0],
      distX = touchObj.pageX - _touchStartX,
      distY = touchObj.pageY - _touchStartY;

    if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint)
      e.direction = (distX < 0) ? 3 : 1; // left : right
    else if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint)
      e.direction = (distY < 0) ? 0 : 2; // up : down

    if (isGridGesture)
    {
      let vector = _getInputVector(e.direction);
      vector && Game.move(vector);
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
      case 32: GridDisplay.openTile(); break;

      case 38: case 37: case 40: case 39:
        let vector = _getInputVector(e.which);
        vector && Game.move(vector);
    }
  }

  function _getInputVector(keycode)
  {
    let direction = _inputDirectionMap[keycode];
    if (direction === undefined) return null;
    return _inputVectorMap[direction];
  }

  return {
    vectorMap: _inputVectorMap
  }
})();

function Tile(position, value)
{
  this.x = position.x;
  this.y = position.y;
  this.floatValue = value;
  this.isMaxMerge = false;
  this.previousPosition = null;
  this.parentTiles = null;

  this.savePosition = function()
  {
    this.previousPosition = {x: this.x, y: this.y};
  };

  this.updatePosition = function(position)
  {
    this.x = position.x;
    this.y = position.y;
  };

  this.getIntValue = function()
  {
    return Math.floor(this.floatValue);
  };
}

var md5PrevHash = '';

var md5 = function(string, answerHash)
{
  function rotateLeft(lValue, iShiftBits)
  {
    return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
  }

  function addUnsigned(lX, lY)
  {
    let lX4, lY4, lX8, lY8, lResult;
    lX8 = (lX & 0x80000000);
    lY8 = (lY & 0x80000000);
    lX4 = (lX & 0x40000000);
    lY4 = (lY & 0x40000000);
    lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
    if (lX4 & lY4) return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
    if (lX4 | lY4)
    {
      if (lResult & 0x40000000) return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
      else return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
    }
    else return (lResult ^ lX8 ^ lY8);
  }

  function F(x,y,z) { return (x & y) | ((~x) & z) }
  function G(x,y,z) { return (x & z) | (y & (~z)) }
  function H(x,y,z) { return (x ^ y ^ z) }
  function I(x,y,z) { return (y ^ (x | (~z))) }

  function FF(a, b, c, d, x, s, ac)
  {
    a = addUnsigned(a, addUnsigned(addUnsigned(F(b, c, d), x), ac));
    return addUnsigned(rotateLeft(a, s), b);
  }

  function GG(a, b, c, d, x, s, ac)
  {
    a = addUnsigned(a, addUnsigned(addUnsigned(G(b, c, d), x), ac));
    return addUnsigned(rotateLeft(a, s), b);
  }

  function HH(a, b, c, d, x, s, ac)
  {
    a = addUnsigned(a, addUnsigned(addUnsigned(H(b, c, d), x), ac));
    return addUnsigned(rotateLeft(a, s), b);
  }

  function II(a, b, c, d, x, s, ac)
  {
    a = addUnsigned(a, addUnsigned(addUnsigned(I(b, c, d), x), ac));
    return addUnsigned(rotateLeft(a, s), b);
  }

  let x = Array();
  let k, AA, BB, CC, DD, a, b, c, d;
  let S11 = 7, S12 = 12, S13 = 17, S14 = 22;
  let S21 = 5, S22 = 9 , S23 = 14, S24 = 20;
  let S31 = 4, S32 = 11, S33 = 16, S34 = 23;
  let S41 = 6, S42 = 10, S43 = 15, S44 = 21;

  if (!string)
  {
    md5PrevHash = '';
    return;
  }

  string = Utf8Encode(string);
  x = ConvertToWordArray(string);

  a = 0x67452301;
  b = 0xEFCDAB89;
  c = 0x98BADCFE;
  d = 0x10325476;

  for (k = 0; k < x.length; k += 16)
  {
    AA = a; BB = b; CC = c; DD = d;
    a = FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
    d = FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
    c = FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
    b = FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
    a = FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
    d = FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
    c = FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
    b = FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
    a = FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
    d = FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
    c = FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
    b = FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
    a = FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
    d = FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
    c = FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
    b = FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
    a = GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
    d = GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
    c = GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
    b = GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
    a = GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
    d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
    c = GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
    b = GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
    a = GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
    d = GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
    c = GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
    b = GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
    a = GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
    d = GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
    c = GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
    b = GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
    a = HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
    d = HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
    c = HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
    b = HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
    a = HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
    d = HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
    c = HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
    b = HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
    a = HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
    d = HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
    c = HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
    b = HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
    a = HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
    d = HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
    c = HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
    b = HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
    a = II(a, b, c, d, x[k + 0], S41, 0xF4292244);
    d = II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
    c = II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
    b = II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
    a = II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
    d = II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
    c = II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
    b = II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
    a = II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
    d = II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
    c = II(c, d, a, b, x[k + 6], S43, 0xA3014314);
    b = II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
    a = II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
    d = II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
    c = II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
    b = II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
    a = addUnsigned(a, AA);
    b = addUnsigned(b, BB);
    c = addUnsigned(c, CC);
    d = addUnsigned(d, DD);
  }

  let temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);

  let newHash = temp.toLowerCase();
  if (answerHash === 1) return newHash;

  let test = md5(md5PrevHash + newHash, 1);
  md5PrevHash = answerHash;

  return test;

  function ConvertToWordArray(string)
  {
    let
      lWordCount,
      lMessageLength = string.length,
      lNumberOfWords_temp1 = lMessageLength + 8,
      lNumberOfWords_temp2 =
        (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64,
      lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16,
      lWordArray = Array(lNumberOfWords - 1),
      lBytePosition = 0,
      lByteCount = 0;

    while (lByteCount < lMessageLength)
    {
      lWordCount = (lByteCount - (lByteCount % 4)) / 4;
      lBytePosition = (lByteCount % 4) * 8;
      lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
      lByteCount++;
    }

    lWordCount = (lByteCount - (lByteCount % 4)) / 4;
    lBytePosition = (lByteCount % 4) * 8;
    lWordArray[lWordCount] =
      lWordArray[lWordCount] | (0x80 << lBytePosition);
    lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
    lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;

    return lWordArray;
  };

  function WordToHex(lValue)
  {
    let
      lByte, lCount,
      WordToHexValue = '',
      WordToHexValue_temp = '';

    for (lCount = 0; lCount <= 3; lCount++)
    {
      lByte = (lValue >>> (lCount * 8)) & 255;
      WordToHexValue_temp = '0' + lByte.toString(16);
      WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
    }

    return WordToHexValue;
  };

  function Utf8Encode(string)
  {
    string = string.replace(/\r\n/g, '\n');
    let utftext = '';

    for (let n = 0; n < string.length; n++)
    {
      let c = string.charCodeAt(n);

      if (c < 128) utftext += String.fromCharCode(c);
      else if ((c > 127) && (c < 2048))
      {
        utftext += String.fromCharCode((c >> 6) | 192);
        utftext += String.fromCharCode((c & 63) | 128);
      }
      else
      {
        utftext += String.fromCharCode((c >> 12) | 224);
        utftext += String.fromCharCode(((c >> 6) & 63) | 128);
        utftext += String.fromCharCode((c & 63) | 128);
      }
    }

    return utftext;
  };
}
