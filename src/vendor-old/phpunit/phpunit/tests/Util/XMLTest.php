<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @since      Class available since Release 3.3.0
 *
 * @covers     PHPUnit_Util_XML
 */
class Util_XMLTest extends PHPUnit_Framework_TestCase
{
    public function test_assert_valid_keys_valid_keys()
    {
        $options = ['testA' => 1, 'testB' => 2, 'testC' => 3];
        $valid = ['testA', 'testB', 'testC'];
        $expected = ['testA' => 1, 'testB' => 2, 'testC' => 3];
        $validated = PHPUnit_Util_XML::assertValidKeys($options, $valid);

        $this->assertEquals($expected, $validated);
    }

    public function test_assert_valid_keys_valid_keys_empty()
    {
        $options = ['testA' => 1, 'testB' => 2];
        $valid = ['testA', 'testB', 'testC'];
        $expected = ['testA' => 1, 'testB' => 2, 'testC' => null];
        $validated = PHPUnit_Util_XML::assertValidKeys($options, $valid);

        $this->assertEquals($expected, $validated);
    }

    public function test_assert_valid_keys_default_values_a()
    {
        $options = ['testA' => 1, 'testB' => 2];
        $valid = ['testA' => 23, 'testB' => 24, 'testC' => 25];
        $expected = ['testA' => 1, 'testB' => 2, 'testC' => 25];
        $validated = PHPUnit_Util_XML::assertValidKeys($options, $valid);

        $this->assertEquals($expected, $validated);
    }

    public function test_assert_valid_keys_default_values_b()
    {
        $options = [];
        $valid = ['testA' => 23, 'testB' => 24, 'testC' => 25];
        $expected = ['testA' => 23, 'testB' => 24, 'testC' => 25];
        $validated = PHPUnit_Util_XML::assertValidKeys($options, $valid);

        $this->assertEquals($expected, $validated);
    }

    public function test_assert_valid_keys_invalid_key()
    {
        $options = ['testA' => 1, 'testB' => 2, 'testD' => 3];
        $valid = ['testA', 'testB', 'testC'];

        try {
            $validated = PHPUnit_Util_XML::assertValidKeys($options, $valid);
            $this->fail();
        } catch (PHPUnit_Framework_Exception $e) {
            $this->assertEquals('Unknown key(s): testD', $e->getMessage());
        }
    }

    public function test_assert_valid_keys_invalid_keys()
    {
        $options = ['testA' => 1, 'testD' => 2, 'testE' => 3];
        $valid = ['testA', 'testB', 'testC'];

        try {
            $validated = PHPUnit_Util_XML::assertValidKeys($options, $valid);
            $this->fail();
        } catch (PHPUnit_Framework_Exception $e) {
            $this->assertEquals('Unknown key(s): testD, testE', $e->getMessage());
        }
    }

    public function test_convert_assert_select()
    {
        $selector = 'div#folder.open a[href="http://www.xerox.com"][title="xerox"].selected.big > span + h1';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div',
            'id' => 'folder',
            'class' => 'open',
            'descendant' => ['tag' => 'a',
                'class' => 'selected big',
                'attributes' => ['href' => 'http://www.xerox.com',
                    'title' => 'xerox'],
                'child' => ['tag' => 'span',
                    'adjacent-sibling' => ['tag' => 'h1']]]];
        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt()
    {
        $selector = 'div';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_class()
    {
        $selector = '.foo';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['class' => 'foo'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_id()
    {
        $selector = '#foo';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['id' => 'foo'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_attribute()
    {
        $selector = '[foo="bar"]';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['attributes' => ['foo' => 'bar']];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_attribute_spaces()
    {
        $selector = '[foo="bar baz"] div[value="foo bar"]';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['attributes' => ['foo' => 'bar baz'],
            'descendant' => ['tag' => 'div',
                'attributes' => ['value' => 'foo bar']]];
        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_attribute_multiple_spaces()
    {
        $selector = '[foo="bar baz"] div[value="foo bar baz"]';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['attributes' => ['foo' => 'bar baz'],
            'descendant' => ['tag' => 'div',
                'attributes' => ['value' => 'foo bar baz']]];
        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_class()
    {
        $selector = 'div.foo';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'class' => 'foo'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_id()
    {
        $selector = 'div#foo';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'id' => 'foo'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_attr_equal()
    {
        $selector = 'div[foo="bar"]';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'attributes' => ['foo' => 'bar']];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_multi_attr_equal()
    {
        $selector = 'div[foo="bar"][baz="fob"]';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'attributes' => ['foo' => 'bar', 'baz' => 'fob']];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_attr_has_one()
    {
        $selector = 'div[foo~="bar"]';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'attributes' => ['foo' => 'regexp:/.*\bbar\b.*/']];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_attr_contains()
    {
        $selector = 'div[foo*="bar"]';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'attributes' => ['foo' => 'regexp:/.*bar.*/']];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_child()
    {
        $selector = 'div > a';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'child' => ['tag' => 'a']];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_adjacent_sibling()
    {
        $selector = 'div + a';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'adjacent-sibling' => ['tag' => 'a']];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_elt_descendant()
    {
        $selector = 'div a';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector);
        $tag = ['tag' => 'div', 'descendant' => ['tag' => 'a']];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_content()
    {
        $selector = '#foo';
        $content = 'div contents';
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector, $content);
        $tag = ['id' => 'foo', 'content' => 'div contents'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_true()
    {
        $selector = '#foo';
        $content = true;
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector, $content);
        $tag = ['id' => 'foo'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_select_false()
    {
        $selector = '#foo';
        $content = false;
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector, $content);
        $tag = ['id' => 'foo'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_number()
    {
        $selector = '.foo';
        $content = 3;
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector, $content);
        $tag = ['class' => 'foo'];

        $this->assertEquals($tag, $converted);
    }

    public function test_convert_assert_range()
    {
        $selector = '#foo';
        $content = ['greater_than' => 5, 'less_than' => 10];
        $converted = PHPUnit_Util_XML::convertSelectToTag($selector, $content);
        $tag = ['id' => 'foo'];

        $this->assertEquals($tag, $converted);
    }

    /**
     * @dataProvider charProvider
     */
    public function test_prepare_string($char)
    {
        $e = null;

        $escapedString = PHPUnit_Util_XML::prepareString($char);
        $xml = "<?xml version='1.0' encoding='UTF-8' ?><tag>$escapedString</tag>";
        $dom = new DomDocument('1.0', 'UTF-8');

        try {
            $dom->loadXML($xml);
        } catch (Exception $e) {
        }

        $this->assertNull($e, sprintf(
            'PHPUnit_Util_XML::prepareString("\x%02x") should not crash DomDocument',
            ord($char)
        ));
    }

    public function charProvider()
    {
        $data = [];

        for ($i = 0; $i < 256; $i++) {
            $data[] = [chr($i)];
        }

        return $data;
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     *
     * @expectedExceptionMessage Could not load XML from empty string
     */
    public function test_load_empty_string()
    {
        PHPUnit_Util_XML::load('');
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     *
     * @expectedExceptionMessage Could not load XML from array
     */
    public function test_load_array()
    {
        PHPUnit_Util_XML::load([1, 2, 3]);
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     *
     * @expectedExceptionMessage Could not load XML from boolean
     */
    public function test_load_boolean()
    {
        PHPUnit_Util_XML::load(false);
    }

    public function test_nested_xml_to_variable()
    {
        $xml = '<array><element key="a"><array><element key="b"><string>foo</string></element></array></element><element key="c"><string>bar</string></element></array>';
        $dom = new DOMDocument;
        $dom->loadXML($xml);

        $expected = [
            'a' => [
                'b' => 'foo',
            ],
            'c' => 'bar',
        ];

        $actual = PHPUnit_Util_XML::xmlToVariable($dom->documentElement);

        $this->assertSame($expected, $actual);
    }
}
