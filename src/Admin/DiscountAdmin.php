<?php

namespace Dynamic\Foxy\Discounts\Admin;

use Dynamic\Foxy\Discounts\Model\Discount;
use SilverStripe\Admin\ModelAdmin;

/**
 * Class DiscountAdmin
 * @package Dynamic\Foxy\Discounts\Admin
 */
class DiscountAdmin extends ModelAdmin
{
    /**
     * @var array
     */
    private static $managed_models = [
        Discount::class,
    ];

    /**
     * @var string
     */
    private static $url_segment = 'discounts';

    /**
     * @var string
     */
    private static $menu_title = 'Discounts';
}
