<?php if (! empty($errors)) : ?>
  <p class="errors text-danger" role="alert">
    <?= implode('<br>', esc($errors)) ?>
  </p>
<?php endif ?>