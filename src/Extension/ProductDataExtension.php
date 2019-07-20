<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\Model\Discount;
use SilverStripe\ORM\DataExtension;

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
            $price = $this->owner->Price - ($this->owner->Price * ($this->getActiveDiscount()->Percentage/100));
            return $price;
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
