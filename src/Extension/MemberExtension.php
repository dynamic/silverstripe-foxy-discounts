<?php

namespace Dynamic\Foxy\Discounts\Extension;

use SilverStripe\ORM\DataExtension;

/**
 * Class MemberExtension
 * @package Dynamic\Foxy\Discounts\Extension
 */
class MemberExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $db = [
        'DiscountPercent' => 'Int',
        'DiscountExpires' => 'DBDatetime',
    ];
}
