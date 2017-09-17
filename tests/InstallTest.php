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

}
