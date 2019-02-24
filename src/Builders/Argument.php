<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class Argument extends BaseBuilder {

    use HasTypeAttribute;
    use HasDescriptionAttribute;
    use HasIsListAttribute;
    use HasNonNullAttribute;
    use HasOnSaveAttribute;

    private $config = null;

    function __construct(Request $request, string $name) {
        $this->request = $request;
        $this->name = $name;
    }

    function getConfig() {
        if ($this->config !== null) {
            return $this->config;
        }

        $type = $this->getTypeConfig();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        return $this->config = [
            'type' => $type,
            'description' => $this->getDescription(),
        ];
    }

}