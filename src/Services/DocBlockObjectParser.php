<?php

namespace markhuot\CraftQL\Services;

use craft\fields\data\ColorData;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Annotations\CraftQL;
use markhuot\CraftQL\Builder2\Schema;
use markhuot\CraftQL\Types\EntryInterface;

class DocBlockObjectParser {

    private $schema;
    private $fields = [];

    function __construct(Schema $schema) {
        $this->schema = $schema;
    }

    function parse($class) {
        $reflect = new \ReflectionClass($class);

        $this->parseProperties($reflect->getProperties());
        $this->parseMethods($reflect->getMethods());

        $type = ObjectType::class;
        $doc = $reflect->getDocComment();
        if (preg_match('/@craftql-type interface/', $doc)) {
            $type = InterfaceType::class;
        }

        return new $type([
            'name' => $reflect->getShortName(),
            'fields' => $this->fields,
        ]);
    }

    /**
     * @param $properties \ReflectionProperty[]
     */
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

        $this->fields[$property->getName()] = [
            'name' => $property->getName(),
            'type' => Type::string(),
        ];
    }

    /**
     * @param $methods \ReflectionMethod[]
     */
    function parseMethods($methods) {
        foreach ($methods as $method) {
            $this->parseMethod($method);
        }
    }

    /**
     * @param $method \ReflectionMethod
     */
    function parseMethod($method) {
        // if ($method->getName() == 'getEntries') {
        //     AnnotationRegistry::registerLoader('class_exists');
        //     $reader = new AnnotationReader;
        //     $annotation = $reader->getMethodAnnotations($method);
        //     var_dump($annotation);
        //     die;
        // }

        if (preg_match('/^get([A-Z][a-z]*)$/', $method->getName(), $matches)) {
            $name = lcfirst($matches[1]);
            $type = Type::string();
            if ($method->getName() == 'getColor') { $type = $this->schema->getType(ColorData::class); }
            if ($method->getName() == 'getEntries') { $type = Type::listOf($this->schema->getType(EntryInterface::class)); }
            $this->fields[] = [
                'name' => $name,
                'type' => $type,
            ];
        }
    }

}