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
        self::$service = new GraphQLService(
            new \markhuot\CraftQL\Repositories\Volumes,
            new \markhuot\CraftQL\Repositories\CategoryGroup,
            new \markhuot\CraftQL\Repositories\TagGroup,
            new \markhuot\CraftQL\Repositories\EntryType,
            new \markhuot\CraftQL\Repositories\Section,
            new \markhuot\CraftQL\Repositories\Globals,
            new \markhuot\CraftQL\Repositories\Site
        );
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

        $this->assertEquals($controlSchema, $schemaText);
    }
}
