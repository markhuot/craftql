<?php

namespace markhuot\CraftQL\Services;

use Craft;

class RequestService {

    private $schema;

    function input() {
        if (Craft::$app->request->isPost && $query=Craft::$app->request->post('query')) {
            return $query;
        }

        if (Craft::$app->request->isGet && $query=Craft::$app->request->get('query')) {
            return $query;
        }

        $data = Craft::$app->request->getRawBody();
        $data = json_decode($data, true);
        return @$data['query'];
    }

    function variables() {
        if (Craft::$app->request->isPost && $query=Craft::$app->request->post('variables')) {
            return $query;
        }

        if (Craft::$app->request->isGet && $query=Craft::$app->request->get('variables')) {
            return $query;
        }

        $data = Craft::$app->request->getRawBody();
        $data = json_decode($data, true);
        return @$data['variables'];
    }

    function isDebugging() {
        return !!Craft::$app->request->post('debug');
    }

}
