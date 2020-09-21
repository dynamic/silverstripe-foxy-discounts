<?php

namespace Dynamic\Foxy\Discounts\Admin;

use Dynamic\Foxy\Discounts\Model\Discount;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;

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

    /**
     * @return DataList
     */
    public function getList()
    {
        $list = parent::getList();

        $excludeIDs = Member::get()->columnUnique('DiscountID');

        if (count($excludeIDs)) {
            $list = $list->exclude('ID', $excludeIDs);
        }

        return $list;
    }
}
