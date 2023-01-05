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
  <meta name="description" content="Menu digitale della pizzeria Totò e Peppino a Galleno, Fucecchio (FI).">
  <title><?= $title ?></title>

  <link rel="stylesheet" href="/lib/bootstrap-5.3.0-alpha1/css/bootstrap.min.css">
  <link rel="stylesheet" href="/lib/fontawesome-free-6.2.0-web/css/all.min.css">

  <link rel="stylesheet" href="/css/base.css">
  <link rel="stylesheet" href="/css/<?= $page ?>.css">
</head>

<body>
  <main>
    <?php if (isset($content)) : ?>
      <?= $content->render() ?>
    <?php else : ?>
      <?= Component::create("pages/$page")->render() ?>
    <?php endif; ?>
  </main>

  <script src="/lib/bootstrap/js/bootstrap.bundle.js"></script>
</body>

</html>