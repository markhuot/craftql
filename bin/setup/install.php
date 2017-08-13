<?php

include getcwd().'/vendor/autoload.php';

if (file_exists(getcwd().'/.env')) {
    $dotenv = new Dotenv\Dotenv(getcwd());
    $dotenv->load();
}

$app = include getcwd().'/vendor/craftcms/cms/bootstrap/console.php';

$app->plugins->installPlugin('craftql');