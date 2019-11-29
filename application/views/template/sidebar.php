<nav class="navbar-dark right-font fixed-top" id="sidebar-wrapper">

  <div id="sidebar-content">

    <div class="overflow-hidden p-3">
      <div id="user-pic">
        <?php if ($logged_in && $user->photo): ?>
          <img class="img-responsive img-rounded" src="<?= base_url("assets/images/users/{$user->photo}") ?>" alt="User picture">
        <?php elseif ($logged_in && !$user->photo): ?>
          <i class="glyphicon glyphicon-queen"></i>
        <?php else: ?>
          <i class="glyphicon glyphicon-pawn"></i>
        <?php endif ?>
      </div>
      <div class="float-left text-white">
        <span class="d-block"><?= $logged_in ? $user->username : '&nbsp;' ?></span>
        <span class="d-block" id="user-role" style="<?= $logged_in ? "color:{$user->league_color};" : '' ?>">
          <?= $logged_in ? $user->league_name : 'Guest' ?>
        </span>
        <span class="d-block" id="qp-status">
          <span class="aether-font">9p&nbsp;</span><?= metric_format($user->total_qp, 2) ?>
        </span>
      </div>
    </div>

    <div class="mb-2" id="sidebar-menu">
      <ul>
        <li class="header-menu hind-font-500">
          <span>General</span>
        </li>

        <?php
          foreach ($nav_items['main'] as $item => $prop):
            if ($item === 'profile') continue;
            $active = $title === $item ? 'active' : '';
            $color = "text-{$prop['color']}-hover";
        ?>
          <li>
            <a href="<?= site_url($item) ?>" class="<?= $color ?> <?= $active ?>">
              <i class="glyphicon glyphicon-<?= $prop['glyphicon'] ?>"></i>
              <span><?= ucfirst($item) ?></span>
              <span class="badge badge-pill badge-<?= $prop['color'] ?>">5</span>
            </a>
          </li>
        <?php endforeach ?>

        <?php if ($logged_in): ?>
          <li class="header-menu hind-font-500">
            <span>Profile</span>
          </li>

          <?php foreach ($nav_items['profile'] as $item => $prop):
            $active = $title === $item;
          ?>
            <li>
              <a href="<?= $item !== 'profile' ? site_url($item) : '#' ?>" class="text-<?= $prop['color'] ?>-hover">
                <i class="glyphicon glyphicon-<?= $prop['glyphicon'] ?>"></i>
                <span><?= ucfirst($item) ?></span>
                <span class="badge badge-pill badge-<?= $prop['color'] ?>">10</span>
              </a>
            </li>
          <?php endforeach ?>

        <?php endif ?>
      </ul>
    </div>

  </div>

</nav>
