<?php
$categories = MenuCategory::fetchAll();
?>
<div class="menu" id="menu">
  <div class="section introduction">
    <img src="/images/logo_crop.png" alt="Logo" class="logo img-fluid" height="130px" width="220px">
    <div class="hstack justify-content-center gap-4">
      <span class="menu-text">Menu</span>
      <img class="arrow" src="/images/arrow.png" alt="Freccia verso destra" width="100px" height="100px">
    </div>
    <span class="divider"></span>
    <p class="text">
      L'indiscussa toscanità trova spazio nella tradizione proponendovi piatti dal sapore unico e indiscutibile.<br>
      Da questo connubio di sapori nasce la cucina tosco-napoletana di Giuseppe, il nostro chef che dopo aver girato il mondo mette le proprie radici in una terra ricca di sapori, profumi e tradizioni decidendo però di mescolarsi ai sapori, i profumi e le tradizioni della sua amata Napoli. <br>
      Da qui la nostra cucina, i nostri piatti, la nostra passione al servizio del vostro palato.
    </p>
    <img class="sign" src="/images/sign.png" alt="Firma dello chef" width="150px" height="100px">
  </div>

  <div class="section collage">
    <div class="hstack justify-content-center gap-4">
      <span class="menu-text">Menu</span>
      <img class="arrow" src="/images/arrow.png" alt="Freccia verso destra" width="100px" height="100px">
    </div>
    <img src="/images/collage.jpg" alt="Collage di piatti dello chef">
  </div>

  <?php foreach ($categories as $category) : ?>
    <?php
    $subsections = array();
    $subcategories = MenuSubCategory::fetchByCategory($category);
    array_push($subsections, [
      "name" => null,
      "entries" => MenuEntry::fetchByCategory($category)
    ]);
    foreach ($subcategories as $subcategory) {
      array_push($subsections, [
        "name" => $subcategory->getProperty('name'),
        "entries" => MenuEntry::fetchBySubCategory($subcategory)
      ]);
    }
    ?>
    <div class="section" style="background-color: #<?= $category->getProperty('color') ?>">
      <h1 class="section-title"><?= $category->getProperty('name'); ?></h1>
      <div class="section-content">
        <?php foreach ($subsections as $subsection) : ?>
          <?php if (count($subsection['entries']) != 0) : ?>
            <div class="subsection">
              <?php if (isset($subsection['name'])) : ?>
                <p class="subsection-title"><?= $subsection['name'] ?></p>
              <?php endif; ?>

              <?php foreach ($subsection['entries'] as $entry) : ?>
                <?php
                $priceArr = explode('.', $entry->getProperty('price'));
                $priceInt = $priceArr[0];
                $unit = "€";
                $priceDec = '00';
                if (isset($priceArr[1])) {
                  $decstr = str_contains($priceArr[1], 'h') ? str_replace("h", "", $priceArr[1]) : $priceArr[1];
                  $priceDec = str_pad($decstr, 2, "0");
                  if (str_contains($priceArr[1], 'h')) $unit = "€/hg";
                }
                ?>
                <div class="menu-entry">
                  <p class="title"><?= $entry->getProperty('title') ?></p>
                  <p class="descr"><span class="spacer"></span><?= str_replace("\n", "<br>", $entry->getProperty('descr')) ?></p>
                  <p class="price"><?= $priceInt ?><span class="decimal">,<?= $priceDec ?> <?= $unit ?></span></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <div class="page-indicator hstack align-items-center justify-content-center">
    <span class="ellipse active"></span>
    <span class="ellipse"></span>
    <?php for ($i = 0; $i < count($categories); $i++) : ?>
      <span class="ellipse"></span>
    <?php endfor; ?>
  </div>
</div>

<script src="/js/main.js"></script>