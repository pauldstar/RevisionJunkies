'use strict';

$(document).ready(_=>
{
  Game.start();
  Questions.load();
});

var Game = (_=>
{
  let _score, _status,
    _numStartTiles = 2,
    _gridSize = 3;

  function _gameScore(value)
  {
    if (value) _score = value;
    else return _score;
  }

  function _gameStatus()
  {
    let gameOver = _status['lost'] || _status['won'];
    return !gameOver;
  }

  function _checkGameStatus()
  {
    if (!Grid.movesAvailable()) _status['lost'] = true;
    if (!_gameStatus()) GridDisplay.message(false);
  }

  function _startGame()
  {
    _score = 0;
    _status = { lost: false, won: false };
    Grid.build(_gridSize);
    Grid.addStartTiles(_numStartTiles);
    GridDisplay.refresh();
  }

  function _restartGame()
  {
    _startGame();
    GridDisplay.restart();
  }

  function _move(e)
  {
    let preventMove = !_gameStatus() || !GridDisplay.tilesValuesAreSet();
    if (preventMove) return;

    let vector = Input.getVector(e);
    if (!vector) return;

    e.preventDefault();

    let moved = Grid.move(vector);

    if (moved)
    {
      Grid.addRandomTile();

      let maxMergeValue = Grid.getMaxMerge(true);

      if (maxMergeValue >= 9999) _status['won'] = true;
      _score = _status['won'] ? 9999 : maxMergeValue;

      GridDisplay.refresh();
    }
  }

  return {
    score: _gameScore,
    start: _startGame,
    move: _move,
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

  function _addStartTiles(numStartTiles)
  {
    for (let i = 0; i < numStartTiles; i++)
    {
      _addRandomTile();
    }
  }

  function _buildGrid(size)
  {
    _size = size;

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
    if (isNaN(val1) || isNaN(val2)) return false;

    let mod1 = val1 % 2 === 0,
      mod2 = val2 % 2 === 0;

    return mod1 === mod2;
  }

  function _tileMatchesAvailable()
  { // only gets called if no space is available
    let tile, matchesAvailable = false;

    _eachCell((x, y, tile) =>
    { // all cells have tiles
      if (!tile.floatValue) matchesAvailable = true;

      for (let direction = 0; direction < 4; direction++)
      {
        let vector = Input.getVector(null, direction);
        let cell = {
          x: x + vector.x,
          y: y + vector.y
        };

        let otherTile = _cellContent(cell),
          isMergeable = otherTile && ( !otherTile.floatValue ||
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
          let
            positions = _findFarthestPosition(cell, vector),
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
    availableCells: _availableCells,
    randomAvailableCell: _randomAvailableCell,
    insertTile: _insertTile,
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
  let _modalQtns = '.modal-qtn',
      _$modalQtns = $(_modalQtns),
      _$modalQtnsContent = _$modalQtns.find('.modal-content'),

      _$modalBoolean = $('#modal-qtn-boolean'),
      _$modalBooleanQ = _$modalBoolean.find('.modal-body'),

      _modalOptions = '#modal-qtn-options',
      _$modalOptions = $(_modalOptions),
      _$modalOptionsQ = _$modalOptions.find('.modal-body').eq(0),
      _$modalOptions1 = _$modalOptions.find('.modal-body').eq(1),
      _$modalOptions2 = _$modalOptions.find('.modal-body').eq(2),
      _$modalOptions3 = _$modalOptions.find('.modal-body').eq(3),
      _$modalOptions4 = _$modalOptions.find('.modal-body').eq(4),

      _$modalOptionsCarousel = $('#modal-qtn-carousel'),
      _$modalGameOver = $('#modal-game-over'),
      _$modalInstructions = $('#modal-instructions');

  $(document).on('hidden.bs.modal', _modalQtns, _resetModalOptions);

  function _nextAnswerOption()
  {
    _$modalOptionsCarousel.carousel('next');
  }

  function _resetModalOptions()
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

  function _move(e)
  {
    if (_$modalQtnsContent.attr('style')) return;

    let direction = Input.getDirection(e);

    switch (direction)
    {
      case 0: direction = 'top'; break;
      case 1: direction = 'right'; break;
      case 2: direction = 'bottom'; break;
      case 3: direction = 'left';
    }

    let swipeAnimationObj = {}, resetAnimationObj = {};

    swipeAnimationObj[direction] = '-500px';
    swipeAnimationObj['opacity'] = '0';

    _$modalQtnsContent.animate(swipeAnimationObj, 'fast', _=>
    {
      _$modalQtns.modal('hide');
    });
  }

  return {
    $qtns: _$modalQtns,
    $boolean: _$modalBoolean,
    $options: _$modalOptions,
    $gameOver: _$modalGameOver,
    $instructions: _$modalInstructions,
    nextAnswer: _nextAnswerOption,
    move: _move,
    showQuestion: _showQuestion
  }
})();

var Questions = (_=>
{
  let _gameLevel = 0, _questions = [];

  function _loadQuestions()
  {
    _gameLevel++;

    $.ajax({
      url: `${SITE_URL}/game/get_questions/${_gameLevel}`,
      dataType: 'JSON',
      success: data =>
      {
        data.forEach(question =>
        {
          _questions.push(question);
        });
      },
      error: e => { console.log(e) }
    });
  }

  function _getQuestion()
  {
    _questions.length === 2 && _loadQuestions();
    return _questions.shift();
  }

  return {
    load: _loadQuestions,
    get: _getQuestion
  }
})();

var GridDisplay = (_=>
{
  let _score = 0,
      _newTile = '.tile-new',
      _$tileContainer = $('#tile-container'),
      _$gameSection = $('#game-section'),
      _gameSection = '#game-section',
      _$gameMsg = $('#game-message'),
      _$gameScore = $('#game-score');

  function _restart()
  {
    _$gameMsg.removeClass('game-won');
    _$gameMsg.removeClass('game-over');
    _updateScore();
  }

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
        if (tile) _addTile(tile);
      });

      _updateScore();
      Game.checkStatus();
    });
  }

  function _message(won)
  {
    let
      type = won ? 'game-won' : 'game-over',
      message = won ? 'You win!' : 'Game over!';

    _$gameMsg.addClass(type);
    _$gameMsg.children('p').text(message);
  }

  function _updateScore()
  {
    _$gameScore.empty();
    _score = Game.score();
    _$gameScore.text(_score);
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
    let $tile = $(_newTile).eq(0);

		if (!$tile.length) return;

    if (_$gameMsg.css('display') === 'block')
    {
      _$gameMsg.css('display', 'none');
      return;
    }

    Modal.$qtns.on('hide.bs.modal', _=>
    {
      let min = 1, max = 40.9999,
      floatValue = Math.random() * (max - min) + min;

      if (floatValue > 30) floatValue = 'X';

      let intValue = Math.floor(floatValue),
      tileX = $tile.data('x'),
      tileY = $tile.data('y');

      let tile = Grid.cells[tileX][tileY];
      tile.floatValue = floatValue;

      $tile.text(isNaN(floatValue) ? 'X'  : intValue);
      $tile.data('val-set', '1');
      $tile.addClass(isNaN(floatValue) ? 'tile-nan btn-light focus' :
        intValue % 2 === 0 ? 'tile-even btn-primary focus' :
          'tile-odd btn-danger focus'
      );
      $tile.removeClass('tile-new');

      Game.checkStatus();

      Modal.$qtns.off('hide.bs.modal');
    });

    Modal.showQuestion();
  }

  function _addTile(tile)
  {
    let positionClass = _positionClass(tile.previousPosition || tile),

      statusClass = !tile.floatValue ? 'tile-new' :
        isNaN(tile.floatValue) ? 'tile-nan' :
        tile.getIntValue() % 2 === 0 ? 'tile-even' : 'tile-odd',

      classes = ['tile', statusClass, positionClass],
      textContent = tile.floatValue ? tile.getIntValue() : '?';

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
    restart: _restart,
    message: _message,
    tilesValuesAreSet: _tilesValuesAreSet
  }
})();

var Input = (_=>
{
  let _$newGameBtn = $('.btn-new-game'),
      _gestures = new Hammer(document);

  $(document).on('keydown', _input);
  _$newGameBtn.click(Game.restart);
  _gestures.get('pan').set({ direction: Hammer.DIRECTION_ALL });
  _gestures.on('panstart tap', _input);

  function _input(e)
  {
    switch (e.type)
    {
      case 'keydown': _keydownInput(e); break;
      case 'tap': _clickInput(e); break;
      case 'panstart': _swipeInput(e);
    }
  }

	function _clickInput(e)
	{
    let $target = $(e.target);
    if ($target.is('#btn-instructions')) return;

    let isGridClick = $target.is('#game-section') ||
    $target.parents('#game-section').length !== 0;

    let isModalClick = $target.is('.modal') ||
      $target.parents('.modal').length !== 0;

		if (isGridClick) GridDisplay.openTile();
    else if (isModalClick) Modal.nextAnswer();
	}

  function _swipeInput(e)
  {
    let $target = $(e.target);

    let isGridGesture = $target.is('#game-section') ||
      $target.parents('#game-section').length !== 0;

    let isModalGesture = $target.is('.modal') ||
      $target.parents('.modal').length !== 0;

    if (isGridGesture) Game.move(e);
    else if (isModalGesture) Modal.move(e);
  }

  function _keydownInput(e)
  {
    if ( $(e.target).is('.modal') )
    {
      switch(e.which)
      {
        case 32: Modal.nextAnswer(); break;
        case 38: case 37: case 40: case 39: Modal.move(e);
      }
      return;
    }

    switch(e.which)
    {
      case 32: GridDisplay.openTile(); break;
      case 38: case 37: case 40: case 39: Game.move(e);
    }
  }

  function _getInputDirection(e)
  { // up: 0, right: 1, down: 2, left: 3
    let keyCodeMap = {
      38: 0, 39: 1, 40: 2, 37: 3,
      8: 0, 4: 1, 16: 2, 2: 3
    };

    return keyCodeMap[e.which || e.direction];
  }

  function _getInputVector(e, direction)
  {
    if (e)
    {
      direction = _getInputDirection(e);
      if (direction === undefined) return null;
    }
    // Vectors representing tile movement
    let vectorMap = {
      0: {x: 0,  y: -1},
      1: {x: 1,  y: 0},
      2: {x: 0,  y: 1},
      3: {x: -1, y: 0}
    };

    return vectorMap[direction];
  }

  return {
    getVector: _getInputVector,
    getDirection: _getInputDirection
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
    let val = Math.floor(this.floatValue);
    return isNaN(val) ? 'X' : val;
  };
}
