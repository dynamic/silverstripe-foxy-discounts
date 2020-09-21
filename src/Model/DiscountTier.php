<?php

namespace Dynamic\Foxy\Discounts\Model;

use Dynamic\Foxy\Coupons\Model\Coupon;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * Class DiscountTier
 * @package Dynamic\Foxy\Discounts\Model
 *
 * @property string $ParentType Save the parent Discount's type so we don't need to run a query
 * @property int $Quantity
 * @property int $Percentage
 * @property float $Amount
 * @property int $DiscountID
 * @method Discount Discount()
 */
class DiscountTier extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'ParentType' => 'Varchar',
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
                'ParentType',
            ]);

            $quantity = $fields->dataFieldByName('Quantity');
            $quantity->setTitle('Quantity to trigger discount');

            /** @var Discount|Coupon $type */
            if ($parent = $this->getParent()) {
                $type = $parent->Type;

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
            }
        });

        return parent::getCMSFields();
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $this->ParentType = $this->Discount()->Type;
    }

    /**
     * @return string
     */
    protected function getParent()
    {
        foreach ($this->hasOne() as $relationName => $className) {
            $field = "{$relationName}ID";

            if ($this->{$field} > 0) {
                return $className::get()->byID($this->{$field});
            }
        }

        return false;
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
        $label = '';

        if ($this->ParentType == 'Percent') {
            $label = "{$this->Percentage}%";
        } elseif ($this->ParentType == 'Amount') {
            $label = $this->dbObject('Amount')->Nice();
        }

        $this->extend('updateDiscountLabel', $label);

        return $label;
    }
}
