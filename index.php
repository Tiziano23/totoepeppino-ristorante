<?php
set_include_path(__DIR__ . '/');

// Development
// ini_set('html_errors', false);
// ini_set('display_errors', true);
// ini_set('display_startup_errors', true);
// error_reporting(E_ALL);

// Production
ini_set("log_errors", true);
ini_set("display_errors", false);
ini_set("display_startup_errors", false);
error_reporting(E_ALL);

// ====================================================

use voku\db\DB;
use voku\helper\Session2DB;

require_once './vendor/autoload.php';

require_once "./database.php";
require_once "./auth.php";

// Initialize database
$db = DB::getInstance('localhost', 'ctotoepm_admin', 'MQk$9)?[F##z', 'ctotoepm_menu')->getLink();

$db->query("CREATE TABLE IF NOT EXISTS `categories` (
    `id`    INT          NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name`  VARCHAR(100) NOT NULL,
    `color` VARCHAR(6)   NOT NULL DEFAULT 'DDC091',
    `order` INT          DEFAULT NULL
)");
$db->query("CREATE TABLE IF NOT EXISTS `subcategories` (
    `id`          INT          NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name`        VARCHAR(100) NOT NULL,
    `category_id` INT          NOT NULL,
    `order`       INT          DEFAULT NULL,

    FOREIGN KEY (category_id) REFERENCES `categories`(id) ON DELETE CASCADE
)");
$db->query("CREATE TABLE IF NOT EXISTS `entries` (
    `id`             INT          NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `title`          VARCHAR(100) NOT NULL,
    `descr`          TEXT         NOT NULL,
    `price`          FLOAT        NOT NULL,
    `category_id`    INT          NOT NULL,
    `subcategory_id` INT,
    `order`          INT          DEFAULT NULL,

    FOREIGN KEY (category_id)    REFERENCES `categories`(id) ON DELETE CASCADE,
    FOREIGN KEY (subcategory_id) REFERENCES `subcategories`(id) ON DELETE SET NULL
)");
$db->query("CREATE TABLE IF NOT EXISTS `users` (
    `username`      VARCHAR(50)  NOT NULL PRIMARY KEY,
    `password_hash` VARCHAR(255) NOT NULL
)");
$db->query('INSERT IGNORE INTO `users` (`username`, `password_hash`) VALUES ("admin", "$2y$10$jlIF1mBomKRedYDo2Oipee7PYC2wupbJaE/hd5krRkWDKKteG1rx.")');
$db->query('CREATE TABLE IF NOT EXISTS `session_data` (
    `session_id`     VARCHAR(128) NOT NULL DEFAULT "" PRIMARY KEY,
    `hash`           VARCHAR(128) NOT NULL DEFAULT "",
    `session_data`   BLOB         NOT NULL,
    `session_expire` INT(11)      NOT NULL DEFAULT "0",
    INDEX hash (`hash`),
    INDEX session_expire (`session_expire`),
    INDEX select_helper_index (`session_id`, `hash`, `session_expire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;');

// Initialize Session2DB
new Session2DB();

if (session_status() === PHP_SESSION_NONE) session_start();

require_once "./routes.php";
