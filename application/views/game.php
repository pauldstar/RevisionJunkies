<div class="d-flex fixed-top justify-content-center align-items-center" id="game-section">
  <div>

    <div class="d-flex justify-content-between text-white right-font" id="game-header">
      <span id="game-score" data-toggle="tooltip" title="Score" data-placement="top">0000</span>
      <span id="game-level" data-toggle="tooltip" title="Level" data-placement="top">1</span>
      <span id="game-timer" data-toggle="tooltip" title="Timer" data-placement="top">00:00</span>
    </div>

    <div id="game-container">
      <div class="hind-font d-flex justify-content-center align-items-center" id="game-message">
        <div class="d-flex flex-column align-items-center" id="loading-msg">
          <img id="game-loader-image" src="<?= base_url('assets/images/logo-black-min.png') ?>" height="60px" alt="quepenny">
          <div id="game-loader-spinner"></div>
        </div>
        <p class="start-msg d-none" id="game-desktop-msg">Level 1<br>Press Space To Start</p>
        <p class="start-msg d-none" id="game-mobile-msg">Level 1<br>Tap To Start</p>
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

    <div class="text-white ml-auto mr-auto right-font" id="game-footer">
      <button class="btn btn-success btn-new-game">New Game</button>
      <button class="btn btn-info float-right" data-toggle="modal" data-target="#modal-instructions">Instructions</button>
    </div>

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
      <div class="progress" style="height: 2px;">
        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <div class="modal-body text-center">...</div>
      <div class="modal-footer text-center">
        <h4>
          <span class="text-danger">&larr;&darr;</span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-success">&uarr;&rarr;</span>
        </h4>
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
      <div class="progress" style="height: 2px;">
        <div class="progress-bar bg-danger" role="progressbar" style="width: 71%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
            <div class="modal-body">...</div>
            <div class="modal-footer"><h4>Q</h4></div>
          </div>
          <div class="carousel-item">
            <div class="modal-body">...</div>
            <div class="modal-footer"><h4 class="text-success">&uarr;</h4></div>
          </div>
          <div class="carousel-item">
            <div class="modal-body">...</div>
            <div class="modal-footer"><h4 class="text-success">&rarr;</h4></div>
          </div>
          <div class="carousel-item">
            <div class="modal-body">...</div>
            <div class="modal-footer"><h4 class="text-success">&darr;</h4></div>
          </div>
          <div class="carousel-item">
            <div class="modal-body">...</div>
            <div class="modal-footer"><h4 class="text-success">&larr;</h4></div>
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
        Better luck next time...
        <!-- Your ranking:<br>
        <br>
        First Place<br>
        <br>
        Two Above<br>
        One Above<br>
        Your Rank<br>
        One Below<br>
        Two Below<br>
        <br>
        Last Place -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success btn-new-game" data-dismiss="modal" aria-label="Close">New Game</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-game-won" tabindex="-1" role="dialog" aria-labelledby="game-message-modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">You Won!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Congratulations. You have achieved a rare feat!<br /><br />
        You are truly awesome!
        <!-- Your ranking:<br>
        <br>
        First Place<br>
        <br>
        Two Above<br>
        One Above<br>
        Your Rank<br>
        One Below<br>
        Two Below<br>
        <br>
        Last Place -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success btn-new-game">New Game</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-instructions" tabindex="-1" role="dialog" aria-labelledby="game-instructions-modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">Instructions</h5>
      </div>
      <div class="modal-body">
        Figure out the game and aim for the highest score (9999) & rank by winning in the shortest possible time.<br>
        <br>
        Easy Right? Let's go!<br>
        <br>
        <strong>Mobile:</strong> Tap & Swipe.<br>
        <strong>Desktop:</strong> Space & Arrow keys.<br>
        <br>
        Simples!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
