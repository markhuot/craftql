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

    private $configCache;

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
        if (!empty($this->configCache)) {
            return $this->configCache;
        }

//        \Yii::beginProfile($this->getName(), 'argumentGetConfig');
        $type = $this->getTypeConfig();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        $foo = [
            'type' => $type,
            'description' => $this->getDescription(),
        ];

//        \Yii::endProfile($this->getName(), 'argumentGetConfig');
        return $this->configCache = $foo;
    }

}