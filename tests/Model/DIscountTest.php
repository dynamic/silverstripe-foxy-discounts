<?php

namespace Dynamic\Foxy\Discounts\Tests\Model;

use Dynamic\Foxy\API\Client\APIClient;
use Dynamic\Foxy\Discounts\Extension\ProductDataExtension;
use Dynamic\Foxy\Discounts\Model\Discount;
use Dynamic\Foxy\Discounts\Model\DiscountTier;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Extension\TestDiscountExtension;
use Dynamic\Foxy\Discounts\Tests\TestOnly\Page\ProductPage;
use Dynamic\Foxy\Extension\Purchasable;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Versioned\Versioned;

/**
 * Class DiscountTest
 * @package Dynamic\Foxy\Discounts\Tests\Model
 */
class DiscountTest extends SapphireTest
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
    ];

    /**
     *
     */
    protected function setUp()
    {
        APIClient::config()->set('enable_api', false);

        if (class_exists('Dynamic\Foxy\SingleSignOn\Client\CustomerClient')) {
            Dynamic\Foxy\SingleSignOn\Client\CustomerClient::config()->get('foxy_sso_enabled');
        }

        parent::setUp();

        Discount::add_extension(TestDiscountExtension::class);

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
    public function testGetTierByQuantity()
    {
        /** @var Discount $discountOne */
        $discountOne = $this->objFromFixture(Discount::class, 'simplediscountpercentage');
        /** @var Discount $discountTwo */
        $discountTwo = $this->objFromFixture(Discount::class, 'tierdiscountpercentage');

        $this->assertEquals(
            $this->idFromFixture(DiscountTier::class, 'singletier'),
            $discountOne->getTierByQuantity(1)->ID
        );

        $this->assertEquals(
            $this->idFromFixture(DiscountTier::class, 'multitierone'),
            $discountTwo->getTierByQuantity(1)->ID
        );

        $this->assertEquals(
            $this->idFromFixture(DiscountTier::class, 'multitiertwo'),
            $discountTwo->getTierByQuantity(6)->ID
        );

        $this->assertEquals(
            $this->idFromFixture(DiscountTier::class, 'multitierthree'),
            $discountTwo->getTierByQuantity(23)->ID
        );
    }
}
