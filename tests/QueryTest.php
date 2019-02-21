<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use markhuot\CraftQL\Models\Token;
use markhuot\CraftQL\Services\GraphQLService;

/**
 * @covers Email
 */
final class QueryTest extends TestCase
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
        self::$schema = self::$service->getSchema(Token::admin());
    }

    protected function execute($input, $variables=[]) {
        return self::$service->execute(self::$schema, $input, $variables);
    }

    public function testSimpleQuery(): void
    {
        $input = '{ helloWorld }';

        $result = $this->execute($input);

        $this->assertEquals('Welcome to GraphQL! You now have a fully functional GraphQL endpoint.', @$result['data']['helloWorld']);
    }
}
