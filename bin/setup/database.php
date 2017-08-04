<?php

require dirname(__FILE__).'/../../../../autoload.php';

$install = new \craft\migrations\Install();
$install->username = 'craftadmin';
$install->password = 'password';
$install->email = 'admin@craftcms.com';
$install->site = '';
$install->safeUp();