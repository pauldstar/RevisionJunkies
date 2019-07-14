<div class="d-flex fixed-top justify-content-center align-items-center" id="game-section">
  <div>

    <div class="d-flex justify-content-between text-white right-font" id="game-header">
      <span class="position-relative" id="game-score" data-toggle="tooltip" title="Score" data-placement="top">0000</span>
      <span class="position-relative" id="game-level" data-toggle="tooltip" title="Level" data-placement="top">1</span>
      <span class="position-relative" id="game-timer" data-toggle="tooltip" title="Timer" data-placement="top">00:00</span>
    </div>

    <div id="game-container">
      <div class="hind-700-font d-flex justify-content-center align-items-center" id="game-message">
        <div class="d-flex flex-column align-items-center" id="loading-msg">
          <h1 class="aether-font">9p</h1>
          <div id="game-loader-spinner"></div>
        </div>
        <p class="start-msg m-0 d-none">
          Level 1<br>
          Start Game<br><br>
          <button class="btn btn-dark instruction-btn right-font" data-toggle="modal" data-target="#modal-instructions">Instructions</button>
        </p>
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
      <button class="btn btn-danger float-right" data-toggle="modal" data-target="#modal-select-mode">Select Mode</button>
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
      <div class="carousel slide" id="modal-qtn-carousel" data-ride="carousel" data-interval="false">
        <div class="carousel-inner text-center">
          <ol class="carousel-indicators qtn-options">
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
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" onclick="location.href='<?= site_url('leaderboard') ?>'">Leaderboard</button>
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
        Congratulations. You have achieved a rare feat!<br><br>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span class="text-danger">
          <b>If you play quepenny, we'll assume you agree with our Terms & Conditions below.</b>
        </span><br>
        <br>
        Figure out the game and aim for the highest score (9999) & rank by winning in the shortest possible time.<br>
        <br>
        Easy Right? Play your card's right and you could win in less than 3 minutes, placing you in contention for this week's <a href="<?= site_url('prizes') ?>">cash prizes</a>.<br>
        <br>
        <b>Mobile:</b> Tap & Swipe.<br>
        <b>Desktop:</b> Space & Arrow keys.<br>
        <br>
        Simples. Let's go!<br>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-warning">Terms & Conditions</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-select-mode" tabindex="-1" role="dialog" aria-labelledby="select-mode-modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Mode</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="carousel slide" id="modal-select-mode-carousel" data-interval="false">
          <div class="carousel-inner text-center">
            <div class="carousel-item active" data-price="1111">
              <h4>1: Two Moves</h4>
              <p class="mt-3">
                Move twice for each correct answer.
              </p>
            </div>
            <div class="carousel-item" data-price="2222">
              <h4>2: 2nd Chance</h4>
              <p class="mt-3">
                Two chances to answer multiple choice questions.
              </p>
            </div>
            <div class="carousel-item" data-price="3333">
              <h4>3: Mix Two</h4>
              <p class="mt-3">
                Mix modes 1 and 2.
              </p>
            </div>
            <div class="carousel-item" data-price="4444">
              <h4>4: New Joiners</h4>
              <p class="mt-3">
                Merge any live tiles together.
              </p>
            </div>
            <div class="carousel-item" data-price="5555">
              <h4>5: Mix Three</h4>
              <p class="mt-3">
                Mix modes 1, 2, and 4.
              </p>
            </div>
            <div class="carousel-item" data-price="7777">
              <h4>6: Get Rid</h4>
              <p class="mt-3">
                Randomly remove a dead tile for each correct answer.
              </p>
            </div>
            <div class="carousel-item" data-price="9999">
              <h4>7: Mix All!</h4>
              <p class="mt-3">
                Mix modes 1, 2, 4, and 6.
              </p>
            </div>
          </div>
          <a class="carousel-control-prev select-mode" href="#modal-select-mode-carousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next select-mode" href="#modal-select-mode-carousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <h5 class="right-font m-0"><span class="aether-font">9p</span> 2222</h5>
        <button type="button" class="btn btn-info">Select</button>
      </div>
    </div>
  </div>
</div>
