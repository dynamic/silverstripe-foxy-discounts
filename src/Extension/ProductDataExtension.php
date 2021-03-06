<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Model\Discount;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\HasManyList;

/**
 * Class ProductDataExtension
 * @package Dynamic\Foxy\Discounts\Extension
 */
class ProductDataExtension extends DataExtension
{
    /**
     * @var DiscountHelper
     */
    private $best_discount;

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'Discounts' => Discount::class,
    ];

    /**
     * @return mixed
     */
    private function getDiscountsList()
    {
        $list = Discount::get()->filter([
            'StartTime:LessThanOrEqual' => date("Y-m-d H:i:s", strtotime('now')),
            'EndTime:GreaterThanOrEqual' => date("Y-m-d H:i:s", strtotime('now')),
        ]);

        $strict = $list->filter([
            'Products.Count():GreaterThan' => 0,
            'Products.ID' => $this->owner->ID,
        ]);

        $global = $list->filter('Products.Count()', 0);

        $merge = array_merge(array_values($strict->column()), array_values($global->column()));

        return (!empty($merge))
            ? Discount::get()->byIDs($merge)
            : null;
    }

    /**
     * @param int $quantity
     * @param null $optionKey
     * @return $this
     */
    public function setBestDiscount($quantity = 1, $optionKey = null)
    {
        /** @var HasManyList $filtered */
        if ($filtered = $this->getDiscountsList()) {
            $filtered = $filtered->filter('DiscountTiers.Quantity:LessThanOrEqual', $quantity);

            if ($filtered->count() == 1) {
                $this->best_discount = DiscountHelper::create($this->owner, $filtered->first(), $optionKey);

                return $this;
            }

            $bestDiscount = null;

            /** @var Discount $discount */
            foreach ($filtered as $discount) {
                if ($bestDiscount === null) {
                    $bestDiscount = DiscountHelper::create($this->owner, $discount, $optionKey);
                    continue;
                }

                $testDiscount = DiscountHelper::create($this->owner, $discount, $optionKey);

                $bestDiscount = (float)$bestDiscount->getDiscountedPrice() > (float)$testDiscount->getDiscountedPrice()
                    ? $testDiscount
                    : $bestDiscount;
            }

            $this->best_discount = $bestDiscount;
        } else {
            $this->best_discount = null;
        }

        return $this;
    }

    /**
     * @return DiscountHelper
     */
    public function getBestDiscount()
    {
        //if (!$this->best_discount) {
        $this->setBestDiscount();

        //}

        return $this->best_discount;
    }

    /**
     * @param int $quantity
     * @param null $optionKey
     * @return bool|mixed
     */
    public function getDiscountPrice($quantity = 1, $optionKey = null)
    {
        if ($discount = $this->getBestDiscount()) {
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
            $discount = $discount->getDiscount();

            $restrictions = $discount->hasMethod('getRestrictions')
                ? $discount->getRestrictions()
                : [];

            return $this->getBestDiscount() instanceof DiscountHelper
                && (empty($restrictions) || in_array($this->owner->ID, $restrictions));
        }

        return false;
    }
}
