<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\FoxyHelper;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\HiddenField;
use SilverStripe\ORM\DataObject;

/**
 * Class PageControllerExtension
 * @package Dynamic\Foxy\Discounts\Extension
 */
class PageControllerExtension extends Extension
{
    /**
     * @param $form
     */
    public function updateAddToCartForm(&$form)
    {
        $class = $this->owner->data()->ClassName;
        if ($class::singleton()->hasMethod('getActiveDiscount')) {
            if ($discount = $this->owner->data()->getActiveDiscount()) {
                $code = $this->owner->data()->Code;
                $fields = $form->Fields();
                $fields->push(
                    HiddenField::create(AddToCartForm::getGeneratedValue(
                        $code,
                        $discount->getDiscountType(),
                        $this->getDiscountFieldValue()
                    ))->setValue($this->getDiscountFieldValue())
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getDiscountFieldValue()
    {
        if ($discount = $this->owner->data()->getActiveDiscount()) {
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
}
