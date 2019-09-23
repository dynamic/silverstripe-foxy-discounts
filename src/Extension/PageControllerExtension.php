<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Model\DiscountTier;
use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Products\Page\Product;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\HiddenField;
use SilverStripe\View\Requirements;

/**
 * Class PageControllerExtension
 * @package Dynamic\Foxy\Discounts\Extension
 */
class PageControllerExtension extends Extension
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'fetchprice',
    ];

    /**
     * @var array
     */
    private static $exempt_fields = [
        'price',
        'x:visibleQuantity',
        'h:product_id',
        'isAjax',
    ];

    /**
     * @param $form
     */
    public function updateAddToCartForm(&$form)
    {
        $class = $this->owner->data()->ClassName;
        if ($class::singleton()->hasMethod('getBestDiscount')) {
            /** @var DiscountHelper $discount */
            if ($discount = $this->owner->data()->getBestDiscount()) {
                Requirements::javascript('dynamic/silverstripe-foxy-discounts: client/dist/javascript/discount.js');
                $code = $this->owner->data()->Code;
                $fields = $form->Fields();
                $fields->push(
                    HiddenField::create(AddToCartForm::getGeneratedValue(
                        $code,
                        $discount->getDiscount()->getDiscountType(),
                        $this->getDiscountFieldValue()
                    ))->setValue($this->getDiscountFieldValue())
                        ->addExtraClass('product-discount')
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getDiscountFieldValue()
    {
        /** @var Discount $discount */
        if ($discount = $this->owner->data()->getBestDiscount()->getDiscount()) {
            $tiers = $discount->DiscountTiers();
            $bulkString = '';
            foreach ($tiers as $tier) {
                if ($discount->Type == 'Percent') {
                    $bulkString .= "|{$tier->Quantity}-{$tier->Percentage}";
                    $method = 'allunits';
                } elseif ($discount->Type == 'Amount') {
                    $bulkString .= "|{$tier->Quantity}-{$tier->Amount}";
                    $method = 'allunits';
                }
            }

            return "{$discount->Title}{{$method}{$bulkString}}";
        }

        return false;
    }

    public function fetchprice(HTTPRequest $request)
    {
        if (!$id = $request->getVar('h:product_id')) {
            return;
        }

        if (!$product = Product::get()->byID(explode('||', $id)[0])) {
            return;
        }

        $quantity = (int)$request->getVar('quantity');

        $cost = $product->Price * $quantity;

        $optionsQuery = $this->getOptionsQuery($request->getVars());

        $options = $product->Options()->filter($optionsQuery);

        foreach ($options as $option) {
            switch ($option->PriceModifierAction) {
                case 'Add':
                    $cost += ($option->PriceModifier * $quantity);
                    break;
                case 'Subtract':
                    $cost -= ($option->PriceModifier * $quantity);
                    break;
                case 'Set':
                    $cost = ($option->PriceModifier * $quantity);
                    break;
            }
        }

        $discount = $this->getDiscount($quantity);

        if ($discount instanceof DiscountTier && $discount->exists()) {
            if ($discount->Discount()->Type == 'Percent') {
                $discountAmount = $cost * ($discount->Percentage / 100);
            } elseif ($discount->Discount()->Type == 'Amount') {
                $discountAmount = $discount->Amount;
            }

            if (isset($discountAmount)) {
                $cost = $cost - $discountAmount;
            }
        }

        return $cost;
    }

    /**
     * @param $quantity
     * @return mixed
     */
    protected function getDiscount($quantity)
    {
        /** @var DiscountHelper $best */
        $best = $this->owner->data()->getBestDiscount();

        $best->setDiscountTier($quantity);

        $tier = $best->getDiscountTier();

        return $tier;
    }

    /**
     * @param $vars
     * @return array
     */
    protected function getOptionsQuery($vars)
    {
        $exempt = $this->owner->config()->get('exempt_fields');
        $filter = ['PriceModifierAction:not' => null];

        foreach ($vars as $key => $val) {
            if (!in_array($key, $exempt)) {
                $filter['OptionModifierKey'][] = explode('||', $val)[0];
            }
        }

        return $filter;
    }
}
