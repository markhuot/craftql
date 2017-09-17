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
        self::$service = new GraphQLService(
            new \markhuot\CraftQL\Repositories\Volumes,
            new \markhuot\CraftQL\Repositories\CategoryGroup,
            new \markhuot\CraftQL\Repositories\TagGroup,
            new \markhuot\CraftQL\Repositories\EntryType,
            new \markhuot\CraftQL\Repositories\Section
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

    public function testRichTextMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Text Test", body:"page one<!--pagebreak-->page two") { id, title, body, pageOne:body(page:1) } }';

        $result = $this->execute($input);

        $this->assertEquals('page one<!--pagebreak-->page two', @$result['data']['story']['body']);
        $this->assertEquals('page one', @$result['data']['story']['pageOne']);
    }

    public function testDateMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Date Test", releaseDate:'.date('U', strtotime('2017-02-04 03:12:18')).') { id, releaseDateTimestamp: releaseDate, releaseDateFormatted: releaseDate @date(as:"Y-m-d H:i:s") } }';

        $result = $this->execute($input);

        $this->assertEquals(date('U', strtotime('2017-02-04 03:12:18')), @$result['data']['story']['releaseDateTimestamp']);
        $this->assertEquals('2017-02-04 03:12:18', @$result['data']['story']['releaseDateFormatted']);
    }

    public function testLightswitchMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Lightswitch Test", promoted:true) { id, promoted } }';

        $result = $this->execute($input);

        $this->assertTrue(@$result['data']['story']['promoted']);
    }

    public function testCheckboxMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Checkbox Test", socialLinks:[fb, tw]) { id, socialLinks } }';

        $result = $this->execute($input);

        $this->assertEquals('["fb","tw"]', json_encode(@$result['data']['story']['socialLinks']));
    }

    public function testDropdownMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Dropdown Test", language:cn) { id, language } }';

        $result = $this->execute($input);

        $this->assertEquals('cn', @$result['data']['story']['language']);
    }

    public function testEntriesMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Entries Test One", body:"My first test") { id } }';
        $first = $this->execute($input);
        $firstId = @$first['data']['story']['id'];
        $this->assertGreaterThan(0, $firstId);

        $input = 'mutation { story: upsertStories(title:"Entries Test Two", relatedEntry:['.$firstId.']) { id, relatedEntry { id } } }';
        $second = $this->execute($input);
        $secondId = @$second['data']['story']['id'];
        $this->assertEquals($firstId, @$second['data']['story']['relatedEntry'][0]['id']);

        $input = 'query { entry(relatedTo:[{element:'.$firstId.'}]) { id } }';
        $third = $this->execute($input);
        $this->assertEquals($secondId, @$third['data']['entry']['id']);

        $input = 'query { entriesConnection(id:'.$firstId.') { edges { relatedTo { entries { id } } } } }';
        $fourth = $this->execute($input);
        $this->assertEquals($secondId, @$fourth['data']['entriesConnection']['edges'][0]['relatedTo']['entries'][0]['id']);
    }

    public function testMultiSelectMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Multi Select Test", socialLinksTwo:[fb, tw]) { id, socialLinksTwo } }';

        $result = $this->execute($input);

        $this->assertEquals('["fb","tw"]', json_encode(@$result['data']['story']['socialLinksTwo']));
    }

    public function testPositionSelectMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Position Select Test", heroImagePosition:right) { id, heroImagePosition } }';

        $result = $this->execute($input);

        $this->assertEquals('right', @$result['data']['story']['heroImagePosition']);
    }

    public function testPositionSelectFailureMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"FOOPosition Select Failure Test", heroImagePosition:left) { id, heroImagePosition } }';

        $result = $this->execute($input);

        $this->assertEquals("Argument \"heroImagePosition\" has invalid value left.\nExpected type \"HeroImagePositionEnum\", found left.", @$result['errors'][0]['message']);
    }
}
