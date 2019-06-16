<div class="section bg-dark fixed-top w-100 h-100" id="game-section">

    <div class="text-white ml-auto mr-auto" id="game-stats">
      <span id="game-score">0</span>
      <span class="float-right" id="game-timer">00:00</span>
    </div>

    <div class="ml-auto mr-auto" id="game-container">
      <div id="game-message">
        <p id="game-desktop-msg">Press Space To Start</p>
        <p id="game-mobile-msg">Tap To Start</p>
        <div class="lower">
          <a id="btn-instructions" href="#" data-toggle="modal" data-target="#modal-instructions">Instructions</a>
        </div>
      </div>

      <div id="grid-container">
        <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
      </div>

      <div id="tile-container"></div>

    </div>
</div>

<div class="modal fade modal-qtn" id="modal-qtn-boolean" tabindex="-1" role="dialog" aria-labelledby="true-false-modal" data-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Question</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        The scrapped Sonic the Hedgehog 2 level "Hidden Palace Zone" was later reused in the iOS port of the game.
      </div>
      <div class="modal-footer text-center">
        &larr;&darr;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&uarr;&rarr;
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-qtn" id="modal-qtn-options" tabindex="-1" role="dialog" aria-labelledby="multiple-choice-modal" data-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Question</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modal-qtn-carousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner text-center">
          <ol class="carousel-indicators">
            <li data-target="#modal-qtn-carousel" data-slide-to="0" class="active"></li>
            <li data-target="#modal-qtn-carousel" data-slide-to="1"></li>
            <li data-target="#modal-qtn-carousel" data-slide-to="2"></li>
            <li data-target="#modal-qtn-carousel" data-slide-to="3"></li>
            <li data-target="#modal-qtn-carousel" data-slide-to="4"></li>
          </ol>
          <div class="carousel-item active">
            <div class="modal-body">
              Which stage was planned to be a part of "Sonic the Hedgehog 2", but was scrapped during development?
            </div>
            <div class="modal-footer">Q</div>
          </div>
          <div class="carousel-item">
            <div class="modal-body">
              Which stage was planned to be a part of "Sonic the Hedgehog 2", but was scrapped during development?
            </div>
            <div class="modal-footer">&uarr;</div>
          </div>
          <div class="carousel-item">
            <div class="modal-body">
              Which stage was planned to be a part of "Sonic the Hedgehog 2", but was scrapped during development?
            </div>
            <div class="modal-footer">&rarr;</div>
          </div>
          <div class="carousel-item">
            <div class="modal-body">
              Which stage was planned to be a part of "Sonic the Hedgehog 2", but was scrapped during development?
            </div>
            <div class="modal-footer">&darr;</div>
          </div>
          <div class="carousel-item">
            <div class="modal-body">
              Which stage was planned to be a part of "Sonic the Hedgehog 2", but was scrapped during development?
            </div>
            <div class="modal-footer">&larr;</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-game-over" tabindex="-1" role="dialog" aria-labelledby="game-message-modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">You Lost...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Better luck next time. Your stats:
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-instructions">Instructions</button>
        <button type="button" class="btn btn-success btn-new-game">New Game</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-instructions" tabindex="-1" role="dialog" aria-labelledby="game-message-modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">Instructions</h5>
      </div>
      <div class="modal-body">
        Figure out the game and aim for the highest score & rank.<br/>
        <br/>
        Easy Right? Let's go!<br/>
        </br>
        <strong>Mobile:</strong> Tap & Swipe.<br/>
        <strong>Desktop:</strong> Space & Arrow keys.<br/>
        <br/>
        Simples!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
