<div class="bg-dark" id="navbar-section">
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark right-font d-flex justify-content-between">
      <a class="navbar-brand order-md-2" data-toggle="tooltip" title="Hi-Score" data-placement="bottom" href="<?= site_url() ?>">
        <h2 class="aether-font" id="site-logo">9p</h2>
        <span id="site-hiscore"><?= str_pad($hiScore, 4, 0, STR_PAD_LEFT) ?></span>
      </a>

      <span class="glyphicon glyphicon-menu-hamburger nav-link order-md-1" id="menu-toggle"></span>
    </nav>
  </div>
</div>
