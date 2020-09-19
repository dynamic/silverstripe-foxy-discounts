<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Model\DiscountTier;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBCurrency;
use SilverStripe\ORM\HasManyList;

/**
 * Class ProductDataExtension
 * @package Dynamic\Foxy\Discounts\Extension
 */
class ProductDataExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $db = [
        //'ExcludeFromDiscounts' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'Discounts' => Discount::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        /*$fields->addFieldToTab(
            'Root.'
        );//*/
    }

    /**
     * @param int $quantity
     * @return DiscountHelper
     */
    public function getBestDiscount($quantity = 1)
    {
        return DiscountHelper::create($this->owner, $quantity);
    }

    /**
     * @param int $quantity
     * @return false|DBCurrency
     */
    public function getDiscountPrice($quantity = 1)
    {
        if ($discount = $this->getBestDiscount($quantity)) {
            return $discount->getDiscountedPrice();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getHasDiscount()
    {
        return $this->getBestDiscount()->getDiscountTier() instanceof DiscountTier;
    }
}
