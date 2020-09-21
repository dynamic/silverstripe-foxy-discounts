<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discounts\Model\Discount;
use SilverShop\HasOneField\HasOneButtonField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\ORM\DataExtension;

/**
 * Class MemberExtension
 * @package Dynamic\Foxy\Discounts\Extension
 */
class MemberExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $has_one = [
        'Discount' => Discount::class,
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName([
            'DiscountID',
        ]);

        if ($this->owner->exists()) {
            $fields->addFieldToTab(
                'Root.Main',
                $discountButton = HasOneButtonField::create($this->owner, 'Discount'),
                'FirstName'
            );

            $discountButton->getConfig()->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        }
    }
}
