<?php

namespace markhuot\CraftQL\Builders;

use Craft;
use craft\elements\Entry;
use craft\i18n\Locale;
use DateTime;
use DateTimeZone;

class Date extends Field {

    function getType() {
        $this->type = \markhuot\CraftQL\Types\Timestamp::type();
        return parent::getType();
    }

    function getResolve() {
        return function ($root, $args, $context, $info) {
            $format = 'U';
            $timezone = 'GMT';
            $locale = false;

            if (is_a($root, Entry::class)) {
                /** @var Entry $entry */
                $entry = $root;
                $locale = $entry->site->language;
            }

            if (isset($info->fieldNodes[0]->directives[0])) {
                $directive = $info->fieldNodes[0]->directives[0];
                if ($directive->arguments) {
                    foreach ($directive->arguments as $arg) {
                        switch ($arg->name->value) {
                            case 'as':
                                $format = $arg->value->value;
                                break;

                            case 'timezone':
                                $timezone = $arg->value->value;
                                break;

                            case 'format':
                                $format = constant('DateTime::' . strtoupper($arg->value->value));
                                break;

                            case 'locale':
                                $locale = $arg->value->value;
                                break;
                        }
                    }
                }
            }

            /** @var DateTime $date */
            $date = $root->{$info->fieldName};

            if ($this->isNonNull() && !$date) {
                throw new Error("`{$info->fieldName}` is a required field but has no value");
            }

            if (!$date) {
                return null;
            }

            $formatter = $locale ? (new Locale($locale))->getFormatter() : Craft::$app->getFormatter();
            $fmtTimeZone = $formatter->timeZone;
            $formatter->timeZone = $timezone;
            $formatted = $formatter->asDate($date, 'php:'.$format);
            $formatter->timeZone = $fmtTimeZone;
            return $formatted;
        };
    }

}