<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\Model\Discount;
use SilverStripe\ORM\DataExtension;

/**
 * Class ProductDataExtension
 * @package Dynamic\Foxy\Discounts\Extension
 */
class ProductDataExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $belongs_many_many = [
        'Discounts' => Discount::class,
    ];

    /**
     * @return bool|float|int|mixed
     */
    public function getDiscountPrice()
    {
        if ($discount = $this->getActiveDiscount()) {
            if ($discount->DiscountTiers()->count() > 1) {
                if ($discount->Type == 'Percent') {
                    $discount_amount = $this->owner->Price * ($discount->DiscountTiers()->first()->Percentage / 100);
                } elseif ($discount->Type == 'Amount') {
                    $discount_amount = $discount->DiscountTiers()->first()->Amount;
                }
                $price = $this->owner->Price - $discount_amount;
                return $price;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getActiveDiscount()
    {
        $filter = [
            'StartTime:LessThanOrEqual' => date("Y-m-d H:i:s", strtotime('now')),
            'EndTime:GreaterThanOrEqual' => date("Y-m-d H:i:s", strtotime('now')),
        ];

        if ($this->owner->Discounts()->filter($filter)->count() > 0) {
            foreach ($this->owner->Discounts()->filter($filter)->sort('Percentage DESC') as $discount) {
                if ($discount->getIsActive()) {
                    return $discount;
                }
            }
        }

        foreach (Discount::get()->filter($filter)->sort('Percentage DESC') as $discount) {
            if ($discount->getIsGlobal()) {
                return $discount;
            }
        }

        return false;
    }
}
