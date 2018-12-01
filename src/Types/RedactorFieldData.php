<?php

namespace markhuot\CraftQL\Types;

class RedactorFieldData extends ProxyObject {

    /**
     * @var int
     */
    public $totalPages;

    /**
     * @var string
     */
    function getCraftQLContent($request, $root, $args, $context, $info) {
        if (!empty($args['page'])) {
            return (string)$this->source->getPage($args['page']);
        }

        return (string)$this->source;
    }

}