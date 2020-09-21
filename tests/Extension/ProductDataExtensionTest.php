<?php

namespace Dynamic\Foxy\Discounts\Tests\Page;

use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Extension\ProductDataExtension;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Extension\TestDiscountExtension;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Extension\VariationDataExtension;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Page\ProductPage;
use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Model\Variation;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Versioned\Versioned;

/**
 * Class ProductPageDiscountTest
 * @package Dynamic\Foxy\Discounts\Tests\Page
 */
class ProductDataExtensionTest extends SapphireTest
{
    /**
     * @var string[]
     */
    protected static $fixture_file = [
        '../products.yml',
        '../discounts.yml',
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
     * @var string[]
     */
    protected static $illegal_extensions = [
        Discount::class => [
            'Dynamic\\FoxyRecipe\\Extension\\DiscountDataExtension',
        ],
    ];

    /**
     *
     */
    protected function setUp()
    {
        if (class_exists('Dynamic\\Foxy\\API\\Client\\APIClient')) {
            Config::modify()->set('Dynamic\\Foxy\\API\\Client\\APIClient', 'enable_api', false);
        }
        if (class_exists('Dynamic\\Foxy\\SingleSignOn\\Client\\CustomerClient')) {
            Config::modify()->set('Dynamic\\Foxy\\SingleSignOn\\Client\\CustomerClient', 'foxy_sso_enabled', false);
        }

        parent::setUp();

        $product = $this->objFromFixture(ProductPage::class, 'productthree');
        $product->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $discountOne = $this->objFromFixture(Discount::class, 'simplediscountpercentage');
        $discountOne->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        $discountTwo = $this->objFromFixture(Discount::class, 'tierdiscountpercentage');
        $discountTwo->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        Versioned::set_stage(Versioned::LIVE);
    }

    /**
     *
     */
    public function testGetBestDiscount()
    {
        $product = $product = $this->objFromFixture(ProductPage::class, 'productthree');

        $this->assertInstanceOf(DiscountHelper::class, $product->getBestDiscount());

        //TODO test user_error for a 0 value
    }

    /**
     *
     */
    public function testGetDiscountPrice()
    {
        $product = $this->objFromFixture(ProductPage::class, 'productthree');

        $this->assertEquals(75, $product->getDiscountPrice()->getValue());

        $discountOne = $this->objFromFixture(Discount::class, 'simplediscountpercentage');
        $discountOne->doUnpublish();

        $this->assertEquals(70, $product->getDiscountPrice(23)->getValue());
    }

    /**
     * Expected failure in local tests of a foxy-recipe-installer
     */
    public function testGetHasDiscount()
    {
        $product = $this->objFromFixture(ProductPage::class, 'productthree');

        $this->assertTrue($product->getHasDiscount());

        $newProduct = ProductPage::create();
        $newProduct->Title = 'No Discount Product';
        $newProduct->Code = 'no-discount-product';
        $newProduct->Price = 1000;
        $newProduct->writeToStage(Versioned::DRAFT);
        $newProduct->publishSingle();

        /** Expected failure in local tests of a foxy-recipe-installer */
        Discount::get()->each(function (Discount $discount) use ($newProduct) {
            $discount->ExcludeProducts()->add($newProduct);
        });

        $this->assertFalse($newProduct->getHasDiscount());
    }
}
