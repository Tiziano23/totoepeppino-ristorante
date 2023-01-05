<?php

namespace Steampixel;

$title = $this->prop('title', [
  'type' => 'string',
  'required' => true
]);
$page = $this->prop('page', [
  'type' => 'string',
  'required' => true
]);
$content = $this->prop('content', [
  'type' => 'object',
  'required' => false
]);
?>

<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>

  <link rel="stylesheet" href="/lib/bootstrap-5.3.0-alpha1/css/bootstrap.min.css">
  <link rel="stylesheet" href="/lib/fontawesome-free-6.2.0-web/css/all.min.css">

  <link rel="stylesheet" href="/css/base.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <link rel="stylesheet" href="/css/<?= $page ?>.css">
</head>

<body>

  <nav class="navbar position-fixed">
    <button class="navbar-toggler bg-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#main-nav" aria-controls="main-nav">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="main-nav" aria-labelledby="main-nav-label">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="main-nav-label">Dashboard</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link <?= $page == 'admin' ? 'active' : '' ?>" href="/admin">Gestione menu</a></li>
          <li class="nav-item"><a class="nav-link <?= $page == 'account' ? 'active' : '' ?>" href="/account">Gestione account</a></li>
          <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container">
    <?php if (isset($content)) : ?>
      <?= $content->render() ?>
    <?php else : ?>
      <?= Component::create("pages/$page")->render() ?>
    <?php endif; ?>
  </main>

  <script src="/lib/bootstrap/js/bootstrap.bundle.js"></script>
</body>

</html>