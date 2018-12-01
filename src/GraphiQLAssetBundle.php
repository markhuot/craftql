<?php

namespace markhuot\CraftQL;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class GraphiQLAssetBundle extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@vendor/markhuot/craftql/src/resources';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'es6-promise/4.0.5/es6-promise.auto.min.js',
            'fetch/0.9.0/fetch.min.js',
            'react/15.4.2/react.min.js',
            'react/15.4.2/react-dom.min.js',
            'ajax/libs/graphiql/0.12.0/graphiql.js',
        ];

        $this->css = [
            'ajax/libs/graphiql/0.12.0/graphiql.min.css',
        ];

        parent::init();
    }
}