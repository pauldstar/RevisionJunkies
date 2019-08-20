<div class="shadow bg-dark <?= $title === 'game' ? 'fixed-top' : '' ?>" id="navbar-section">
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark right-font">
      <a class="navbar-brand" href="#">
        <h2 class="aether-font" id="site-logo">9p</h2>
        <span id="site-hiscore"  data-toggle="tooltip" title="Hi-Score" data-placement="bottom">0000</span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav text-center ml-auto">
          <?php
            $nav_items = [
              'game', 'races', 'leaderboard', 'contact', 'login'
            ];

            foreach ($nav_items as $item):
              $active = $title === $item;
          ?>
              <li class="nav-item <?= $active ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url($item) ?>">
                  <?= ucfirst($item) ?>
                  <?php if ($active): ?>
                    <span class="sr-only">(current)</span>
                  <?php endif; ?>
                </a>
              </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </nav>
  </div>
</div>
