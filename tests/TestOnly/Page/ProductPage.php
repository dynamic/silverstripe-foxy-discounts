<?php

namespace Dynamic\Foxy\Discounts\Tests\TestOnly\Page;

use Dynamic\Foxy\Discounts\Extension\ProductDataExtension;
use Dynamic\Foxy\Extension\Purchasable;
use SilverStripe\Dev\TestOnly;

/**
 * Class ProductPage
 * @package Dynamic\Foxy\Discounts\Tests\TestOnly\Page
 */
class ProductPage extends \Page implements TestOnly
{
    /**
     * @var string
     */
    private static $table_name = 'FoxyProductTestPage';

    /**
     * @var string[]
     */
    private static $extensions = [
        Purchasable::class,
        ProductDataExtension::class,
    ];
}
