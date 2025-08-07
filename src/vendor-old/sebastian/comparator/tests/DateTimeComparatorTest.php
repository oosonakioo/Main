<?php

/*
 * This file is part of the Comparator package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Comparator;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

/**
 * @coversDefaultClass SebastianBergmann\Comparator\DateTimeComparator
 */
class DateTimeComparatorTest extends \PHPUnit_Framework_TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new DateTimeComparator;
    }

    public function acceptsFailsProvider()
    {
        $datetime = new DateTime;

        return [
            [$datetime, null],
            [null, $datetime],
            [null, null],
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        return [
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:13:25', new DateTimeZone('America/New_York')),
                10,
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:14:40', new DateTimeZone('America/New_York')),
                65,
            ],
            [
                new DateTime('2013-03-29', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29', new DateTimeZone('America/New_York')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 03:13:35', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 03:13:49', new DateTimeZone('America/Chicago')),
                15,
            ],
            [
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 23:00:00', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 23:01:30', new DateTimeZone('America/Chicago')),
                100,
            ],
            [
                new DateTime('@1364616000'),
                new DateTime('2013-03-29 23:00:00', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-29T05:13:35-0500'),
                new DateTime('2013-03-29T04:13:35-0600'),
            ],
        ];
    }

    public function assertEqualsFailsProvider()
    {
        return [
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 03:13:35', new DateTimeZone('America/New_York')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 03:13:35', new DateTimeZone('America/New_York')),
                3500,
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 05:13:35', new DateTimeZone('America/New_York')),
                3500,
            ],
            [
                new DateTime('2013-03-29', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
            ],
            [
                new DateTime('2013-03-29', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
                43200,
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/Chicago')),
                3500,
            ],
            [
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-30', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-29T05:13:35-0600'),
                new DateTime('2013-03-29T04:13:35-0600'),
            ],
            [
                new DateTime('2013-03-29T05:13:35-0600'),
                new DateTime('2013-03-29T05:13:35-0500'),
            ],
        ];
    }

    /**
     * @covers  ::accepts
     */
    public function test_accepts_succeeds()
    {
        $this->assertTrue(
            $this->comparator->accepts(
                new DateTime,
                new DateTime
            )
        );
    }

    /**
     * @covers       ::accepts
     *
     * @dataProvider acceptsFailsProvider
     */
    public function test_accepts_fails($expected, $actual)
    {
        $this->assertFalse(
            $this->comparator->accepts($expected, $actual)
        );
    }

    /**
     * @covers       ::assertEquals
     *
     * @dataProvider assertEqualsSucceedsProvider
     */
    public function test_assert_equals_succeeds($expected, $actual, $delta = 0.0)
    {
        $exception = null;

        try {
            $this->comparator->assertEquals($expected, $actual, $delta);
        } catch (ComparisonFailure $exception) {
        }

        $this->assertNull($exception, 'Unexpected ComparisonFailure');
    }

    /**
     * @covers       ::assertEquals
     *
     * @dataProvider assertEqualsFailsProvider
     */
    public function test_assert_equals_fails($expected, $actual, $delta = 0.0)
    {
        $this->setExpectedException(
            'SebastianBergmann\\Comparator\\ComparisonFailure',
            'Failed asserting that two DateTime objects are equal.'
        );
        $this->comparator->assertEquals($expected, $actual, $delta);
    }

    /**
     * @requires PHP 5.5
     *
     * @covers   ::accepts
     */
    public function test_accepts_date_time_interface()
    {
        $this->assertTrue($this->comparator->accepts(new DateTime, new DateTimeImmutable));
    }

    /**
     * @requires PHP 5.5
     *
     * @covers   ::assertEquals
     */
    public function test_supports_date_time_interface()
    {
        $this->comparator->assertEquals(
            new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
            new DateTimeImmutable('2013-03-29 04:13:35', new DateTimeZone('America/New_York'))
        );
    }
}
