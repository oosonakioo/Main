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

/**
 * @requires PHP 7.1.0RC3
 */
class MockingMethodsWithNullableParametersTest extends MockeryTestCase
{
    /**
     * @var \Mockery\Container
     */
    private $container;

    protected function setUp()
    {
        require_once __DIR__.'/Fixtures/MethodWithNullableParameters.php';

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
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nonNullablePrimitive')->with('a string');
        $mock->nonNullablePrimitive('a string');
    }

    /**
     * @test
     *
     * @expectedException \TypeError
     */
    public function it_should_not_allow_non_null_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->nonNullablePrimitive(null);
    }

    /**
     * @test
     */
    public function it_should_allow_primitive_nullable_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullablePrimitive')->with(null);
        $mock->nullablePrimitive(null);
    }

    /**
     * @test
     */
    public function it_should_allow_primitive_nullabe_to_be_set()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullablePrimitive')->with('a string');
        $mock->nullablePrimitive('a string');
    }

    /**
     * @test
     */
    public function it_should_allow_self_to_be_set()
    {
        $obj = new \test\Mockery\Fixtures\MethodWithNullableParameters;
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nonNullableSelf')->with($obj);
        $mock->nonNullableSelf($obj);
    }

    /**
     * @test
     *
     * @expectedException \TypeError
     */
    public function it_should_not_allow_self_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->nonNullableSelf(null);
    }

    /**
     * @test
     */
    public function it_should_allow_nullalbe_self_to_be_set()
    {
        $obj = new \test\Mockery\Fixtures\MethodWithNullableParameters;

        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullableSelf')->with($obj);
        $mock->nullableSelf($obj);
    }

    /**
     * @test
     */
    public function it_should_allow_nullable_self_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullableClass')->with(null);
        $mock->nullableClass(null);
    }

    /**
     * @test
     */
    public function it_should_allow_class_to_be_set()
    {
        $obj = new \test\Mockery\Fixtures\MethodWithNullableParameters;
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nonNullableClass')->with($obj);
        $mock->nonNullableClass($obj);
    }

    /**
     * @test
     *
     * @expectedException \TypeError
     */
    public function it_should_not_allow_class_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->nonNullableClass(null);
    }

    /**
     * @test
     */
    public function it_should_allow_nullalbe_class_to_be_set()
    {
        $obj = new \test\Mockery\Fixtures\MethodWithNullableParameters;

        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullableClass')->with($obj);
        $mock->nullableClass($obj);
    }

    /**
     * @test
     */
    public function it_should_allow_nullable_class_to_be_null()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullableClass')->with(null);
        $mock->nullableClass(null);
    }
}
