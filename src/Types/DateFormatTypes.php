<?php

namespace markhuot\CraftQL\Types;

/**
 * Class DateFormatTypes
 * @package markhuot\CraftQL\Types
 * @craftql-type enum
 */
class DateFormatTypes {

    const atom = ['description' => 'Atom feeds'];
    const cookie = ['description' => 'HTTP cookies'];
    const iso8601 = ['description' => 'ISO-8601 spec'];
    const rfc822 = ['description' => 'RFC-822 spec'];
    const rfc850 = ['description' => 'RFC-850 spec'];
    const rfc1036 = ['description' => 'RFC-1036 spec'];
    const rfc1123 = ['description' => 'RFC-1123 spec'];
    const rfc2822 = ['description' => 'RFC-2822 spec'];
    const rfc3339 = ['description' => 'RFC-3339 spec'];
    const rss = ['description' => 'RSS feed'];
    const w3c = ['description' => 'W3C spec'];

}