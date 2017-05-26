<?php

namespace Craft;

class CraftQL_RequestService extends BaseApplicationComponent {

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

}
