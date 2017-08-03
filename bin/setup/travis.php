<?php

// add craftql to the deps as a symbolic link
$package = file_get_contents('/home/travis/build/craftcms/craft/composer.json');
$package = json_decode($package, true);
$package['repositories'] = @$package['repositories'] ?: [];
$package['repositories'][] = [
    "type" => "path",
    "url" => realpath(dirname(__FILE__)."/../../"),
];
$package['minimum-stability'] = 'dev';
file_put_contents('/home/travis/build/craftcms/craft/composer.json', json_encode($package));