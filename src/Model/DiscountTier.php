<?php

namespace Dynamic\Foxy\Discounts\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

class DiscountTier extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'Quantity' => 'Int',
        'Percentage' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Discount' => Discount::class,
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'Quantity' => 1,
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'DiscountPercentage' => [
            'title' => 'Discount',
        ],
        'Quantity',
    ];

    /**
     * @var string
     */
    private static $table_name = 'FoxyDiscountTier';

    /**
     * @var array
     */
    private static $default_sort = array(
        'Quantity'
    );

    /**
     * @return FieldList|void
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'DiscountID',
            ]);

            $quantity = $fields->dataFieldByName('Quantity');
            $quantity->setTitle('Quantity to trigger discount');

            $percentage = $fields->dataFieldByName('Percentage');
            $percentage->setTitle('Percent discount');
        });

        return parent::getCMSFields();
    }

    /**
     * @return string
     */
    public function getDiscountPercentage()
    {
        return "{$this->Percentage}%";
    }
}
