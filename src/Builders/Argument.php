<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\FieldArgument;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class Argument extends BaseBuilder {

    use HasNameAttribute;
    use HasTypeAttribute;
    use HasDescriptionAttribute;
    use HasIsListAttribute;
    use HasNonNullAttribute;
    use HasOnSaveAttribute;
    use HasDefaultValueAttribute;

    /**
     * A cache of the generated config array
     *
     * @var array
     */
    private $config = null;

    /**
     * A cache of the config used in a directive
     *
     * @var FieldArgument
     */
    private $directiveConfig = null;

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

        $this->config = [
            'type' => $type,
            'description' => $this->getDescription(),
        ];

        if (($defaultValue=$this->getDefaultValue()) !== '__empty__') {
            $this->config['defaultValue'] = $defaultValue;
        }

        return $this->config;
    }

    function getDirectiveConfig() {
        if ($this->directiveConfig !== null) {
            return $this->directiveConfig;
        }

        $config = array_merge([
            'name' => $this->getName(),
        ], $this->getConfig());

        if (($defaultValue=$this->getDefaultValue()) !== '__empty__') {
            $config['defaultValue'] = $defaultValue;
        }

        return $this->directiveConfig = new FieldArgument($config);
    }

}