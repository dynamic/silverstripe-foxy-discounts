<?php

namespace Dynamic\Foxy\Discounts\Extension;

use Dynamic\Foxy\Discount\Model\Discount;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataExtension;

class FoxyAdminExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.Discounts',
            [
                GridField::create(
                    'Discounts',
                    _t(__CLASS__ . '.DiscountsLabel', 'Discounts'),
                    Discount::get(),
                    GridFieldConfig_RecordEditor::create()
                ),
            ]
        );
    }
}
