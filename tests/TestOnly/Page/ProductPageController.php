<?php

namespace Dynamic\Foxy\Discounts\Tests\TestOnly\Page;

use Dynamic\Foxy\Discounts\Extension\PageControllerExtension;
use Dynamic\Foxy\Extension\PurchasableExtension;
use SilverStripe\Dev\TestOnly;

/**
 * Class ProductPageController
 * @package Dynamic\Foxy\Discounts\Tests\TestOnly\Page
 */
class ProductPageController extends \PageController implements TestOnly
{
    /**
     * @var string[]
     */
    private static $extensions = [
        PageControllerExtension::class,
        PurchasableExtension::class,
    ];
}
