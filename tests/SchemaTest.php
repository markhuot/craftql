<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use markhuot\CraftQL\Models\Token;
use markhuot\CraftQL\Services\GraphQLService;

/**
 * @covers Email
 */
final class SchemaTest extends TestCase
{
    static $service;
    static $schema;

    public static function setUpBeforeClass(): void
    {
        self::$service = new GraphQLService;
        self::$service->bootstrap();

        /** @var \markhuot\CraftQL\Builders\Schema schema */
        self::$schema = self::$service->getSchema(Token::admin());
    }

    protected function execute($input, $variables=[]) {
        return self::$service->execute(self::$schema, $input, $variables);
    }

    function testFullSchema() {
        $controlSchema = file_get_contents(__DIR__.'/../seeds/schema.graphql');

        /** @var \markhuot\CraftQL\Builders\Schema $schema */
        $schema = self::$schema;
        $schemaText = \GraphQL\Utils\SchemaPrinter::doPrint($schema);

        $this->assertEquals(preg_replace("#\r\n#", "\n", $controlSchema), preg_replace("#\r\n#", "\n", $schemaText));
    }
}
