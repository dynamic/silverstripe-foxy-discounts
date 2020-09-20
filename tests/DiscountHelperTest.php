<?php

namespace Dynamic\Foxy\Discounts\Tests;

use Dynamic\Foxy\API\Client\APIClient;
use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Extension\ProductDataExtension;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Extension\TestDiscountExtension;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Extension\VariationDataExtension;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Page\ProductPage;
use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Model\VariationType;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Versioned\Versioned;

/**
 * Class DiscountHelperTest
 * @package Dynamic\Foxy\Discounts\Tests
 */
class DiscountHelperTest extends SapphireTest
{
    /**
     * @var string[]
     */
    protected static $fixture_file = [
        'products.yml',
        'discounts.yml',
    ];

    /**
     * @var string[]
     */
    protected static $extra_dataobjects = [
        ProductPage::class,
    ];

    /**
     * @var \string[][]
     */
    protected static $required_extensions = [
        ProductPage::class => [
            Purchasable::class,
            ProductDataExtension::class,
        ],
        Discount::class => [
            TestDiscountExtension::class,
        ],
        Variation::class => [
            VariationDataExtension::class,
        ],
    ];

    /**
     *
     */
    protected function setUp()
    {
        APIClient::config()->set('enable_api', false);
        if (class_exists('Dynamic\Foxy\SingleSignOn\Client\CustomerClient')) {
            Config::modify()->set('Dynamic\Foxy\SingleSignOn\Client\CustomerClient', 'foxy_sso_enabled', false);
        }

        parent::setUp();

        $product = $this->objFromFixture(ProductPage::class, 'productthree');
        $product->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $productTwo = $this->objFromFixture(ProductPage::class, 'productfiveandvariations');
        $productTwo->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $discountOne = $this->objFromFixture(Discount::class, 'simplediscountpercentage');
        $discountOne->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $discountTwo = $this->objFromFixture(Discount::class, 'tierdiscountpercentage');
        $discountTwo->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $discountThree = $this->objFromFixture(Discount::class, 'tierdiscountamount');
        $discountThree->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        Versioned::set_stage(Versioned::LIVE);

        $variationOne = Variation::create();
        $variationOne->Title = 'Variation One';
        $variationOne->PriceModifier = 10;
        $variationOne->PriceModifierAction = 'Add';
        $variationOne->Available = true;
        $variationOne->VariationTypeID = VariationType::get()->first()->ID;
        $variationOne->ProductID = $productTwo->ID;
        $variationOne->write();

        $variationTwo = Variation::create();
        $variationTwo->Title = 'Variation Two';
        $variationTwo->PriceModifier = 150;
        $variationTwo->PriceModifierAction = 'Set';
        $variationTwo->Available = true;
        $variationTwo->VariationTypeID = VariationType::get()->first()->ID;
        $variationTwo->ProductID = $productTwo->ID;
        $variationTwo->write();

        $variationThree = Variation::create();
        $variationThree->Title = 'Variation Three';
        $variationThree->PriceModifier = 20;
        $variationThree->PriceModifierAction = 'Subtract';
        $variationThree->Available = true;
        $variationThree->VariationTypeID = VariationType::get()->first()->ID;
        $variationThree->ProductID = $productTwo->ID;
        $variationThree->write();
    }

    public function testGetProduct()
    {
        $product = $this->objFromFixture(ProductPage::class, 'productthree');
        $helper = DiscountHelper::create($product);

        $this->assertEquals($product->ID, $helper->getProduct()->ID);
    }

    /**
     *
     */
    public function testGetDiscountedPrice()
    {
        $product = $this->objFromFixture(ProductPage::class, 'productthree');
        $helper = DiscountHelper::create($product);

        $this->assertEquals(75, $helper->getDiscountedPrice()->getValue());

        $helper->setQuantity(25);
        $this->assertEquals(70, $helper->getDiscountedPrice()->getValue());

        $discountOne = $this->objFromFixture(Discount::class, 'simplediscountpercentage');
        $discountOne->doUnpublish();

        $helper->setDiscountTier();
        $helper->setQuantity(1);
        $this->assertEquals(95, $helper->getDiscountedPrice()->getValue());

        $helper->setQuantity(6);
        $this->assertEquals(88, $helper->getDiscountedPrice()->getValue());

        $helper->setQuantity(23);
        $this->assertEquals(70, $helper->getDiscountedPrice()->getValue());

        $product->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
    }

    /**
     *
     */
    public function testGetDiscountedPriceVariation()
    {
        $product = $this->objFromFixture(ProductPage::class, 'productfiveandvariations');

        $variationOne = Variation::get()->filter('PriceModifierAction', 'Add')->first();
        $variationTwo = Variation::get()->filter('PriceModifierAction', 'Set')->first();
        $variationThree = Variation::get()->filter('PriceModifierAction', 'Subtract')->first();

        //$110 less 25%
        $this->assertEquals(
            82.5,
            DiscountHelper::create($product, 1, $variationOne)->getDiscountedPrice()->getValue()
        );

        //$150 less 25%
        $this->assertEquals(
            112.5,
            DiscountHelper::create($product, 1, $variationTwo)->getDiscountedPrice()->getValue()
        );

        //$80 less 25%
        $this->assertEquals(
            60,
            DiscountHelper::create($product, 1, $variationThree)->getDiscountedPrice()->getValue()
        );
    }
}
