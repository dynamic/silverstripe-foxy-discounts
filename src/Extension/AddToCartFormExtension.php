<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Form\AddToCartForm;
use SilverStripe\Core\Extension;

/**
 * Class AddToCartFormExtension
 * @package Dynamic\Foxy\Discounts\Extension
 *
 * @property-read AddToCartFormExtension|AddToCartForm $owner
 */
class AddToCartFormExtension extends Extension
{
    /**
     * @param $fields
     * @throws \Exception
     */
    public function updateProductFields(&$fields)
    {
        $page = $this->owner->getProduct();
        if ($this->getIsDiscountable($page)) {
            /** @var DiscountHelper $discount */
            if ($discount = $page->getBestDiscount()) {
                $expirationMinutes = $this->resolveDiscountExpiration($discount->getDiscount()->EndTime);

                $this->owner->getExpirationHelper()->addExpiration($expirationMinutes);
            }
        }
    }

    /**
     * @param $product
     * @return bool
     */
    protected function getIsDiscountable($product)
    {
        return $product->hasMethod('getHasDiscount')
            && $product->hasMethod('getBestDiscount')
            && $product->getHasDiscount();
    }

    /**
     * @param $endTime
     * @return float|int|string
     * @throws \Exception
     */
    protected function resolveDiscountExpiration($endTime)
    {
        $expiration = new \DateTime($endTime, new \DateTimeZone('America/Chicago'));
        $now = new \DateTime('now', new \DateTimeZone('America/Chicago'));
        $diff = $now->diff($expiration);

        $days = $diff->format('%a');
        $hours = $diff->format('%H');
        $minutes = $diff->format('%I');

        $totalMinutes = (((($days * 24) + $hours) * 60) + $minutes);

        return $totalMinutes;
    }
}
