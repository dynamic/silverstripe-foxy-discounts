<?php

namespace Dynamic\Foxy\Discounts\Tests\TestOnly\Extension;

use Dynamic\Foxy\Discounts\Tests\TestOnly\Page\ProductPage;
use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataExtension;

/**
 * Class VariationDataExtension
 */
class VariationDataExtension extends DataExtension implements TestOnly
{
    /**
     * @var string[]
     */
    private static $has_one = [
        'Product' => ProductPage::class,
    ];
}
