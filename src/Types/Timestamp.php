<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Error\Error;
use GraphQL\Type\Definition\ScalarType;

class Timestamp extends ScalarType
{
    static $type;

    public $description = "The `Timestamp` scalar type represents a unix timestamp. The Timestamp type can be converted to a human friendly format with the `@date` directive.\r\n\r\n    {\r\n      entries {\r\n        dateCreated @date(as:\"F j, Y\")\r\n      }\r\n    }";

    static function type() {
        if (static::$type) {
            return static::$type;
        }

        return static::$type = new static;
    }

    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     * @return string
     */
    public function serialize($value)
    {
        // Assuming internal representation of email is always correct:
        return $value;

        // If it might be incorrect and you want to make sure that only correct values are included
        // in response - use following line instead:
        // if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        //     throw new InvariantViolation("Could not serialize following value as email: " . Utils::printSafe($value));
        // }
        // return $this->parseValue($value);
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {
        // if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        //     throw new Error("Cannot represent following value as email: " . Utils::printSafeJson($value));
        // }
        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * E.g.
     * {
     *   user(email: "user@example.com")
     * }
     *
     * @param \GraphQL\Language\AST\Node $valueNode
     * @param array|null $variables
     * @return string
     * @throws Error
     */
    public function parseLiteral($valueNode, array $variables = null)
    {
        // Note: throwing GraphQL\Error\Error vs \UnexpectedValueException to benefit from GraphQL
        // error location in query:
        // if (!$valueNode instanceof StringValueNode) {
        //     throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        // }
        // if (!filter_var($valueNode->value, FILTER_VALIDATE_EMAIL)) {
        //     throw new Error("Not a valid email", [$valueNode]);
        // }
        return $valueNode->value;
    }
}
