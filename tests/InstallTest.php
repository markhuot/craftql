<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use markhuot\CraftQL\Models\Token;
use markhuot\CraftQL\Services\GraphQLService;

/**
 * @covers Email
 */
final class InstallTest extends TestCase
{
    public function testPhpUnitIsWorking(): void
    {
        $this->assertEquals(1, 1);
    }

    public function testCraftHasDatabase(): void
    {
        $this->assertTrue(Craft::$app->getDb()->getIsActive());
    }

    public function testCraftqlIsInstalled(): void
    {
        $this->assertNotNull(Craft::$app->plugins->getPlugin('craftql'));
    }

    public function testSimpleQuery(): void
    {
        $input = '{ helloWorld }';
        
        $token = Token::admin();
        $service = new GraphQLService(
            new \markhuot\CraftQL\Repositories\Volumes,
            new \markhuot\CraftQL\Repositories\CategoryGroup,
            new \markhuot\CraftQL\Repositories\TagGroup,
            new \markhuot\CraftQL\Repositories\EntryType,
            new \markhuot\CraftQL\Repositories\Section
        );
        $service->bootstrap();
        $schema = $service->getSchema($token);
        $result = $service->execute($schema, $input, []);
        $this->assertEquals('Welcome to GraphQL! You now have a fully functional GraphQL endpoint.', @$result['data']['helloWorld']);
    }

}
