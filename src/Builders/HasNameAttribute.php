<?php

namespace markhuot\CraftQL\Builders;

trait HasNameAttribute {

    /**
     * The name of our schema
     *
     * @var string
     */
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

}