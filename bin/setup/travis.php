<?php

// clone the craft repo
`rm -rf /tmp/craft3`;
`composer create-project craftcms/craft /tmp/craft3`;

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

// require craftql, through a symbolic link
`pushd /tmp/craft3 && composer require markhuot/craftql dev-master && composer require phpunit`;