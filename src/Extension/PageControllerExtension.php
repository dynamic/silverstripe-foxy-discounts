<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Model\DiscountTier;
use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Products\Page\Product;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\Form;
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
        'quantity',
        'h:product_id',
        'isAjax',
    ];

    /**
     * @var DiscountHelper|null
     */
    private $discount_helper = null;

    /**
     * @return $this
     */
    protected function setDiscountHelper()
    {
        if ($this->getIsDiscountable($this->owner->data())) {
            $this->discount_helper = $this->owner->data()->getBestDiscount();
        }

        return $this;
    }

    /**
     * @return DiscountHelper|null
     */
    protected function getDiscountHelper()
    {
        if (!$this->discount_helper) {
            $this->setDiscountHelper();
        }

        return $this->discount_helper;
    }

    /**
     * @param $form
     */
    public function updateAddToCartForm(&$form)
    {
        $page = $this->owner->data();
        /** @var DiscountHelper $discount */
        if ($discount = $this->getDiscountHelper()) {
            Requirements::javascript('dynamic/silverstripe-foxy-discounts: client/dist/javascript/discount.js');
            $code = $page->Code;
            if ($form instanceof Form && ($fields = $form->Fields())) {
                $fields->push(
                    HiddenField::create(AddToCartForm::getGeneratedValue(
                        $code,
                        $discount->getFoxyDiscountType(),
                        $discount->getDiscountFieldValue()
                    ))->setValue($this->getDiscountFieldValue())
                        ->addExtraClass('product-discount')
                );

                if ($endTime = $discount->getDiscountTier()->Discount()->EndTime) {
                    $fields->push(
                        HiddenField::create(AddToCartForm::getGeneratedValue(
                            $code,
                            'expires',
                            strtotime($endTime)
                        ))
                            ->setValue(strtotime($endTime))
                    );
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getDiscountFieldValue()
    {
        /** @var Discount $discount */
        if ($discount = $this->getDiscountHelper()) {
            return $this->getDiscountHelper()->getDiscountFieldValue();
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

        if (!$this->getIsDiscountable($product)) {
            return;
        }

        $totalPrice = Discount::config()->get('calculate_total');
        $quantity = (int)$request->getVar('quantity');
        $cost = ($totalPrice) ? $product->Price * $quantity : $product->Price;
        $optionsQuery = $this->getOptionsQuery($request->getVars());
        $options = $product->Options()->filter($optionsQuery);

        foreach ($options as $option) {
            switch ($option->PriceModifierAction) {
                case 'Add':
                    if ($totalPrice) {
                        $cost += ($option->PriceModifier * $quantity);
                    } else {
                        $cost += $option->PriceModifier;
                    }
                    break;
                case 'Subtract':
                    if ($totalPrice) {
                        $cost -= ($option->PriceModifier * $quantity);
                    } else {
                        $cost -= $option->ProceModifier;
                    }
                    break;
                case 'Set':
                    if ($totalPrice) {
                        $cost = ($option->PriceModifier * $quantity);
                    } else {
                        $cost = $option->PriceModifier;
                    }
                    break;
            }
        }
        return $this->getDiscountHelper() instanceof DiscountHelper
            ? $cost - $this->getDiscountHelper()->getDiscountedPrice()->getValue()
            : $cost;
    }

    /**
     * @param $vars
     * @return array
     */
    protected function getOptionsQuery($vars)
    {
        $exempt = $this->owner->config()->get('exempt_fields');
        $filter['PriceModifierAction:not'] = null;

        foreach ($vars as $key => $val) {
            if (!in_array($key, $exempt)) {
                $filter['OptionModifierKey'][] = explode('||', $val)[0];
            }
        }

        return $filter;
    }

    /**
     * @param $product
     * @return bool
     */
    public function getIsDiscountable($product)
    {
        return $product->hasMethod('getHasDiscount')
            && $product->hasMethod('getBestDiscount')
            && $product->getHasDiscount();
    }
}
