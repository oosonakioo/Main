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
 * @coversDefaultClass SebastianBergmann\Comparator\MockObjectComparator
 */
class MockObjectComparatorTest extends \PHPUnit_Framework_TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new MockObjectComparator;
        $this->comparator->setFactory(new Factory);
    }

    public function acceptsSucceedsProvider()
    {
        $testmock = $this->getMock('SebastianBergmann\\Comparator\\TestClass');
        $stdmock = $this->getMock('stdClass');

        return [
            [$testmock, $testmock],
            [$stdmock, $stdmock],
            [$stdmock, $testmock],
        ];
    }

    public function acceptsFailsProvider()
    {
        $stdmock = $this->getMock('stdClass');

        return [
            [$stdmock, null],
            [null, $stdmock],
            [null, null],
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        // cyclic dependencies
        $book1 = $this->getMock('SebastianBergmann\\Comparator\\Book', null);
        $book1->author = $this->getMock('SebastianBergmann\\Comparator\\Author', null, ['Terry Pratchett']);
        $book1->author->books[] = $book1;
        $book2 = $this->getMock('SebastianBergmann\\Comparator\\Book', null);
        $book2->author = $this->getMock('SebastianBergmann\\Comparator\\Author', null, ['Terry Pratchett']);
        $book2->author->books[] = $book2;

        $object1 = $this->getMock('SebastianBergmann\\Comparator\\SampleClass', null, [4, 8, 15]);
        $object2 = $this->getMock('SebastianBergmann\\Comparator\\SampleClass', null, [4, 8, 15]);

        return [
            [$object1, $object1],
            [$object1, $object2],
            [$book1, $book1],
            [$book1, $book2],
            [
                $this->getMock('SebastianBergmann\\Comparator\\Struct', null, [2.3]),
                $this->getMock('SebastianBergmann\\Comparator\\Struct', null, [2.5]),
                0.5,
            ],
        ];
    }

    public function assertEqualsFailsProvider()
    {
        $typeMessage = 'is not instance of expected class';
        $equalMessage = 'Failed asserting that two objects are equal.';

        // cyclic dependencies
        $book1 = $this->getMock('SebastianBergmann\\Comparator\\Book', null);
        $book1->author = $this->getMock('SebastianBergmann\\Comparator\\Author', null, ['Terry Pratchett']);
        $book1->author->books[] = $book1;
        $book2 = $this->getMock('SebastianBergmann\\Comparator\\Book', null);
        $book2->author = $this->getMock('SebastianBergmann\\Comparator\\Author', null, ['Terry Pratch']);
        $book2->author->books[] = $book2;

        $book3 = $this->getMock('SebastianBergmann\\Comparator\\Book', null);
        $book3->author = 'Terry Pratchett';
        $book4 = $this->getMock('stdClass');
        $book4->author = 'Terry Pratchett';

        $object1 = $this->getMock('SebastianBergmann\\Comparator\\SampleClass', null, [4, 8, 15]);
        $object2 = $this->getMock('SebastianBergmann\\Comparator\\SampleClass', null, [16, 23, 42]);

        return [
            [
                $this->getMock('SebastianBergmann\\Comparator\\SampleClass', null, [4, 8, 15]),
                $this->getMock('SebastianBergmann\\Comparator\\SampleClass', null, [16, 23, 42]),
                $equalMessage,
            ],
            [$object1, $object2, $equalMessage],
            [$book1, $book2, $equalMessage],
            [$book3, $book4, $typeMessage],
            [
                $this->getMock('SebastianBergmann\\Comparator\\Struct', null, [2.3]),
                $this->getMock('SebastianBergmann\\Comparator\\Struct', null, [4.2]),
                $equalMessage,
                0.5,
            ],
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
    public function test_assert_equals_fails($expected, $actual, $message, $delta = 0.0)
    {
        $this->setExpectedException(
            'SebastianBergmann\\Comparator\\ComparisonFailure', $message
        );
        $this->comparator->assertEquals($expected, $actual, $delta);
    }
}
