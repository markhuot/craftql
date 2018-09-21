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
    use HasOverwriteAttribute;

    function __construct(Request $request, string $name) {
        $this->request = $request;
        $this->name = $name;

        $this->bootTraits();
    }

    /**
     * @TODO make dynamic
     */
    function bootTraits() {
        $this->bootHasOverwriteAttribute();
    }

    function getConfig() {
        $type = $this->getTypeConfig();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        return [
            'type' => $type,
            'description' => $this->getDescription(),
        ];
    }

}