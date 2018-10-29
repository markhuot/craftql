<?php

namespace markhuot\CraftQL\Builder2;

use craft\base\Component;
use Doctrine\Common\Annotations\AnnotationReader;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Annotations\CraftQL;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class GraphQlObject extends Component {

    /** @var string */
    protected $name;

    /**
     * Set the name of the schema/object
     *
     * @param string $name
     * @return self
     */
    function name(string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the name of the schema/object
     *
     * @return string
     */
    function getName(): string {
        if ($this->name === null) {
            $reflect = new \ReflectionClass(static::class);
            return $this->name = $reflect->getShortName();
        }

        return $this->name;
    }

    /**
     * @return array
     */
    function getGraphQlFieldConfig() {
        $fields = [];

        // $reflect = new \ReflectionClass(static::class);
        // $property = $reflect->getProperty('helloWorld');
        // $reader = new AnnotationReader;
        // $myAnnotation = $reader->getPropertyAnnotation($property, CraftQL::class);
        // var_dump($myAnnotation);
        // var_dump('done');
        // die;

        // $reader = new AnnotationReader;
        $reflect = new \ReflectionClass(static::class);
        foreach ($reflect->getProperties() as $prop) {
            // var_dump($prop->getDocComment());
            // die;
            // $foo = $reader->getPropertyAnnotation($prop, \markhuot\CraftQL\Annotations\CraftQL::class);
            // var_dump($foo);
            // die;

            // $type = 'string';
            // /** @var Var_ $varTag */
            // foreach ($docs->getTagsByName('var') as $varTag) {
            //     if ($prop->getName() == 'entries') {
            //         var_dump($varTag->getType());
            //         die;
            //     }
            // }

            if (!preg_match('/@CraftQL/', $prop->getDocComment())) {
                continue;
            }

            // $type = 'string';
            // preg_match ('/^\s*\*\s+@var\s+(.*)$/m', $prop->getDocComment(), $matches);
            // var_dump($matches);
            // die;
            $type = Type::string();
            if ($prop->getName() == 'color') {
                // $reflect = new \ReflectionClass(ColorData::class);
            }

            $name = $prop->getName();
            $fields[$name] = [
                'type' => Type::string(),
                'description' => 'foo',
            ];
        }

        return $fields;
    }

    /**
     * @return array
     */
    function getGraphQlConfig() {
        $className = $this->getGraphQlType();
        return new $className([
            'name' => $this->getName(),
            'description' => '@resolve '.get_class($this),
            'fields' => function () {
                return $this->getGraphQlFieldConfig();
            },
        ]);
    }

    function getGraphQlType() {
        return ObjectType::class;
    }

}