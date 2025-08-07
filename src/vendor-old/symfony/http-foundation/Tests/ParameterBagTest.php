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

use Symfony\Component\HttpFoundation\ParameterBag;

class ParameterBagTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor()
    {
        $this->testAll();
    }

    public function test_all()
    {
        $bag = new ParameterBag(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $bag->all(), '->all() gets all the input');
    }

    public function test_keys()
    {
        $bag = new ParameterBag(['foo' => 'bar']);
        $this->assertEquals(['foo'], $bag->keys());
    }

    public function test_add()
    {
        $bag = new ParameterBag(['foo' => 'bar']);
        $bag->add(['bar' => 'bas']);
        $this->assertEquals(['foo' => 'bar', 'bar' => 'bas'], $bag->all());
    }

    public function test_remove()
    {
        $bag = new ParameterBag(['foo' => 'bar']);
        $bag->add(['bar' => 'bas']);
        $this->assertEquals(['foo' => 'bar', 'bar' => 'bas'], $bag->all());
        $bag->remove('bar');
        $this->assertEquals(['foo' => 'bar'], $bag->all());
    }

    public function test_replace()
    {
        $bag = new ParameterBag(['foo' => 'bar']);

        $bag->replace(['FOO' => 'BAR']);
        $this->assertEquals(['FOO' => 'BAR'], $bag->all(), '->replace() replaces the input with the argument');
        $this->assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }

    public function test_get()
    {
        $bag = new ParameterBag(['foo' => 'bar', 'null' => null]);

        $this->assertEquals('bar', $bag->get('foo'), '->get() gets the value of a parameter');
        $this->assertEquals('default', $bag->get('unknown', 'default'), '->get() returns second argument as default if a parameter is not defined');
        $this->assertNull($bag->get('null', 'default'), '->get() returns null if null is set');
    }

    public function test_get_does_not_use_deep_by_default()
    {
        $bag = new ParameterBag(['foo' => ['bar' => 'moo']]);

        $this->assertNull($bag->get('foo[bar]'));
    }

    public function test_set()
    {
        $bag = new ParameterBag([]);

        $bag->set('foo', 'bar');
        $this->assertEquals('bar', $bag->get('foo'), '->set() sets the value of parameter');

        $bag->set('foo', 'baz');
        $this->assertEquals('baz', $bag->get('foo'), '->set() overrides previously set parameter');
    }

    public function test_has()
    {
        $bag = new ParameterBag(['foo' => 'bar']);

        $this->assertTrue($bag->has('foo'), '->has() returns true if a parameter is defined');
        $this->assertFalse($bag->has('unknown'), '->has() return false if a parameter is not defined');
    }

    public function test_get_alpha()
    {
        $bag = new ParameterBag(['word' => 'foo_BAR_012']);

        $this->assertEquals('fooBAR', $bag->getAlpha('word'), '->getAlpha() gets only alphabetic characters');
        $this->assertEquals('', $bag->getAlpha('unknown'), '->getAlpha() returns empty string if a parameter is not defined');
    }

    public function test_get_alnum()
    {
        $bag = new ParameterBag(['word' => 'foo_BAR_012']);

        $this->assertEquals('fooBAR012', $bag->getAlnum('word'), '->getAlnum() gets only alphanumeric characters');
        $this->assertEquals('', $bag->getAlnum('unknown'), '->getAlnum() returns empty string if a parameter is not defined');
    }

    public function test_get_digits()
    {
        $bag = new ParameterBag(['word' => 'foo_BAR_012']);

        $this->assertEquals('012', $bag->getDigits('word'), '->getDigits() gets only digits as string');
        $this->assertEquals('', $bag->getDigits('unknown'), '->getDigits() returns empty string if a parameter is not defined');
    }

    public function test_get_int()
    {
        $bag = new ParameterBag(['digits' => '0123']);

        $this->assertEquals(123, $bag->getInt('digits'), '->getInt() gets a value of parameter as integer');
        $this->assertEquals(0, $bag->getInt('unknown'), '->getInt() returns zero if a parameter is not defined');
    }

    public function test_filter()
    {
        $bag = new ParameterBag([
            'digits' => '0123ab',
            'email' => 'example@example.com',
            'url' => 'http://example.com/foo',
            'dec' => '256',
            'hex' => '0x100',
            'array' => ['bang'],
        ]);

        $this->assertEmpty($bag->filter('nokey'), '->filter() should return empty by default if no key is found');

        $this->assertEquals('0123', $bag->filter('digits', '', FILTER_SANITIZE_NUMBER_INT), '->filter() gets a value of parameter as integer filtering out invalid characters');

        $this->assertEquals('example@example.com', $bag->filter('email', '', FILTER_VALIDATE_EMAIL), '->filter() gets a value of parameter as email');

        $this->assertEquals('http://example.com/foo', $bag->filter('url', '', FILTER_VALIDATE_URL, ['flags' => FILTER_FLAG_PATH_REQUIRED]), '->filter() gets a value of parameter as URL with a path');

        // This test is repeated for code-coverage
        $this->assertEquals('http://example.com/foo', $bag->filter('url', '', FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED), '->filter() gets a value of parameter as URL with a path');

        $this->assertFalse($bag->filter('dec', '', FILTER_VALIDATE_INT, [
            'flags' => FILTER_FLAG_ALLOW_HEX,
            'options' => ['min_range' => 1, 'max_range' => 0xFF],
        ]), '->filter() gets a value of parameter as integer between boundaries');

        $this->assertFalse($bag->filter('hex', '', FILTER_VALIDATE_INT, [
            'flags' => FILTER_FLAG_ALLOW_HEX,
            'options' => ['min_range' => 1, 'max_range' => 0xFF],
        ]), '->filter() gets a value of parameter as integer between boundaries');

        $this->assertEquals(['bang'], $bag->filter('array', ''), '->filter() gets a value of parameter as an array');
    }

    public function test_get_iterator()
    {
        $parameters = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new ParameterBag($parameters);

        $i = 0;
        foreach ($bag as $key => $val) {
            $i++;
            $this->assertEquals($parameters[$key], $val);
        }

        $this->assertEquals(count($parameters), $i);
    }

    public function test_count()
    {
        $parameters = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new ParameterBag($parameters);

        $this->assertEquals(count($parameters), count($bag));
    }

    public function test_get_boolean()
    {
        $parameters = ['string_true' => 'true', 'string_false' => 'false'];
        $bag = new ParameterBag($parameters);

        $this->assertTrue($bag->getBoolean('string_true'), '->getBoolean() gets the string true as boolean true');
        $this->assertFalse($bag->getBoolean('string_false'), '->getBoolean() gets the string false as boolean false');
        $this->assertFalse($bag->getBoolean('unknown'), '->getBoolean() returns false if a parameter is not defined');
    }
}
