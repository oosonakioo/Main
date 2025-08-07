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

use SplObjectStorage;
use stdClass;

/**
 * @coversDefaultClass SebastianBergmann\Comparator\SplObjectStorageComparator
 */
class SplObjectStorageComparatorTest extends \PHPUnit_Framework_TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new SplObjectStorageComparator;
    }

    public function acceptsFailsProvider()
    {
        return [
            [new SplObjectStorage, new stdClass],
            [new stdClass, new SplObjectStorage],
            [new stdClass, new stdClass],
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        $object1 = new stdClass;
        $object2 = new stdClass;

        $storage1 = new SplObjectStorage;
        $storage2 = new SplObjectStorage;

        $storage3 = new SplObjectStorage;
        $storage3->attach($object1);
        $storage3->attach($object2);

        $storage4 = new SplObjectStorage;
        $storage4->attach($object2);
        $storage4->attach($object1);

        return [
            [$storage1, $storage1],
            [$storage1, $storage2],
            [$storage3, $storage3],
            [$storage3, $storage4],
        ];
    }

    public function assertEqualsFailsProvider()
    {
        $object1 = new stdClass;
        $object2 = new stdClass;

        $storage1 = new SplObjectStorage;

        $storage2 = new SplObjectStorage;
        $storage2->attach($object1);

        $storage3 = new SplObjectStorage;
        $storage3->attach($object2);
        $storage3->attach($object1);

        return [
            [$storage1, $storage2],
            [$storage1, $storage3],
            [$storage2, $storage3],
        ];
    }

    /**
     * @covers  ::accepts
     */
    public function test_accepts_succeeds()
    {
        $this->assertTrue(
            $this->comparator->accepts(
                new SplObjectStorage,
                new SplObjectStorage
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
    public function test_assert_equals_succeeds($expected, $actual)
    {
        $exception = null;

        try {
            $this->comparator->assertEquals($expected, $actual);
        } catch (ComparisonFailure $exception) {
        }

        $this->assertNull($exception, 'Unexpected ComparisonFailure');
    }

    /**
     * @covers       ::assertEquals
     *
     * @dataProvider assertEqualsFailsProvider
     */
    public function test_assert_equals_fails($expected, $actual)
    {
        $this->setExpectedException(
            'SebastianBergmann\\Comparator\\ComparisonFailure',
            'Failed asserting that two objects are equal.'
        );
        $this->comparator->assertEquals($expected, $actual);
    }
}
