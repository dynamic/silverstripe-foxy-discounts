<?php

namespace Dynamic\Foxy\Discounts\Task;

use Dynamic\Foxy\Discounts\Model\DiscountTier;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\ValidationException;

/**
 * Class DiscountTierTypeTask
 * @package Dynamic\Foxy\Discounts\Task
 */
class DiscountTierTypeTask extends BuildTask
{
    /**
     * @var string
     */
    protected $title = "Foxy Discounts - Discount Tier Type Task";

    /**
     * @var string
     */
    protected $description = "Set the new ParentType field for existing Discount Tier records.";

    /**
     * @var string
     */
    private static $segment = 'foxy-discounts-discount-tier-type-task';

    /**
     * @param HTTPRequest $request
     * @throws ValidationException
     */
    public function run($request)
    {
        $this->setParentTypes();
    }

    /**
     * @throws ValidationException
     */
    protected function setParentTypes()
    {
        /** @var DiscountTier $tier */
        foreach (DiscountTier::get() as $tier) {
            $tier->ParentType = $tier->Discount()->Type;
            $tier->write();
        }
    }
}
