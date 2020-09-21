<?php

namespace Dynamic\Foxy\Discounts\Tests\Extension;

use Dynamic\Foxy\API\Client\APIClient;
use Dynamic\Foxy\Discounts\DiscountHelper;
use Dynamic\Foxy\Discounts\Extension\PageControllerExtension;
use Dynamic\Foxy\Discounts\Extension\ProductDataExtension;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Extension\TestDiscountExtension;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Extension\VariationDataExtension;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Page\ProductPage;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Page\ProductPageController;
use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Extension\PurchasableExtension;
use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\Variation;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Versioned\Versioned;

/**
 * Class AddToCartFormTest
 * @package Dynamic\Foxy\Discounts\Tests\Extension
 */
class AddToCartFormTest extends FunctionalTest
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
        ProductPageController::class => [
            PageControllerExtension::class,
            PurchasableExtension::class,
        ],
    ];

    /**
     * @var string[]
     */
    protected static $illegal_extensions = [
        /*Discount::class => [
            'Dynamic\\FoxyRecipe\\Extension\\DiscountDataExtension',
        ],//*/
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

        $product = $this->objFromFixture(ProductPage::class, 'productone');
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
    public function testUpdateAddToCartFormDiscountField()
    {
        $product = $this->objFromFixture(ProductPage::class, 'productone');
        $helper = DiscountHelper::create($product);
        $controller = ProductPageController::create($product);

        $key = AddToCartForm::getGeneratedValue(
            $product->Code,
            $helper->getFoxyDiscountType(),
            $helper->getDiscountFieldValue()
        );

        /** @var AddToCartForm $form */
        $form = $controller->AddToCartForm();
        $field = $form->Fields()->dataFieldByName($key);

        $this->assertInstanceOf(HiddenField::class, $field);
        $this->assertEquals($helper->getDiscountFieldValue(), $field->Value());
    }

    public function testUpdateAddToCartFormExpirationField()
    {
        $timeString = strtotime('tomorrow 5pm');
        $endTime = date('Y-m-d H:i:s', $timeString);

        /** @var Discount $discount */
        $discount = $this->objFromFixture(Discount::class, 'simplediscountpercentage');
        $discount->EndTime = $endTime;
        $discount->writeToStage(Versioned::DRAFT);
        $discount->publishSingle();

        $product = $this->objFromFixture(ProductPage::class, 'productone');
        $controller = ProductPageController::create($product);

        $key = AddToCartForm::getGeneratedValue(
            $product->Code,
            'expires',
            strtotime($endTime)
        );

        /** @var AddToCartForm $form */
        $form = $controller->AddToCartForm();
        $field = $form->Fields()->dataFieldByName($key);

        $this->assertInstanceOf(HiddenField::class, $field);
        $this->assertEquals($timeString, $field->Value());
    }
}
