<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class Object extends Field {

    function boot() {
        $this->type = new Schema($this->request);
    }

    function config($callback): self {
        if (is_callable($callback)) {
            $callback($this->type);
        }

        if (is_a($callback, Schema::class)) {
            $this->type = $callback;
        }

        return $this;
    }

}