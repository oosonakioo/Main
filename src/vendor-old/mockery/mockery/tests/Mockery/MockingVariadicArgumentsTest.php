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
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace test\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockingVariadicArgumentsTest extends MockeryTestCase
{
    protected function setup()
    {
        $this->container = new \Mockery\Container;
    }

    protected function teardown()
    {
        $this->container->mockery_close();
    }

    /** @test */
    public function should_allow_mocking_variadic_arguments()
    {
        $mock = $this->container->mock("test\Mockery\TestWithVariadicArguments");

        $mock->shouldReceive('foo')->andReturn('notbar');
        $this->assertEquals('notbar', $mock->foo());
    }
}

abstract class TestWithVariadicArguments
{
    public function foo(...$bar)
    {
        return $bar;
    }
}
