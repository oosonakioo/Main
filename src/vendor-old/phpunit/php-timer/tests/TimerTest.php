<?php

/*
 * This file is part of the PHP_Timer package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

class PHP_TimerTest extends TestCase
{
    /**
     * @covers PHP_Timer::start
     * @covers PHP_Timer::stop
     */
    public function test_start_stop()
    {
        $this->assertInternalType('float', PHP_Timer::stop());
    }

    /**
     * @covers       PHP_Timer::secondsToTimeString
     *
     * @dataProvider secondsProvider
     */
    public function test_seconds_to_time_string($string, $seconds)
    {
        $this->assertEquals(
            $string,
            PHP_Timer::secondsToTimeString($seconds)
        );
    }

    /**
     * @covers PHP_Timer::timeSinceStartOfRequest
     */
    public function test_time_since_start_of_request()
    {
        $this->assertStringMatchesFormat(
            '%f %s',
            PHP_Timer::timeSinceStartOfRequest()
        );
    }

    /**
     * @covers PHP_Timer::resourceUsage
     */
    public function test_resource_usage()
    {
        $this->assertStringMatchesFormat(
            'Time: %s, Memory: %fMB',
            PHP_Timer::resourceUsage()
        );
    }

    public function secondsProvider()
    {
        return [
            ['0 ms', 0],
            ['1 ms', .001],
            ['10 ms', .01],
            ['100 ms', .1],
            ['999 ms', .999],
            ['1 second', .9999],
            ['1 second', 1],
            ['2 seconds', 2],
            ['59.9 seconds', 59.9],
            ['59.99 seconds', 59.99],
            ['59.99 seconds', 59.999],
            ['1 minute', 59.9999],
            ['59 seconds', 59.001],
            ['59.01 seconds', 59.01],
            ['1 minute', 60],
            ['1.01 minutes', 61],
            ['2 minutes', 120],
            ['2.01 minutes', 121],
            ['59.99 minutes', 3599.9],
            ['59.99 minutes', 3599.99],
            ['59.99 minutes', 3599.999],
            ['1 hour', 3599.9999],
            ['59.98 minutes', 3599.001],
            ['59.98 minutes', 3599.01],
            ['1 hour', 3600],
            ['1 hour', 3601],
            ['1 hour', 3601.9],
            ['1 hour', 3601.99],
            ['1 hour', 3601.999],
            ['1 hour', 3601.9999],
            ['1.01 hours', 3659.9999],
            ['1.01 hours', 3659.001],
            ['1.01 hours', 3659.01],
            ['2 hours', 7199.9999],
        ];
    }
}
