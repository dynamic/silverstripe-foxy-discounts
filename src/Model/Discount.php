<?php

namespace Dynamic\Foxy\Discounts\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;

/**
 * Class Discount
 * @package Dynamic\Foxy\Discounts\Model
 *
 * @property string $Title
 * @property string $StartTime
 * @property string $EndTime
 * @property string $Type
 * @method HasManyList DiscountTiers()
 */
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
     * This will relay the total amount based on discount, option and quantity if true.
     * If false, it will assume the quantity is 1
     *
     * @var bool
     */
    private static $calculate_total = false;

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'StartTime' => 'DBDatetime',
        'EndTime' => 'DBDatetime',
        'Type' => 'Enum("Percent, Amount")',
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'DiscountTiers' => DiscountTier::class,
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title',
        'StartTime.Nice' => 'Starts',
        'EndTime.Nice' => 'Ends',
        'IsActive' => 'Active',
        'IsGlobal' => 'Global',
        'Products.count' => 'Products',
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
     * @var array
     */
    private $type_mapping = [
        'Percent' => 'discount_quantity_percentage',
        'Amount' => 'discount_quantity_amount',
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            if ($this->ID) {
                $fields->removeByName([
                    'DiscountTiers',
                ]);

                // ProductDiscountTiers
                $config = GridFieldConfig_RelationEditor::create();
                $config
                    ->removeComponentsByType([
                        GridFieldAddExistingAutocompleter::class,
                        GridFieldDeleteAction::class,
                    ])
                    ->addComponents([
                        new GridFieldDeleteAction(false),
                    ]);
                $discountGrid = GridField::create(
                    'DiscountTiers',
                    'Discount Tiers',
                    $this->owner->DiscountTiers(),
                    $config
                );
                $fields->addFieldToTab('Root.Main', $discountGrid);
            }
        });

        return parent::getCMSFields();
    }

    /**
     * @throws ValidationException
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->isChanged('Type')) {
            $this->DiscountTiers()->each(function (DiscountTier $tier) {
                $tier->write();
            });
        }
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
     * @return mixed
     */
    public function getDiscountType()
    {
        $types = $this->type_mapping;

        return $types[$this->Type];
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
    public function canCreate($member = null, $context = [])
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * @param int $quantity
     * @return DataObject|null
     */
    public function getTierByQuantity($quantity = 1)
    {
        $sort = $this->Type == 'Percent' ? 'Percentage DESC' : 'Amount DESC';
        return $this->DiscountTiers()->filter('Quantity:LessThanOrEqual', $quantity)->sort($sort)->first();
    }
}
