<?php

namespace Dynamic\Foxy\Discounts\Tests\TestOnly\Extension;

use Dynamic\Foxy\Discounts\Tests\TestOnly\Page\ProductPage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\GridFieldArchiveAction;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;

/**
 * Class TestDiscountExtension
 * @package Dynamic\Foxy\Discounts\Tests\TestOnly\Extension
 */
class TestDiscountExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $many_many = [
        'Products' => ProductPage::class,
        'ExcludeProducts' => ProductPage::class,
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->ID) {
            // Products
            $field = $fields->dataFieldByName('Products');
            $fields->removeByName('Products');
            $fields->addFieldToTab('Root.Included', $field);
            $field->setDescription('Limit the discount to these products.');
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

            $exclusions = $fields->dataFieldByName('ExcludeProducts');
            $fields->removeByName('ExcludeProducts');
            $fields->addFieldToTab('Root.Excluded', $exclusions);
            $exclusions->setDescription('Products in this list will ALWAYS be excluded from the discount.');
            $excludeConfig = $exclusions->getConfig();
            $excludeConfig
                ->removeComponentsByType([
                    GridFieldAddExistingAutocompleter::class,
                    GridFieldAddNewButton::class,
                    GridFieldArchiveAction::class,
                ])
                ->addComponents([
                    new GridFieldAddExistingSearchButton(),
                ]);
        }
    }

    /**
     * @return array
     */
    public function getRestrictions()
    {
        if ($this->owner->Products()->count() == 0) {
            $products = ProductPage::get()->column();
        } else {
            $products = $this->owner->Products()->column();
        }

        foreach ($this->owner->ExcludeProducts()->column() as $id) {
            if (in_array($id, $products)) {
                $key = array_search($id, $products);
                unset($products[$key]);
            }
        }

        return $products;
    }
}
