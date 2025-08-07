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

/**
 * @coversDefaultClass SebastianBergmann\Comparator\NumericComparator
 */
class NumericComparatorTest extends \PHPUnit_Framework_TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new NumericComparator;
    }

    public function acceptsSucceedsProvider()
    {
        return [
            [5, 10],
            [8, '0'],
            ['10', 0],
            [0x74C3B00C, 42],
            [0755, 0777],
        ];
    }

    public function acceptsFailsProvider()
    {
        return [
            ['5', '10'],
            [8, 5.0],
            [5.0, 8],
            [10, null],
            [false, 12],
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        return [
            [1337, 1337],
            ['1337', 1337],
            [0x539, 1337],
            [02471, 1337],
            [1337, 1338, 1],
            ['1337', 1340, 5],
        ];
    }

    public function assertEqualsFailsProvider()
    {
        return [
            [1337, 1338],
            ['1338', 1337],
            [0x539, 1338],
            [1337, 1339, 1],
            ['1337', 1340, 2],
        ];
    }

    /**
     * @covers       ::accepts
     *
     * @dataProvider acceptsSucceedsProvider
     */
    public function test_accepts_succeeds($expected, $actual)
    {
        $this->assertTrue(
            $this->comparator->accepts($expected, $actual)
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
            'SebastianBergmann\\Comparator\\ComparisonFailure', 'matches expected'
        );
        $this->comparator->assertEquals($expected, $actual, $delta);
    }
}
