<?php

namespace Dynamic\Foxy\Discounts\Test\Model;

use Dynamic\Foxy\API\Client\APIClient;
use Dynamic\Foxy\Discounts\Model\Discount;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;

/**
 * Class DiscountPermissionTest
 * @package Dynamic\Foxy\Discounts\Test\Model
 */
class DiscountPermissionTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = [
        '../accounts.yml',
        '../discounts.yml',
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
    }

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(Discount::class, 'simplediscountpercentage');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testCanView()
    {
        /** @var Discount $object */
        $object = $this->objFromFixture(Discount::class, 'simplediscountpercentage');

        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canView($admin));

        /** @var Member $siteowner */
        $siteowner = $this->objFromFixture(Member::class, 'site-owner');
        $this->assertTrue($object->canView($siteowner));

        /** @var Member $member */
        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canView($member));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        /** @var Discount $object */
        $object = $this->objFromFixture(Discount::class, 'simplediscountpercentage');

        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canEdit($admin));

        /** @var Member $siteowner */
        $siteowner = $this->objFromFixture(Member::class, 'site-owner');
        $this->assertTrue($object->canEdit($siteowner));

        /** @var Member $member */
        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        /** @var Discount $object */
        $object = $this->objFromFixture(Discount::class, 'simplediscountpercentage');

        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canDelete($admin));

        /** @var Member $siteowner */
        $siteowner = $this->objFromFixture(Member::class, 'site-owner');
        $this->assertTrue($object->canDelete($siteowner));

        /** @var Member $member */
        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        /** @var Discount $object */
        $object = $this->objFromFixture(Discount::class, 'simplediscountpercentage');

        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canCreate($admin));

        /** @var Member $siteowner */
        $siteowner = $this->objFromFixture(Member::class, 'site-owner');
        $this->assertTrue($object->canCreate($siteowner));

        /** @var Member $member */
        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canCreate($member));
    }
}
