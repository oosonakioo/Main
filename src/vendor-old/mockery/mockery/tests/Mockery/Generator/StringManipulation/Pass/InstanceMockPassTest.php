<?php

namespace Mockery\Test\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;

class InstanceMockPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_append_constructor_and_property_for_instance_mock()
    {
        $builder = new MockConfigurationBuilder;
        $builder->setInstanceMock(true);
        $config = $builder->getMockConfiguration();
        $pass = new InstanceMockPass;
        $code = $pass->apply('class Dave { }', $config);
        $this->assertContains('public function __construct', $code);
        $this->assertContains('protected $_mockery_ignoreVerification', $code);
    }
}
