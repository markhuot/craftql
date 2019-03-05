<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use markhuot\CraftQL\Models\Token;
use markhuot\CraftQL\Services\GraphQLService;

/**
 * @covers Email
 */
final class MutationTest extends TestCase
{
    static $service;
    static $schema;

    public static function setUpBeforeClass(): void
    {
        self::$service = new GraphQLService;
        self::$service->bootstrap();
        self::$schema = self::$service->getSchema(Token::admin());
    }

    protected function execute($input, $variables=[]) {
        return self::$service->execute(self::$schema, $input, $variables);
    }

    function testNothing() {
        $this->assertEquals(true, true);
    }
}
