<?php

namespace Dynamic\Foxy\Discounts\Tests;

use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Extension\ProductDataExtension;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Extension\TestDiscountExtension;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Page\ProductPage;
use Dynamic\Foxy\Extension\Purchasable;
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
    ];

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        Discount::add_extension(TestDiscountExtension::class);

        $product = $this->objFromFixture(ProductPage::class, 'productthree');
        $product->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $discountOne = $this->objFromFixture(Discount::class, 'simplediscountpercentage');
        $discountOne->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $discountTwo = $this->objFromFixture(Discount::class, 'tierdiscountpercentage');
        $discountTwo->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $discountThree = $this->objFromFixture(Discount::class, 'tierdiscountamount');
        $discountThree->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        Versioned::set_stage(Versioned::LIVE);
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
    }
}
