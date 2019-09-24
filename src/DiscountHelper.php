<?php

namespace Dynamic\Foxy\Discounts;

use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Model\DiscountTier;
use Dynamic\Foxy\Model\ProductOption;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\FieldType\DBCurrency;
use SilverStripe\ORM\FieldType\DBField;

/**
 * Class DiscountHelper
 * @package Dynamic\Foxy\Discounts
 */
class DiscountHelper
{
    use Injectable;

    /**
     * @var
     */
    private $product;

    /**
     * @var ProductOption
     */
    private $product_option;

    /**
     * @var Discount
     */
    private $discount;

    /**
     * @var DBField|DBCurrency
     */
    private $discounted_price;

    /**
     * @var DiscountTier
     */
    private $discount_tier;

    /**
     * DiscountHelper constructor.
     * @param $product
     * @param $discount
     * @param ProductOption|string|null $productOption
     */
    public function __construct($product, $discount, $productOption = null)
    {
        $this->setProduct($product);
        $this->setDiscount($discount);

        if ($productOption instanceof ProductOption || is_string($productOption)) {
            $this->setProductOption($productOption);
        }

        $this->setDiscountedPrice();
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param $product
     * @return $this
     */
    public function setProduct($product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductOption()
    {
        return $this->product_option;
    }

    /**
     * @param ProductOption $productOption
     * @return $this
     */
    public function setProductOption($productOption): self
    {
        $this->product_option = ($productOption instanceof ProductOption)
            ? $productOption
            : $this->getProduct()->Options()->filter('OptionModifierKey', $productOption)->first();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param Discount $discount
     * @return $this
     */
    public function setDiscount(Discount $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function setDiscountTier($quantity = 1)
    {
        $this->discount_tier = $this->getDiscount()->DiscountTiers()
            ->filter('Quantity:LessThanOrEqual', $quantity)
            ->sort('Quantity DESC')->first();

        return $this;
    }

    /**
     * @return DiscountTier
     */
    public function getDiscountTier()
    {
        if (!$this->discount_tier) {
            $this->setDiscountTier();
        }

        return $this->discount_tier;
    }

    /**
     * @return DBField|DBCurrency
     */
    public function setDiscountedPrice()
    {
        $price = ($this->getProductOption())
            ? $this->getProductOption()->getPrice($this->getProduct())
            : $this->getProduct()->Price;

        $tier = $this->getDiscountTier();

        $price = ($this->getDiscount()->Type == 'Percent')
            ? $price - ($price * ($tier->Percentage / 100))
            : $price - $tier->Amount;

        return $this->discounted_price = DBField::create_field(DBCurrency::class, $price);
    }

    /**
     * @return mixed
     */
    public function getDiscountedPrice()
    {
        if (!$this->discounted_price) {
            $this->setDiscountedPrice();
        }

        return $this->discounted_price;
    }
}
