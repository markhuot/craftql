<?php
/**
 * Craft console bootstrap file
 */

// Project root path
$root = getcwd();

// Composer autoloader
require_once $root.'/vendor/autoload.php';

// dotenv?
if (file_exists($root.'/.env')) {
    $dotenv = new Dotenv\Dotenv($root);
    $dotenv->load();
}

// Craft
define('CRAFT_BASE_PATH', $root);
return require $root.'/vendor/craftcms/cms/bootstrap/console.php';