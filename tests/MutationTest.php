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
            new \markhuot\CraftQL\Repositories\Section,
            new \markhuot\CraftQL\Repositories\Globals
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

    public function testPlainTextMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Text Test'.date('U').'", body:"text in the body") { id, title, body } }';

        $result = $this->execute($input);

        $this->assertEquals('text in the body', @$result['data']['story']['body']);
    }

    public function testDateMutation(): void
    {
        $originalDate = new DateTime('2017-02-04 03:12:18', new DateTimeZone('UTC'));
        $input = 'mutation { story: upsertStories(title:"Date Test'.date('U').'", releaseDate:'.$originalDate->getTimestamp().') { id, releaseDateTimestamp: releaseDate, releaseDateFormatted: releaseDate @date(as:"Y-m-d H:i:s"), releaseDateLa: releaseDate @date(as:"Y-m-d H:i:s", timezone: "America/Los_Angeles") } }';

        $result = $this->execute($input);

        $utcDateTime = new DateTime('2017-02-04 03:12:18', new DateTimeZone('UTC'));
        $this->assertEquals($utcDateTime->format('U'), @$result['data']['story']['releaseDateTimestamp']);

        $this->assertEquals('2017-02-04 03:12:18', @$result['data']['story']['releaseDateFormatted']);

        $laTimestamp = (clone $utcDateTime)
            ->setTimezone(new DateTimeZone('America/Los_Angeles'))
            ->format('Y-m-d H:i:s');

        $this->assertEquals($laTimestamp, @$result['data']['story']['releaseDateLa']);
    }

    public function testLightswitchMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Lightswitch Test'.date('U').'", promoted:true) { id, promoted } }';

        $result = $this->execute($input);

        $this->assertTrue(@$result['data']['story']['promoted']);
    }

    public function testCheckboxMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Checkbox Test'.date('U').'", socialLinks:[fb, tw]) { id, socialLinks } }';

        $result = $this->execute($input);

        $this->assertEquals('["fb","tw"]', json_encode(@$result['data']['story']['socialLinks']));
    }

    public function testDropdownMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Dropdown Test'.date('U').'", language:cn) { id, language } }';

        $result = $this->execute($input);

        $this->assertEquals('cn', @$result['data']['story']['language']);
    }

    public function testEntriesMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Entries Test One'.date('U').'", body:"My first test") { id } }';
        $first = $this->execute($input);
        $firstId = @$first['data']['story']['id'];
        $this->assertGreaterThan(0, $firstId);

        $input = 'mutation { story: upsertStories(title:"Entries Test Two'.date('U').'", relatedEntry:['.$firstId.']) { id, relatedEntry { id } } }';
        $second = $this->execute($input);
        $secondId = @$second['data']['story']['id'];
        $this->assertEquals(1, count(@$second['data']['story']['relatedEntry']), 'Entries field behavior is not properly limiting to the field.');
        $this->assertEquals($firstId, @$second['data']['story']['relatedEntry'][0]['id'], 'Entries field behavior is not returning the correct entries.');

        $input = 'query { entry(relatedTo:[{element:'.$firstId.'}]) { id } }';
        $third = $this->execute($input);
        $this->assertEquals($secondId, @$third['data']['entry']['id']);

        $input = 'query { entriesConnection(id:'.$firstId.') { edges { relatedEntries { entries { id } } } } }';
        $fourth = $this->execute($input);
        $this->assertEquals($secondId, @$fourth['data']['entriesConnection']['edges'][0]['relatedEntries']['entries'][0]['id']);
    }

    public function testMultiSelectMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Multi Select Test'.date('U').'", socialLinksTwo:[fb, tw]) { id, socialLinksTwo } }';

        $result = $this->execute($input);

        $this->assertEquals('["fb","tw"]', json_encode(@$result['data']['story']['socialLinksTwo']));
    }

    public function testCategoriesMutation(): void
    {
        $hash = date('U');
        $input = 'mutation { story: upsertStories(title:"Category Test'.date('U').'", storyTypes:[{title:"foo bar", slug:"foo-bar'.$hash.'"}]) { id, storyTypes { id, title, slug } } }';

        $result = $this->execute($input);

        $this->assertEquals('foo-bar'.$hash, @$result['data']['story']['storyTypes'][0]['slug']);
    }

    // public function testPositionSelectMutation(): void
    // {
    //     $input = 'mutation { story: upsertStories(title:"Position Select Test'.date('U').'", heroImagePosition:right) { id, heroImagePosition } }';

    //     $result = $this->execute($input);

    //     $this->assertEquals('right', @$result['data']['story']['heroImagePosition']);
    // }

    // public function testPositionSelectFailureMutation(): void
    // {
    //     $input = 'mutation { story: upsertStories(title:"Position Select Failure Test'.date('U').'", heroImagePosition:left) { id, heroImagePosition } }';

    //     $result = $this->execute($input);

    //     $this->assertEquals("Argument \"heroImagePosition\" has invalid value left.\nExpected type \"HeroImagePositionEnum\", found left.", @$result['errors'][0]['message']);
    // }

    public function testAssetsMutation(): void
    {
        $input = 'mutation { story: upsertStories(title:"Asset Test'.date('U').'", heroImage:{url:"https://assets.imgix.net/examples/treefrog.jpg"}) { id, ...on Stories { heroImage { url } } } }';

        $result = $this->execute($input);

        $this->assertEquals('/uploads/treefrog', substr(@$result['data']['story']['heroImage'][0]['url'], 0, 17));
    }
}
