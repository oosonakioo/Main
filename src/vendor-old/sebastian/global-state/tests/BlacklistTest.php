<?php

/*
 * This file is part of the GlobalState package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\GlobalState;

use PHPUnit_Framework_TestCase;

class BlacklistTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \SebastianBergmann\GlobalState\Blacklist
     */
    private $blacklist;

    protected function setUp()
    {
        $this->blacklist = new Blacklist;
    }

    public function test_global_variable_that_is_not_blacklisted_is_not_treated_as_blacklisted()
    {
        $this->assertFalse($this->blacklist->isGlobalVariableBlacklisted('variable'));
    }

    public function test_global_variable_can_be_blacklisted()
    {
        $this->blacklist->addGlobalVariable('variable');

        $this->assertTrue($this->blacklist->isGlobalVariableBlacklisted('variable'));
    }

    public function test_static_attribute_that_is_not_blacklisted_is_not_treated_as_blacklisted()
    {
        $this->assertFalse(
            $this->blacklist->isStaticAttributeBlacklisted(
                'SebastianBergmann\GlobalState\TestFixture\BlacklistedClass',
                'attribute'
            )
        );
    }

    public function test_class_can_be_blacklisted()
    {
        $this->blacklist->addClass('SebastianBergmann\GlobalState\TestFixture\BlacklistedClass');

        $this->assertTrue(
            $this->blacklist->isStaticAttributeBlacklisted(
                'SebastianBergmann\GlobalState\TestFixture\BlacklistedClass',
                'attribute'
            )
        );
    }

    public function test_subclasses_can_be_blacklisted()
    {
        $this->blacklist->addSubclassesOf('SebastianBergmann\GlobalState\TestFixture\BlacklistedClass');

        $this->assertTrue(
            $this->blacklist->isStaticAttributeBlacklisted(
                'SebastianBergmann\GlobalState\TestFixture\BlacklistedChildClass',
                'attribute'
            )
        );
    }

    public function test_implementors_can_be_blacklisted()
    {
        $this->blacklist->addImplementorsOf('SebastianBergmann\GlobalState\TestFixture\BlacklistedInterface');

        $this->assertTrue(
            $this->blacklist->isStaticAttributeBlacklisted(
                'SebastianBergmann\GlobalState\TestFixture\BlacklistedImplementor',
                'attribute'
            )
        );
    }

    public function test_class_name_prefixes_can_be_blacklisted()
    {
        $this->blacklist->addClassNamePrefix('SebastianBergmann\GlobalState');

        $this->assertTrue(
            $this->blacklist->isStaticAttributeBlacklisted(
                'SebastianBergmann\GlobalState\TestFixture\BlacklistedClass',
                'attribute'
            )
        );
    }

    public function test_static_attribute_can_be_blacklisted()
    {
        $this->blacklist->addStaticAttribute(
            'SebastianBergmann\GlobalState\TestFixture\BlacklistedClass',
            'attribute'
        );

        $this->assertTrue(
            $this->blacklist->isStaticAttributeBlacklisted(
                'SebastianBergmann\GlobalState\TestFixture\BlacklistedClass',
                'attribute'
            )
        );
    }
}
