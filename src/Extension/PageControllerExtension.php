<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\FoxyHelper;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\HiddenField;
use SilverStripe\ORM\DataObject;

class PageControllerExtension extends Extension
{
    /**
     * @param $form
     */
    public function updateAddToCartForm(&$form)
    {
        $class = $this->owner->data()->ClassName;
        if($class::singleton()->hasMethod('getActiveDiscount')) {
            if ($this->owner->data()->getActiveDiscount()) {
                $code = $this->owner->data()->Code;
                $fields = $form->Fields();
                $fields->push(
                    HiddenField::create(AddToCartForm::getGeneratedValue(
                        $code,
                        'discount_quantity_percentage',
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
            $string = "|{$discount->Quantity}-{$discount->Percentage}";
            return "{$discount->Title}{allunits{$string}}";
        }
        return false;
    }
}
