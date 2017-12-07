<?php

namespace markhuot\CraftQL\GraphQLFields\General;

use GraphQL\Error\Error;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;

class Date extends BaseField {

    protected $handle;
    protected $required = false;

    function __construct($request, $required=false) {
        parent::__construct($request);

        $this->required = $required;
    }

    function getType() {
        $type = \markhuot\CraftQL\Types\Timestamp::type();

        return $this->required ? Type::nonNull($type) : $type;
    }

    function getResolve($root, $args, $context, $info) {
        $format = 'U';

        if (isset($info->fieldNodes[0]->directives[0])) {
            $directive = $info->fieldNodes[0]->directives[0];
            if ($directive->arguments) {
            foreach ($directive->arguments as $arg) {
                $format = $arg->value->value;
            }
            }
        }

        $date = $root->{$info->fieldName};

        if ($this->required && !$date) {
            throw new Error("`{$info->fieldName}` is a required field but has no value");
        }

        if (!$date) {
            return null;
        }

        $date = $date->format($format);
        $cast = ($format === 'U') ? 'intval' : 'strval';
        return $cast($date);
    }

}