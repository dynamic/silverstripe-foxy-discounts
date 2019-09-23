<?php

namespace Dynamic\Foxy\Discounts\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * Class DiscountTier
 * @package Dynamic\Foxy\Discounts\Model
 */
class DiscountTier extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'Quantity' => 'Int',
        'Percentage' => 'Int',
        'Amount' => 'Currency',
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
        'DiscountLabel' => [
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
    private static $default_sort = [
        'Quantity',
    ];

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

            $type = $this->Discount()->Type;
            $percentage = $fields->dataFieldByName('Percentage')
                ->setTitle('Percent discount');
            $amount = $fields->dataFieldByName('Amount')
                ->setTitle('Amount to discount');

            $fields->removeByName([
                'Percentage',
                'Amount',
            ]);

            if ($type == 'Percent') {
                $fields->addFieldToTab(
                    'Root.Main',
                    $percentage
                );
            } elseif ($type == 'Amount') {
                $fields->addFieldToTab(
                    'Root.Main',
                    $amount
                );
            }
        });

        return parent::getCMSFields();
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult|void
     */
    public function validate()
    {
        $response = parent::validate();

        if ($this->exists()) {
            $exclude['ID'] = $this->ID;
        }

        /** @var Discount $discount */
        if ($discount = Discount::get()->byID($this->DiscountID)) {
            $existing = $discount->DiscountTiers()->filter('Quantity', $this->Quantity);
            if (isset($exclude)) {
                $existing = $existing->exclude($exclude);
            }

            if ($existing->count() > 0) {
                $response->addError("A discount tier already has the quantity {$this->Quantity} set");
            }
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getDiscountLabel()
    {
        $type = $this->Discount()->Type;
        if ($type == 'Percent') {
            return "{$this->Percentage}%";
        } elseif ($type == 'Amount') {
            return $this->dbObject('Amount')->Nice();
        }
    }
}
