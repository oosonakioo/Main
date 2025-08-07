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

use DOMDocument;
use DOMNode;

/**
 * @coversDefaultClass SebastianBergmann\Comparator\DOMNodeComparator
 */
class DOMNodeComparatorTest extends \PHPUnit_Framework_TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new DOMNodeComparator;
    }

    public function acceptsSucceedsProvider()
    {
        $document = new DOMDocument;
        $node = new DOMNode;

        return [
            [$document, $document],
            [$node, $node],
            [$document, $node],
            [$node, $document],
        ];
    }

    public function acceptsFailsProvider()
    {
        $document = new DOMDocument;

        return [
            [$document, null],
            [null, $document],
            [null, null],
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        return [
            [
                $this->createDOMDocument('<root></root>'),
                $this->createDOMDocument('<root/>'),
            ],
            [
                $this->createDOMDocument('<root attr="bar"></root>'),
                $this->createDOMDocument('<root attr="bar"/>'),
            ],
            [
                $this->createDOMDocument('<root><foo attr="bar"></foo></root>'),
                $this->createDOMDocument('<root><foo attr="bar"/></root>'),
            ],
            [
                $this->createDOMDocument("<root>\n  <child/>\n</root>"),
                $this->createDOMDocument('<root><child/></root>'),
            ],
        ];
    }

    public function assertEqualsFailsProvider()
    {
        return [
            [
                $this->createDOMDocument('<root></root>'),
                $this->createDOMDocument('<bar/>'),
            ],
            [
                $this->createDOMDocument('<foo attr1="bar"/>'),
                $this->createDOMDocument('<foo attr1="foobar"/>'),
            ],
            [
                $this->createDOMDocument('<foo> bar </foo>'),
                $this->createDOMDocument('<foo />'),
            ],
            [
                $this->createDOMDocument('<foo xmlns="urn:myns:bar"/>'),
                $this->createDOMDocument('<foo xmlns="urn:notmyns:bar"/>'),
            ],
            [
                $this->createDOMDocument('<foo> bar </foo>'),
                $this->createDOMDocument('<foo> bir </foo>'),
            ],
        ];
    }

    private function createDOMDocument($content)
    {
        $document = new DOMDocument;
        $document->preserveWhiteSpace = false;
        $document->loadXML($content);

        return $document;
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
            'Failed asserting that two DOM'
        );
        $this->comparator->assertEquals($expected, $actual);
    }
}
