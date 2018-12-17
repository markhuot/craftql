<?php

namespace markhuot\CraftQL\Parser;

trait ParsePropertiesTrait {

    function parseProperties($properties) {
        foreach ($properties as $property) {
            $this->parseProperty($property);
        }
    }

    /**
     * @param $property \ReflectionProperty
     */
    function parseProperty($property) {
        if (!$property->isPublic()) {
            return;
        }

        list($type, $isList) = $this->getTypeFromDoc($property);

        $field = $this->type->addField($property->getName())->type($type)->lists($isList);

        if ($this->getNonNullFromdoc($property)) {
            $field->nonNull();
        }
    }

}