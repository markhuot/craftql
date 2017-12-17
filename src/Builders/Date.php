<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\ContentField;

class Date extends Field {

    function getType() {
        return \markhuot\CraftQL\Types\Timestamp::type();
    }

    function getResolve() {
        return function ($root, $args, $context, $info) {
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

            if ($this->isNonNull() && !$date) {
                throw new Error("`{$info->fieldName}` is a required field but has no value");
            }

            if (!$date) {
                return null;
            }

            $date = $date->format($format);
            $cast = ($format === 'U') ? 'intval' : 'strval';
            return $cast($date);
        };
    }

}