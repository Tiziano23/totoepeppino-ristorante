<?php

use Steampixel\Route;
use Steampixel\Component;

Component::addFolder('components/');

function page_main()
{
  return Component::create('layouts/default')->assign([
    'title' => 'Totò e Peppino - Menù',
    'page' => 'main'
  ]);
}
function page_login()
{
  return Component::create('layouts/default')->assign([
    'title' => 'Totò e Peppino - Login',
    'page' => 'login'
  ]);
}
function page_admin()
{
  return Component::create('layouts/dashboard')->assign([
    'title' => 'Totò e Peppino - Pannello di controllo',
    'page' => 'admin'
  ]);
}
function page_account()
{
  return Component::create('layouts/dashboard')->assign([
    'title' => 'Totò e Peppino - Gestione account',
    'page' => 'account'
  ]);
}

Route::add('/', function () {
  return page_main();
});
Route::add('/admin', function () {
  if (!authenticate()) redirect('/login');
  return page_admin();
});

Route::add('/login', function () {
  if (authenticate()) redirect('/admin');
  return page_login();
}, 'get');
Route::add('/login', function () {
  if (login($_POST['username'], $_POST['password'])) redirect('/admin');
  return page_login()->assign('content', Component::create('pages/login')->assign('error', 'Username o password non validi.'));
}, 'post');
Route::add('/logout', function () {
  logout();
  redirect('/login');
}, ['get', 'post']);

Route::add('/account', function () {
  if (!authenticate()) redirect('/login');
  return page_account();
}, 'get');
Route::add('/account/update', function () {
  if (!authenticate()) redirect('/login');
  if (!isset($_POST['username']) || !isset($_POST['old_password']) || !isset($_POST['new_password'])) bad_request();

  $username = $_POST['username'];
  if ($username == null) return page_account()->assign('content', Component::create('pages/account')->assign('update_error', 'Username non valido.'));
  if (!authenticate($username, $_POST['old_password'])) return page_account()->assign('content', Component::create('pages/account')->assign('update_error', 'Password non corretta.'));

  update_user($username, $_POST['new_password']);
  return page_account()->assign('content', Component::create('pages/account')->assign('update_msg', 'Operazione eseguita con successo.'));
}, 'post');
Route::add('/account/register', function () {
  if (!authenticate()) redirect('/login');
  if (!isset($_POST['username']) || !isset($_POST['password'])) bad_request();

  $res = register_user($_POST['username'], $_POST['password']);
  if ($res == false) return page_account()->assign('content', Component::create('pages/account')->assign('register_error', 'Utente già registrato.'));
  return page_account()->assign('content', Component::create('pages/account')->assign('register_msg', 'Operazione eseguita con successo.'));
}, 'post');
Route::add('/account/([a-z]*)/delete', function ($username) {
  if (!delete_user($username)) server_error();
  redirect('/account');
});

// ====================================================

Route::add('/api/category', function () {
  restrict_access();

  $data = MenuCategory::fetchAll();
  header('Content-Type: application/json');
  return json_encode($data);
}, 'get');
Route::add('/api/category/create', function () {
  restrict_access();

  MenuCategory::create($_POST['name']);

  redirect('/admin');
}, 'post');
Route::add('/api/category/sort', function () {
  global $db;

  restrict_access();
  if (!isset($_POST['order'])) bad_request();

  $orderData = json_decode($_POST['order']);
  $orderStrings = [];
  $whereClauses = [];
  foreach ($orderData as $id => $order) {
    $orderStrings[] = "WHEN `id` = $id THEN $order";
    $whereClauses[] = "`id` = $id";
  }
  $res = $db->query(sprintf(
    'UPDATE `%s` SET `order`=CASE %s END WHERE %s',
    MenuCategory::TABLE_NAME,
    implode(' ', $orderStrings),
    implode(' OR ', $whereClauses)
  ));

  if ($res == false) server_error("Database error occurred! $db->error");

  redirect('/admin');
}, 'post');
Route::add('/api/category/([0-9]*)/update', function ($id) {
  restrict_access();

  $category = MenuCategory::fetchId($id);
  if ($category == null) not_found();

  if (isset($_POST['name'])) $category->setProperty('name', $_POST['name']);
  if (isset($_POST['color'])) $category->setProperty('color', $_POST['color']);
  if (isset($_POST['order'])) $category->setProperty('order', $_POST['order']);
  $category->sync();

  redirect('/admin');
}, 'post');
Route::add('/api/category/([0-9]*)/delete', function ($id) {
  restrict_access();

  $category = MenuCategory::fetchId($id);
  if ($category == null) not_found();

  $category->delete();

  redirect('/admin');
}, 'get');

// ====================================================

Route::add('/api/subcategory', function () {
  restrict_access();

  $data = [];
  if (isset($_GET['category_id'])) {
    $category = MenuCategory::fetchId($_GET['category_id']);
    if ($category != null) $data = MenuSubCategory::fetchByCategory($category);
  } else $data = MenuSubCategory::fetchAll();

  header('Content-Type: application/json');
  return json_encode($data);
}, 'get');
Route::add('/api/subcategory/create', function () {
  restrict_access();

  MenuSubCategory::create($_POST['name'], $_POST['category_id']);

  redirect('/admin');
}, 'post');
Route::add('/api/subcategory/sort', function () {
  global $db;

  restrict_access();

  if (!isset($_POST['order'])) bad_request();

  $orderData = json_decode($_POST['order']);
  $orderStrings = [];
  $whereClauses = [];
  foreach ($orderData as $id => $order) {
    $orderStrings[] = "WHEN `id` = $id THEN $order";
    $whereClauses[] = "`id` = $id";
  }
  $res = $db->query(sprintf(
    'UPDATE `%s` SET `order`=CASE %s END WHERE %s',
    MenuSubCategory::TABLE_NAME,
    implode(' ', $orderStrings),
    implode(' OR ', $whereClauses)
  ));

  if ($res == false) server_error("Database error occurred! $db->error");

  redirect('/admin');
}, 'post');
Route::add('/api/subcategory/([0-9]*)/update', function ($id) {
  restrict_access();

  $subcategory = MenuSubCategory::fetchId($id);

  if ($subcategory == null) not_found();

  if (isset($_POST['name'])) $subcategory->setProperty('name', $_POST['name']);
  if (isset($_POST['order'])) $subcategory->setProperty('order', $_POST['order']);
  if (isset($_POST['category_id'])) $subcategory->setProperty('category_id', $_POST['category_id']);
  $subcategory->sync();

  redirect('/admin');
}, 'post');
Route::add('/api/subcategory/([0-9]*)/delete', function ($id) {
  restrict_access();

  $subcategory = MenuSubCategory::fetchId($id);
  if ($subcategory == null) not_found();

  $subcategory->delete();

  redirect('/admin');
}, 'get');

// ====================================================

Route::add('/api/entry', function () {
  restrict_access();

  $data = MenuEntry::fetchAll();
  header('Content-Type: application/json');
  return json_encode($data);
}, 'get');
Route::add('/api/entry/create', function () {
  restrict_access();

  $descr = isset($_POST['descr']) ? $_POST['descr'] : "";
  $sub_id = isset($_POST['subcategory_id']) && $_POST['subcategory_id'] != "null" ? $_POST['subcategory_id'] : null;
  MenuEntry::create($_POST['title'], $descr, $_POST['price'], $_POST['category_id'], $sub_id);

  redirect('/admin');
}, 'post');
Route::add('/api/entry/sort', function () {
  global $db;

  restrict_access();

  if (!isset($_POST['order'])) bad_request();

  $orderData = json_decode($_POST['order']);
  $orderStrings = [];
  $whereClauses = [];
  foreach ($orderData as $id => $order) {
    $orderStrings[] = "WHEN `id` = $id THEN $order";
    $whereClauses[] = "`id` = $id";
  }
  $res = $db->query(sprintf(
    'UPDATE `%s` SET `order`=CASE %s END WHERE %s',
    MenuEntry::TABLE_NAME,
    implode(' ', $orderStrings),
    implode(' OR ', $whereClauses)
  ));

  if ($res == false) server_error("Database error occurred! $db->error");

  redirect('/admin');
}, 'post');
Route::add('/api/entry/([0-9]*)/update', function ($id) {
  restrict_access();

  $entry = MenuEntry::fetchId($id);
  if ($entry == null) not_found();

  if (isset($_POST['title'])) $entry->setProperty('title', $_POST['title']);
  if (isset($_POST['descr'])) $entry->setProperty('descr', $_POST['descr']);
  if (isset($_POST['price'])) $entry->setProperty('price', $_POST['price']);
  if (isset($_POST['order'])) $entry->setProperty('order', $_POST['order']);
  if (isset($_POST['category_id'])) $entry->setProperty('category_id', $_POST['category_id']);
  if (isset($_POST['subcategory_id'])) $entry->setProperty('subcategory_id', $_POST['subcategory_id']);
  $entry->sync();

  redirect('/admin');
}, 'post');
Route::add('/api/entry/([0-9]*)/delete', function ($id) {
  restrict_access();

  $entry = MenuEntry::fetchId($id);
  if ($entry == null) not_found();
  $entry->delete();

  redirect('/admin');
}, 'get');

// ====================================================

Route::add('/api/user', function () {
  restrict_access();

  $data = get_all_users_data();
  header('Content-Type: application/json');
  return json_encode($data);
}, 'get');


// Run the router
Route::run('/');


function error_code($code)
{
  http_response_code($code);
  die();
}
function bad_request()
{
  http_response_code(400);
  die();
}
function not_found()
{
  http_response_code(404);
  die();
}
function server_error($msg = null)
{
  http_response_code(500);
  echo $msg;
  die();
}

function restrict_access()
{
  if (!authenticate()) {
    http_response_code(401);
    die();
  }
}

function redirect($url)
{
  header("Location: $url");
  http_response_code(301);
  die();
}
