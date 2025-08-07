<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\AcceptHeaderItem;

class AcceptHeaderItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFromStringData
     */
    public function test_from_string($string, $value, array $attributes)
    {
        $item = AcceptHeaderItem::fromString($string);
        $this->assertEquals($value, $item->getValue());
        $this->assertEquals($attributes, $item->getAttributes());
    }

    public function provideFromStringData()
    {
        return [
            [
                'text/html',
                'text/html', [],
            ],
            [
                '"this;should,not=matter"',
                'this;should,not=matter', [],
            ],
            [
                "text/plain; charset=utf-8;param=\"this;should,not=matter\";\tfootnotes=true",
                'text/plain', ['charset' => 'utf-8', 'param' => 'this;should,not=matter', 'footnotes' => 'true'],
            ],
            [
                '"this;should,not=matter";charset=utf-8',
                'this;should,not=matter', ['charset' => 'utf-8'],
            ],
        ];
    }

    /**
     * @dataProvider provideToStringData
     */
    public function test_to_string($value, array $attributes, $string)
    {
        $item = new AcceptHeaderItem($value, $attributes);
        $this->assertEquals($string, (string) $item);
    }

    public function provideToStringData()
    {
        return [
            [
                'text/html', [],
                'text/html',
            ],
            [
                'text/plain', ['charset' => 'utf-8', 'param' => 'this;should,not=matter', 'footnotes' => 'true'],
                'text/plain;charset=utf-8;param="this;should,not=matter";footnotes=true',
            ],
        ];
    }

    public function test_value()
    {
        $item = new AcceptHeaderItem('value', []);
        $this->assertEquals('value', $item->getValue());

        $item->setValue('new value');
        $this->assertEquals('new value', $item->getValue());

        $item->setValue(1);
        $this->assertEquals('1', $item->getValue());
    }

    public function test_quality()
    {
        $item = new AcceptHeaderItem('value', []);
        $this->assertEquals(1.0, $item->getQuality());

        $item->setQuality(0.5);
        $this->assertEquals(0.5, $item->getQuality());

        $item->setAttribute('q', 0.75);
        $this->assertEquals(0.75, $item->getQuality());
        $this->assertFalse($item->hasAttribute('q'));
    }

    public function test_attribute()
    {
        $item = new AcceptHeaderItem('value', []);
        $this->assertEquals([], $item->getAttributes());
        $this->assertFalse($item->hasAttribute('test'));
        $this->assertNull($item->getAttribute('test'));
        $this->assertEquals('default', $item->getAttribute('test', 'default'));

        $item->setAttribute('test', 'value');
        $this->assertEquals(['test' => 'value'], $item->getAttributes());
        $this->assertTrue($item->hasAttribute('test'));
        $this->assertEquals('value', $item->getAttribute('test'));
        $this->assertEquals('value', $item->getAttribute('test', 'default'));
    }
}
