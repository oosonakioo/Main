<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 *
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace test\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use test\Mockery\Fixtures\MethodWithNullableReturnType;

/**
 * @requires PHP 7.1.0RC3
 */
class MockingNullableMethodsTest extends MockeryTestCase
{
    /**
     * @var \Mockery\Container
     */
    private $container;

    protected function setUp()
    {
        require_once __DIR__.'/Fixtures/MethodWithNullableReturnType.php';

        $this->container = new \Mockery\Container;
    }

    protected function tearDown()
    {
        $this->container->mockery_close();
    }

    /**
     * @test
     */
    public function it_should_allow_non_nullable_type_to_be_set()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullablePrimitive')->andReturn('a string');
        $mock->nonNullablePrimitive();
    }

    /**
     * @test
     *
     * @expectedException \TypeError
     */
    public function it_should_not_allow_non_null_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullablePrimitive')->andReturn(null);
        $mock->nonNullablePrimitive();
    }

    /**
     * @test
     */
    public function it_should_allow_primitive_nullable_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullablePrimitive')->andReturn(null);
        $mock->nullablePrimitive();
    }

    /**
     * @test
     */
    public function it_should_allow_primitive_nullabe_to_be_set()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullablePrimitive')->andReturn('a string');
        $mock->nullablePrimitive();
    }

    /**
     * @test
     */
    public function it_should_allow_self_to_be_set()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullableSelf')->andReturn(new MethodWithNullableReturnType);
        $mock->nonNullableSelf();
    }

    /**
     * @test
     *
     * @expectedException \TypeError
     */
    public function it_should_not_allow_self_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullableSelf')->andReturn(null);
        $mock->nonNullableSelf();
    }

    /**
     * @test
     */
    public function it_should_allow_nullable_self_to_be_set()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullableSelf')->andReturn(new MethodWithNullableReturnType);
        $mock->nullableSelf();
    }

    /**
     * @test
     */
    public function it_should_allow_nullable_self_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullableSelf')->andReturn(null);
        $mock->nullableSelf();
    }

    /**
     * @test
     */
    public function it_should_allow_class_to_be_set()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullableClass')->andReturn(new MethodWithNullableReturnType);
        $mock->nonNullableClass();
    }

    /**
     * @test
     *
     * @expectedException \TypeError
     */
    public function it_should_not_allow_class_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullableClass')->andReturn(null);
        $mock->nonNullableClass();
    }

    /**
     * @test
     */
    public function it_should_allow_nullalbe_class_to_be_set()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullableClass')->andReturn(new MethodWithNullableReturnType);
        $mock->nullableClass();
    }

    /**
     * @test
     */
    public function it_should_allow_nullable_class_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullableClass')->andReturn(null);
        $mock->nullableClass();
    }
}
