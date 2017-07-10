<?php

namespace markhuot\CraftQL\Services;

class RequestService {

    private $schema;

    function input() {
        if (!empty($_POST['query'])) {
            return $_POST['query'];
        }

        if (!empty($_GET['query'])) {
            return $_GET['query'];
        }

        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        return @$data['query'];
    }

    function variables() {
        if (!empty($_POST['variables'])) {
            return $_POST['variables'];
        }

        if (!empty($_GET['variables'])) {
            return $_GET['variables'];
        }

        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        return @$data['variables'];
    }

}
