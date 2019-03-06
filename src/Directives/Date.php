<?php

namespace markhuot\CraftQL\Directives;

use GraphQL\Type\Definition\DirectiveLocation;
use markhuot\CraftQL\Builders\Directive;
use markhuot\CraftQL\Types\DateFormatTypes;

class Date extends Directive {

    protected $name = 'date';

    protected $description = 'Transform Timestamp types into string representations';

    protected $locations = [
        DirectiveLocation::FIELD,
    ];

    // @TODO maybe make this dynamic instead of setting `$this->name` above
    // function getName(): string {
    //     return lcfirst(parent::getName());
    // }

    function boot() {
        $this->addStringArgument('as')
            ->defaultValue('r')
            ->description('Date formatting');

        $this->addStringArgument('timezone')
            ->defaultValue('GMT')
            ->description('The full name of the timezone, defaults to GMT. (E.g., America/New_York)');

        $this->addArgument('format')
            ->type(DateFormatTypes::class)
            ->description('A standard format to use, overrides the `as` argument');

        $this->addStringArgument('locale')
            ->description('The locale to use when formatting the date');
    }

}