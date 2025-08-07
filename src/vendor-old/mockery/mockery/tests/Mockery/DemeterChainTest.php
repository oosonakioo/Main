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

use Mockery\Adapter\Phpunit\MockeryTestCase;

class DemeterChainTest extends MockeryTestCase
{
    /** @var Mockery\Mock $this->mock */
    private $mock;

    protected function setUp()
    {
        $this->mock = $this->mock = Mockery::mock('object')->shouldIgnoreMissing();
    }

    protected function tearDown()
    {
        $this->mock->mockery_getContainer()->mockery_close();
    }

    public function test_two_chains()
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('something');

        $this->mock->shouldReceive('getElement->getSecond')
            ->once()
            ->andReturn('somethingElse');

        $this->assertEquals(
            'something',
            $this->mock->getElement()->getFirst()
        );
        $this->assertEquals(
            'somethingElse',
            $this->mock->getElement()->getSecond()
        );
        $this->mock->mockery_getContainer()->mockery_close();
    }

    public function test_two_chains_with_expected_parameters()
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->with('parameter')
            ->andReturn('something');

        $this->mock->shouldReceive('getElement->getSecond')
            ->once()
            ->with('secondParameter')
            ->andReturn('somethingElse');

        $this->assertEquals(
            'something',
            $this->mock->getElement()->getFirst('parameter')
        );
        $this->assertEquals(
            'somethingElse',
            $this->mock->getElement()->getSecond('secondParameter')
        );
        $this->mock->mockery_getContainer()->mockery_close();
    }

    public function test_three_chains()
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('something');

        $this->mock->shouldReceive('getElement->getSecond')
            ->once()
            ->andReturn('somethingElse');

        $this->assertEquals(
            'something',
            $this->mock->getElement()->getFirst()
        );
        $this->assertEquals(
            'somethingElse',
            $this->mock->getElement()->getSecond()
        );
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('somethingNew');
        $this->assertEquals(
            'somethingNew',
            $this->mock->getElement()->getFirst()
        );
    }

    public function test_many_chains()
    {
        $this->mock->shouldReceive('getElements->getFirst')
            ->once()
            ->andReturn('something');

        $this->mock->shouldReceive('getElements->getSecond')
            ->once()
            ->andReturn('somethingElse');

        $this->mock->getElements()->getFirst();
        $this->mock->getElements()->getSecond();
    }

    public function test_two_not_related_chains()
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('something');

        $this->mock->shouldReceive('getOtherElement->getSecond')
            ->once()
            ->andReturn('somethingElse');

        $this->assertEquals(
            'somethingElse',
            $this->mock->getOtherElement()->getSecond()
        );
        $this->assertEquals(
            'something',
            $this->mock->getElement()->getFirst()
        );
    }

    public function test_demeter_chain()
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('somethingElse');

        $this->assertEquals('somethingElse', $this->mock->getElement()->getFirst());
    }

    public function test_multi_level_demeter_chain()
    {
        $this->mock->shouldReceive('levelOne->levelTwo->getFirst')
            ->andReturn('first');

        $this->mock->shouldReceive('levelOne->levelTwo->getSecond')
            ->andReturn('second');

        $this->assertEquals(
            'second',
            $this->mock->levelOne()->levelTwo()->getSecond()
        );
        $this->assertEquals(
            'first',
            $this->mock->levelOne()->levelTwo()->getFirst()
        );
    }
}
