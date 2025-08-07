<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Attribute;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

/**
 * Tests AttributeBag.
 *
 * @author Drak <drak@zikula.org>
 */
class AttributeBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $array;

    /**
     * @var AttributeBag
     */
    private $bag;

    protected function setUp()
    {
        $this->array = [
            'hello' => 'world',
            'always' => 'be happy',
            'user.login' => 'drak',
            'csrf.token' => [
                'a' => '1234',
                'b' => '4321',
            ],
            'category' => [
                'fishing' => [
                    'first' => 'cod',
                    'second' => 'sole',
                ],
            ],
        ];
        $this->bag = new AttributeBag('_sf2');
        $this->bag->initialize($this->array);
    }

    protected function tearDown()
    {
        $this->bag = null;
        $this->array = [];
    }

    public function test_initialize()
    {
        $bag = new AttributeBag;
        $bag->initialize($this->array);
        $this->assertEquals($this->array, $bag->all());
        $array = ['should' => 'change'];
        $bag->initialize($array);
        $this->assertEquals($array, $bag->all());
    }

    public function test_get_storage_key()
    {
        $this->assertEquals('_sf2', $this->bag->getStorageKey());
        $attributeBag = new AttributeBag('test');
        $this->assertEquals('test', $attributeBag->getStorageKey());
    }

    public function test_get_set_name()
    {
        $this->assertEquals('attributes', $this->bag->getName());
        $this->bag->setName('foo');
        $this->assertEquals('foo', $this->bag->getName());
    }

    /**
     * @dataProvider attributesProvider
     */
    public function test_has($key, $value, $exists)
    {
        $this->assertEquals($exists, $this->bag->has($key));
    }

    /**
     * @dataProvider attributesProvider
     */
    public function test_get($key, $value, $expected)
    {
        $this->assertEquals($value, $this->bag->get($key));
    }

    public function test_get_defaults()
    {
        $this->assertNull($this->bag->get('user2.login'));
        $this->assertEquals('default', $this->bag->get('user2.login', 'default'));
    }

    /**
     * @dataProvider attributesProvider
     */
    public function test_set($key, $value, $expected)
    {
        $this->bag->set($key, $value);
        $this->assertEquals($value, $this->bag->get($key));
    }

    public function test_all()
    {
        $this->assertEquals($this->array, $this->bag->all());

        $this->bag->set('hello', 'fabien');
        $array = $this->array;
        $array['hello'] = 'fabien';
        $this->assertEquals($array, $this->bag->all());
    }

    public function test_replace()
    {
        $array = [];
        $array['name'] = 'jack';
        $array['foo.bar'] = 'beep';
        $this->bag->replace($array);
        $this->assertEquals($array, $this->bag->all());
        $this->assertNull($this->bag->get('hello'));
        $this->assertNull($this->bag->get('always'));
        $this->assertNull($this->bag->get('user.login'));
    }

    public function test_remove()
    {
        $this->assertEquals('world', $this->bag->get('hello'));
        $this->bag->remove('hello');
        $this->assertNull($this->bag->get('hello'));

        $this->assertEquals('be happy', $this->bag->get('always'));
        $this->bag->remove('always');
        $this->assertNull($this->bag->get('always'));

        $this->assertEquals('drak', $this->bag->get('user.login'));
        $this->bag->remove('user.login');
        $this->assertNull($this->bag->get('user.login'));
    }

    public function test_clear()
    {
        $this->bag->clear();
        $this->assertEquals([], $this->bag->all());
    }

    public function attributesProvider()
    {
        return [
            ['hello', 'world', true],
            ['always', 'be happy', true],
            ['user.login', 'drak', true],
            ['csrf.token', ['a' => '1234', 'b' => '4321'], true],
            ['category', ['fishing' => ['first' => 'cod', 'second' => 'sole']], true],
            ['user2.login', null, false],
            ['never', null, false],
            ['bye', null, false],
            ['bye/for/now', null, false],
        ];
    }

    public function test_get_iterator()
    {
        $i = 0;
        foreach ($this->bag as $key => $val) {
            $this->assertEquals($this->array[$key], $val);
            $i++;
        }

        $this->assertEquals(count($this->array), $i);
    }

    public function test_count()
    {
        $this->assertEquals(count($this->array), count($this->bag));
    }
}
