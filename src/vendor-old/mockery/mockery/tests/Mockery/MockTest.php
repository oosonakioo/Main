<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 *
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery\Adapter\Phpunit\MockeryTestCase;

class Mockery_MockTest extends MockeryTestCase
{
    /**
     * @var \Mockery\Container
     */
    public $container;

    protected function setup()
    {
        $this->container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
    }

    protected function teardown()
    {
        $this->container->mockery_close();
    }

    public function test_anonymous_mock_works_with_not_allowing_mocking_of_non_existent_methods()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = $this->container->mock();
        $m->shouldReceive('test123')->andReturn(true);
        assertThat($m->test123(), equalTo(true));
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function test_mock_with_not_allowing_mocking_of_non_existent_methods_can_be_given_additional_methods_to_mock_even_if_they_dont_exist_on_class()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = $this->container->mock('ExampleClassForTestingNonExistentMethod');
        $m->shouldAllowMockingMethod('testSomeNonExistentMethod');
        $m->shouldReceive('testSomeNonExistentMethod')->andReturn(true);
        assertThat($m->testSomeNonExistentMethod(), equalTo(true));
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function test_should_allow_mocking_method_returns_mock_instance()
    {
        $m = Mockery::mock('someClass');
        $this->assertInstanceOf('Mockery\MockInterface', $m->shouldAllowMockingMethod('testFunction'));
    }

    public function test_should_allow_mocking_protected_method_returns_mock_instance()
    {
        $m = Mockery::mock('someClass');
        $this->assertInstanceOf('Mockery\MockInterface', $m->shouldAllowMockingProtectedMethods('testFunction'));
    }

    public function test_mock_adds_to_string()
    {
        $mock = $this->container->mock('ClassWithNoToString');
        assertThat(hasToString($mock));
    }

    public function test_mock_to_string_may_be_deferred()
    {
        $mock = $this->container->mock('ClassWithToString')->shouldDeferMissing();
        assertThat((string) $mock, equalTo('foo'));
    }

    public function test_mock_to_string_should_ignore_missing_always_returns_string()
    {
        $mock = $this->container->mock('ClassWithNoToString')->shouldIgnoreMissing();
        assertThat(isNonEmptyString((string) $mock));

        $mock->asUndefined();
        assertThat(isNonEmptyString((string) $mock));
    }

    public function test_should_ignore_missing()
    {
        $mock = $this->container->mock('ClassWithNoToString')->shouldIgnoreMissing();
        assertThat(nullValue($mock->nonExistingMethod()));
    }

    public function test_should_ignore_debug_info()
    {
        $mock = $this->container->mock('ClassWithDebugInfo');

        $mock->__debugInfo();
    }

    /**
     * @expectedException Mockery\Exception
     */
    public function test_should_ignore_missing_disallow_mocking_non_existent_methods_using_global_configuration()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = $this->container->mock('ClassWithMethods')->shouldIgnoreMissing();
        $mock->shouldReceive('nonExistentMethod');
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function test_should_ignore_missing_calling_non_existent_methods_using_global_configuration()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = $this->container->mock('ClassWithMethods')->shouldIgnoreMissing();
        $mock->nonExistentMethod();
    }

    public function test_should_ignore_missing_calling_existent_methods()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = $this->container->mock('ClassWithMethods')->shouldIgnoreMissing();
        assertThat(nullValue($mock->foo()));
        $mock->shouldReceive('bar')->passthru();
        assertThat($mock->bar(), equalTo('bar'));
    }

    public function test_should_ignore_missing_calling_non_existent_methods()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        $mock = $this->container->mock('ClassWithMethods')->shouldIgnoreMissing();
        assertThat(nullValue($mock->foo()));
        assertThat(nullValue($mock->bar()));
        assertThat(nullValue($mock->nonExistentMethod()));

        $mock->shouldReceive(['foo' => 'new_foo', 'nonExistentMethod' => 'result']);
        $mock->shouldReceive('bar')->passthru();
        assertThat($mock->foo(), equalTo('new_foo'));
        assertThat($mock->bar(), equalTo('bar'));
        assertThat($mock->nonExistentMethod(), equalTo('result'));
    }

    public function test_can_mock_exception()
    {
        $exception = Mockery::mock('Exception');
        $this->assertInstanceOf('Exception', $exception);
    }
}

class ExampleClassForTestingNonExistentMethod {}

class ClassWithToString
{
    public function __toString()
    {
        return 'foo';
    }
}

class ClassWithNoToString {}

class ClassWithMethods
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }
}

class ClassWithDebugInfo
{
    public function __debugInfo()
    {
        return ['test' => 'test'];
    }
}
