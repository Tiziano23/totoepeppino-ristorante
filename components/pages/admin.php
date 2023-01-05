<?php $categories = MenuCategory::fetchAll(); ?>

<h1 class="display-1 text-center mb-4"><strong>Menu</strong></h1>

<div id="menu">
  <?php foreach ($categories as $category) : ?>
    <?php
    $subsections = array();
    $subcategories = MenuSubCategory::fetchByCategory($category);
    array_push($subsections, [
      "id" => -1,
      "name" => null,
      "entries" => MenuEntry::fetchByCategory($category)
    ]);
    foreach ($subcategories as $subcategory) {
      array_push($subsections, [
        "id" => $subcategory->getId(),
        "name" => $subcategory->getProperty('name'),
        "entries" => MenuEntry::fetchBySubCategory($subcategory)
      ]);
    }
    ?>

    <div class="section" data-id="<?= $category->getId(); ?>">
      <input type="text" class="title fs-1" data-model="name" maxlength="100" disabled value="<?= $category->getProperty('name'); ?>" />

      <div class="controls hstack gap-4">
        <input type="color" data-model="color" value="#<?= $category->getProperty('color'); ?>">
        <i class=" btn-edit fs-3 fa-solid fa-pencil"></i>
        <i class="btn-next fs-2 fa-regular fa-circle-down"></i>
        <i class="btn-prev fs-2 fa-regular fa-circle-up"></i>
        <i class="btn-del fs-2 fa-solid fa-times"></i>
      </div>

      <div class="subsections">
        <?php foreach ($subsections as $subsection) : ?>
          <div class="subsection" data-id="<?= $subsection['id'] ?>">

            <?php if ($subsection['id'] != -1) : ?>
              <input type="text" class="title fs-2" data-model="name" maxlength="100" value="<?= $subsection['name'] ?>" disabled />

              <div class="controls hstack gap-4">
                <i class="btn-edit fs-3 fa-solid fa-pencil"></i>
                <i class="btn-next fs-2 fa-regular fa-circle-down"></i>
                <i class="btn-prev fs-2 fa-regular fa-circle-up"></i>
                <i class="btn-del fs-2 fa-solid fa-times"></i>
              </div>
            <?php endif; ?>

            <div class="items">
              <?php foreach ($subsection['entries'] as $entry) : ?>
                <div id="menu-item-<?= $entry->getId(); ?>" class="menu-item" data-id="<?= $entry->getId() ?>" draggable="true">
                  <input class="data-input" type="text" data-model="title" value="<?= htmlspecialchars($entry->getProperty('title')) ?>" maxlength="100" disabled />
                  <textarea class="data-input" data-model="descr" disabled><?= htmlspecialchars($entry->getProperty('descr')) ?></textarea>
                  <div class="hstack price align-items-center">
                    <span>€</span>
                    <input class="data-input" type="text" data-model="price" value="<?= $entry->getProperty('price') ?>" disabled />
                  </div>
                  <div class="buttons hstack gap-2" draggable="false">
                    <button class="edit-btn btn btn-primary bold"><i class="fa-solid fa-pencil"></i></button>
                    <button class="delete-btn btn btn-danger bold"><i class="fa-solid fa-trash"></i></button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <hr>
    </div>
  <?php endforeach; ?>

  <div class="drop-indicator d-flex justify-content-center align-items-center" disabled>
    <span>Trascina qui un piatto per spostarlo</span>
  </div>
</div>

<div class="actions container-fluid">
  <form action="/api/category/create/" method="POST" id="category-form" class="menu-action" data-menu-action="new-category" disabled>
    <h2>Nuova categoria</h2>
    <div class="mb-3">
      <label class="form-label" for="name">Nome</label>
      <input class="form-control" type="text" name="name" id="name" title="Nome categoria" maxlength="100" required>
    </div>
    <button class="btn btn-primary" type="submit">Aggiungi</button>
  </form>
  <form action="/api/subcategory/create/" method="POST" id="subcategory-form" class="menu-action" data-menu-action="new-subcategory" disabled>
    <h2>Nuova sottocategoria</h2>
    <div class="mb-3">
      <label class="form-label" for="name">Nome</label>
      <input class="form-control" type="text" name="name" id="name" title="Nome sotto-categoria" maxlength="100" required>
    </div>
    <div class="mb-3">
      <label class="form-label" for="category_id">Categoria</label>
      <select class="form-select" name="category_id" id="category_id" required data-model="category" data-key="name">
        <option value="" selected disabled>Scegli una categoria...</option>
      </select>
    </div>
    <button class="btn btn-primary" type="submit">Aggiungi</button>
  </form>
  <form action="/api/entry/create/" method="POST" id="entry-form" class="menu-action" data-menu-action="new-entry" disabled>
    <h2>Nuovo articolo</h2>
    <div class="mb-3">
      <label class="form-label" for="title">Nome</label>
      <input class="form-control" type="text" name="title" id="title" title="Nome piatto" maxlength="100" required>
    </div>
    <div class="mb-3">
      <label class="form-label" for="descr">Descrizione</label>
      <textarea class="form-control" type="text" name="descr" id="descr" title="Descrizione piatto"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label" for="price">Prezzo</label>
      <input class="form-control" type="text" pattern="^\d+((,|\.)\d{1,2})?h?$" name="price" id="price" title="Prezzo" required>
    </div>
    <div class="mb-3">
      <label class="form-label" for="category_id">Categoria</label>
      <select class="form-select" name="category_id" id="category_id" data-model="category" data-key="name" required>
        <option value="" selected disabled>Scegli una categoria...</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label" for="subcategory_id">Sottocategoria</label>
      <select class="form-select" name="subcategory_id" id="subcategory_id" data-model="subcategory" data-key="name" data-reference="category_id">
        <option value="null" selected>Scegli una sottocategoria...</option>
      </select>
    </div>
    <button class="btn btn-primary" type="submit">Aggiungi</button>
  </form>
  <div class="action-menu hstack justify-content-center gap-3 my-4">
    <button type="button" class="btn btn-primary" data-menu-action="new-category"><i class="fa-solid fa-plus me-2"></i><span data-message="Nuova categoria">Nuova categoria</span></button>
    <button type="button" class="btn btn-primary" data-menu-action="new-subcategory"><i class="fa-solid fa-plus me-2"></i><span data-message="Nuova sottocategoria">Nuova sottocategoria</span></button>
    <button type="button" class="btn btn-primary" data-menu-action="new-entry"><i class="fa-solid fa-plus me-2"></i><span data-message="Nuovo articolo">Nuovo articolo</span></button>
  </div>
</div>

<script src="/js/admin.js"></script>