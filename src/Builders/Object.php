<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\ContentField;

class Object extends Field {

    function boot() {
        $this->type = new Schema($this->request);
    }

    function config($callback): self {
        $callback($this->type);
        return $this;
    }

}