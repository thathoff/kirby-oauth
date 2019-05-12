<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Kirby OAuth 2 Login</title>

  <link rel="stylesheet" href="<?= $assetUrl ?>/css/app.css">

  <?php if (isset($config['css'])) : ?>
    <link rel="stylesheet" href="<?= Url::to($config['css']) ?>">
  <?php endif ?>

  <link rel="apple-touch-icon" href="<?= $assetUrl ?>/apple-touch-icon.png" />
  <link rel="shortcut icon" href="<?= $assetUrl ?>/favicon.png">
</head>
<body>
  <?= $icons ?>
  <div class="k-panel">
    <main class="k-panel-view">
      <div class="k-view k-login-view" data-align="center">
        <form class="k-login-form">
          <header class="k-header">
            <h1 data-size="huge" class="k-headline">Kirby OAuth 2 Login</h1>
          </header>

          <?php include("providers.php"); ?>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
