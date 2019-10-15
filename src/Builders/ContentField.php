<?php

namespace markhuot\CraftQL\Builders;

use craft\base\Field as CraftField;
use markhuot\CraftQL\Request;

class ContentField extends Field {

    protected $field;

    function __construct(Request $request, CraftField $field) {
        parent::__construct($request, $field->handle);
        $this->field = $field;
    }

    function getDescription() {
        return $this->description ?: $this->field->instructions;
    }

}
