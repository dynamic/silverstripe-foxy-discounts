<?php

namespace Dynamic\Foxy\Discount\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class Discount extends DataObject
{
    /**
     * @var string
     */
    private static $singular_name = 'Discount';

    /**
     * @var string
     */
    private static $plural_name = 'Discounts';

    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(255)',
        'Quantity' => 'Int',
        'Percentage' => 'Int'
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Title',
        'Quantity',
        'DiscountPercentage' => [
            'title' => 'Discount',
        ],
    );

    /**
     * @var string
     */
    private static $table_name = 'FoxyDiscount';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
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

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param \SilverStripe\Security\Member|null $member
     * @return boolean
     */
    public function canView($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param \SilverStripe\Security\Member|null $member
     *
     * @return boolean
     */
    public function canEdit($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * Uses archive not delete so that current stage is respected i.e if a
     * element is not published, then it can be deleted by someone who doesn't
     * have publishing permissions.
     *
     * @param \SilverStripe\Security\Member|null $member
     *
     * @return boolean
     */
    public function canDelete($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param \SilverStripe\Security\Member|null $member
     * @param array $context
     *
     * @return boolean
     */
    public function canCreate($member = null, $context = array())
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }
}
