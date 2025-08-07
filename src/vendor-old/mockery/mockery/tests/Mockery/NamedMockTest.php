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

class NamedMockTest extends MockeryTestCase
{
    /** @test */
    public function it_creates_a_named_mock()
    {
        $mock = Mockery::namedMock("Mockery\Dave123");
        $this->assertEquals("Mockery\Dave123", get_class($mock));
    }

    /** @test */
    public function it_creates_passes_further_arguments_just_like_mock()
    {
        $mock = Mockery::namedMock("Mockery\Dave456", 'DateTime', [
            'getDave' => 'dave',
        ]);

        $this->assertInstanceOf('DateTime', $mock);
        $this->assertEquals('dave', $mock->getDave());
    }

    /**
     * @test
     *
     * @expectedException Mockery\Exception
     *
     * @expectedExceptionMessage The mock named 'Mockery\Dave7' has been already defined with a different mock configuration
     */
    public function it_should_throw_if_attempting_to_redefine_named_mock()
    {
        $mock = Mockery::namedMock("Mockery\Dave7");
        $mock = Mockery::namedMock("Mockery\Dave7", 'DateTime');
    }
}
