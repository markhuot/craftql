<?php

// add craftql to the deps as a symbolic link
$package = file_get_contents('/tmp/craft3/composer.json');
$package = json_decode($package, true);
$package['repositories'] = @$package['repositories'] ?: [];
$package['repositories'][] = [
    "type" => "path",
    "url" => realpath(dirname(__FILE__)."/../../"),
];
$package['minimum-stability'] = 'dev';
file_put_contents('/tmp/craft3/composer.json', json_encode($package));