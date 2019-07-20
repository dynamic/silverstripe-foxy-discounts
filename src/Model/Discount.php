<?php

namespace Dynamic\Foxy\Discounts\Model;

use Dynamic\Products\Page\Product;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Versioned\Versioned;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;

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
        'Percentage' => 'Int',
        'StartTime' => 'DBDatetime',
        'EndTime' => 'DBDatetime',
    );

    /**
     * @var array
     */
    private static $many_many = [
        'Products' => Product::class,
    ];

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Title',
        'DiscountPercentage' => [
            'title' => 'Discount',
        ],
        'StartTime.Nice' => 'Starts',
        'EndTime.Nice' => 'Ends',
        'IsActive' => 'Active',
        'IsGlobal' => 'Global',
        'Products.count' => 'Products',
    );

    /**
     * @var array
     */
    private static $defaults = [
        'Quantity' => 1,
    ];

    /**
     * @var array
     */
    private static $casting = [
        'IsActive' => 'Boolean',
        'IsGlobal' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $extensions = [
        Versioned::class,
    ];

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
            $fields->removeByName([
                'Quantity',
            ]);

            //$quantity = $fields->dataFieldByName('Quantity');
            //$quantity->setTitle('Quantity to trigger discount');

            $percentage = $fields->dataFieldByName('Percentage');
            $percentage->setTitle('Percent discount');

            if ($this->ID) {
                $field = $fields->dataFieldByName('Products');
                $config = $field->getConfig();
                $config
                    ->removeComponentsByType([
                        GridFieldAddExistingAutocompleter::class,
                        GridFieldAddNewButton::class,
                        GridFieldArchiveAction::class,
                    ])
                    ->addComponents([
                        new GridFieldAddExistingSearchButton(),
                    ]);
            }
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
     * @return bool
     */
    public function getIsActive()
    {
        $date = date('Y-m-d H:i:s', strtotime('now'));
        return ($this->owner->StartTime <= $date && $this->owner->EndTime >= $date) && $this->owner->isPublished();
    }

    /**
     * @return bool
     */
    public function getIsGlobal()
    {
        return $this->Products()->count() === 0;
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
