<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Model\DiscountTier;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBCurrency;

/**
 * Class ProductDataExtension
 * @package Dynamic\Foxy\Discounts\Extension
 *
 * @property bool $ExlcludedFromDiscounts
 */
class ProductDataExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $db = [
        'ExcludeFromDiscounts' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'Discounts' => Discount::class,
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $desc = 'Allows, or disallows if this product can be discounted globally. This overrides all other settings.';

        $fields->addFieldToTab(
            'Root.Ecommerce',
            DropdownField::create('ExcludeFromDiscounts')
                ->setSource([
                    false => 'Can be discounted',
                    true => 'Can\'t be discounted',
                ])
                ->setTitle('Can this product be discounted?')
                ->setDescription($desc),
            'Price'
        );
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
        if ($discount = $this->getBestDiscount()) {
            return $discount->getDiscountTier() instanceof DiscountTier;
        }

        return false;
    }
}
