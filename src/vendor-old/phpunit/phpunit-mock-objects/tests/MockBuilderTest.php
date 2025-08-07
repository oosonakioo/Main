<?php

/*
 * This file is part of the PHPUnit_MockObject package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @since      File available since Release 1.0.0
 */
class Framework_MockBuilderTest extends PHPUnit_Framework_TestCase
{
    public function test_mock_builder_requires_class_name()
    {
        $spec = $this->getMockBuilder('Mockable');
        $mock = $spec->getMock();
        $this->assertTrue($mock instanceof Mockable);
    }

    public function test_by_default_mocks_all_methods()
    {
        $spec = $this->getMockBuilder('Mockable');
        $mock = $spec->getMock();
        $this->assertNull($mock->mockableMethod());
        $this->assertNull($mock->anotherMockableMethod());
    }

    public function test_methods_to_mock_can_be_specified()
    {
        $spec = $this->getMockBuilder('Mockable');
        $spec->setMethods(['mockableMethod']);
        $mock = $spec->getMock();
        $this->assertNull($mock->mockableMethod());
        $this->assertTrue($mock->anotherMockableMethod());
    }

    public function test_by_default_does_not_pass_arguments_to_the_constructor()
    {
        $spec = $this->getMockBuilder('Mockable');
        $mock = $spec->getMock();
        $this->assertEquals([null, null], $mock->constructorArgs);
    }

    public function test_mock_class_name_can_be_specified()
    {
        $spec = $this->getMockBuilder('Mockable');
        $spec->setMockClassName('ACustomClassName');
        $mock = $spec->getMock();
        $this->assertTrue($mock instanceof ACustomClassName);
    }

    public function test_constructor_arguments_can_be_specified()
    {
        $spec = $this->getMockBuilder('Mockable');
        $spec->setConstructorArgs($expected = [23, 42]);
        $mock = $spec->getMock();
        $this->assertEquals($expected, $mock->constructorArgs);
    }

    public function test_original_constructor_can_be_disabled()
    {
        $spec = $this->getMockBuilder('Mockable');
        $spec->disableOriginalConstructor();
        $mock = $spec->getMock();
        $this->assertNull($mock->constructorArgs);
    }

    public function test_by_default_original_clone_is_preserved()
    {
        $spec = $this->getMockBuilder('Mockable');
        $mock = $spec->getMock();
        $cloned = clone $mock;
        $this->assertTrue($cloned->cloned);
    }

    public function test_original_clone_can_be_disabled()
    {
        $spec = $this->getMockBuilder('Mockable');
        $spec->disableOriginalClone();
        $mock = $spec->getMock();
        $mock->cloned = false;
        $cloned = clone $mock;
        $this->assertFalse($cloned->cloned);
    }

    public function test_calling_autoload_can_be_disabled()
    {
        // it is not clear to me how to test this nor the difference
        // between calling it or not
        $this->markTestIncomplete();
    }

    public function test_provides_a_fluent_interface()
    {
        $spec = $this->getMockBuilder('Mockable')
            ->setMethods(['mockableMethod'])
            ->setConstructorArgs([])
            ->setMockClassName('DummyClassName')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableAutoload();
        $this->assertTrue($spec instanceof PHPUnit_Framework_MockObject_MockBuilder);
    }
}
