<?php

namespace Dynamic\Foxy\Discounts;

use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Model\DiscountTier;
use Dynamic\Foxy\Model\Variation;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
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
     * @var DataList|null
     */
    private $available_discounts = null;

    /**
     * @var Variation
     */
    private $variation;


    /**
     * @var DiscountTier
     */
    private $discount_tier;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var DBField|DBCurrency
     */
    private $discounted_price;

    /**
     * DiscountHelper constructor.
     * @param $product
     * @param int $quantity
     * @param Variation|null $variation
     */
    public function __construct($product, $quantity = 1, $variation = null)
    {
        $this->setProduct($product);
        $this->setQuantity($quantity);
        $this->setDiscountTier();

        if ($variation instanceof Variation) {
            $this->setVariation($variation);
        }
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

        $this->setAvailableDiscounts();

        return $this;
    }

    /**
     * Set the available discounts based on DiscountHelper::product
     *
     * @return $this
     */
    public function setAvailableDiscounts()
    {
        if (!$this->getProduct()->ExcludeFromDiscounts) {
            $now = date("Y-m-d H:i:s", strtotime('now'));
            //don't get discounts the product is excluded from
            $list = Discount::get()->exclude([
                'ExcludeProducts.ID' => $this->getProduct()->ID,
            ])->whereAny([
                "`StartTime` <= '{$now}' AND `EndTime` >= '{$now}'",
                "(`StartTime` = '' OR `StartTime` IS NULL) AND (`EndTime` = '' OR `EndTime` IS NULL)",
                "`StartTime` <= '{$now}' AND (`EndTime` = '' OR `EndTime` IS NULL)",
                "(`StartTime` = '' OR `StartTime` IS NULL) AND `EndTime` >= '{$now}'",
            ]);

            $strict = $list->filter([
                'Products.Count():GreaterThan' => 0,
                'Products.ID' => $this->getProduct()->ID,
            ]);

            $global = $list->filter('Products.Count()', 0);

            $merge = array_merge(array_values($strict->column()), array_values($global->column()));

            $this->available_discounts = count($merge) ? Discount::get()->byIDs($merge) : ArrayList::create();
        }

        return $this;
    }

    /**
     * @return DataList|null
     */
    public function getAvailableDiscounts()
    {
        return $this->available_discounts;
    }

    /**
     * @param Variation $variation
     * @return $this
     */
    public function setVariation($variation): self
    {
        $this->variation = ($variation instanceof Variation)
            ? $variation
            : $this->getProduct()->Variations()->filter('', $variation)->first();//TODO fix this string thing, is it needed?

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVariation()
    {
        return $this->variation;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        if ($quantity < 1) {
            user_error("\$quantity must be at least 1");
        }

        $this->quantity = $quantity;

        $this->setDiscountTier();

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return $this
     */
    public function setDiscountTier()
    {
        $this->discount_tier = $this->findBestDiscount();

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
     * @return mixed
     */
    protected function findBestDiscount()
    {
        $appropriateTiers = ArrayList::create();

        /** @var Discount $discount */
        foreach ($this->getAvailableDiscounts() as $discount) {
            if ($tier = $discount->getTierByQuantity($this->getQuantity())) {
                $appropriateTiers->push($tier);
            }
        }

        return $this->resolveDiscountTiers($appropriateTiers);
    }

    /**
     * @param $discountTiers
     * @return DiscountTier|null
     */
    protected function resolveDiscountTiers($discountTiers)
    {
        if (!$discountTiers->count()) {
            return null;
        }

        $basePrice = $this->getProduct()->Price;
        $bestTier = null;
        $calculatePrice = function (DiscountTier $tier) use ($basePrice) {
            if ($tier->ParentType == 'Percent') {
                return $basePrice - ($basePrice * ($tier->Percentage / 100));
            } else {
                return $basePrice - $tier->Amount;
            }
        };

        /** @var DiscountTier $tier */
        foreach ($discountTiers as $tier) {
            if ($bestTier == null) {
                $bestTier = [
                    'price' => $calculatePrice($tier),
                    'discountTier' => $tier,
                ];
                continue;
            }

            if ($calculatePrice($tier) < $bestTier['price']) {
                $bestTier['price'] = $calculatePrice($tier);
                $bestTier['discountTier'] = $tier;
            }
        }

        return is_array($bestTier) && isset($bestTier['discountTier']) ? $bestTier['discountTier'] : null;
    }

    /**
     * @return DBCurrency
     */
    public function getDiscountedPrice()
    {
        $price = ($this->getVariation())
            ? $this->getVariation()->FinalPrice
            : $this->getProduct()->Price;

        $tier = $this->getDiscountTier();

        $price = ($this->getDiscountTier()->ParentType == 'Percent')
            ? $price - ($price * ($tier->Percentage / 100))
            : $price - $tier->Amount;

        return DBField::create_field(DBCurrency::class, $price);
    }

    /**
     * @return string
     */
    public function getFoxyDiscountType()
    {
        return $this->getDiscountTier()->ParentType == 'Percent'
            ? 'discount_quantity_percentage'
            : 'discount_quantity_amount';
    }

    /**
     * @return false|string
     */
    public function getDiscountFieldValue()
    {
        if ($this->getDiscountTier()) {
            $discount = $this->getDiscountTier()->Discount();
            $field = $discount->Type == 'Percent' ? 'Percentage' : 'Amount';
            $discountString = $discount->Title . '{allunits';

            foreach (DiscountTier::get()->filter('DiscountID', $this->getDiscountTier()->DiscountID) as $tier) {
                $discountString .= "|{$tier->Quantity}-{$tier->{$field}}";
            }

            $discountString .= '}';
            return $discountString;
        }

        return false;
    }
}
