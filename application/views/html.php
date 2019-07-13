<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
    <title>quepenny ~ <?= $title ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Hind:500,700|Righteous&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/qp.css') ?>" rel="stylesheet" type="text/css">

    <?= $styles ?>

  </head>
  <body class="bg-dark">

    <?= $header ?>
    <?= $page_content ?>
    <?= $footer ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="<?= base_url('assets//js/jquery-3.4.1.min.js') ?>">\x3C/script>')</script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <script>
      if (!window.jQuery.fn.modal)
      {
        document.write('<script src="<?= base_url('assets/js/bootstrap.min.js') ?>">\x3C/script>');
        $('head').append('<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">');
      }
      $('[data-toggle="tooltip"]').tooltip();
    </script>

    <script> const SITE_URL = '<?= site_url() ?>' </script>

    <?= $scripts ?>

  </body>
</html>
