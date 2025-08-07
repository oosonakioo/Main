<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Flash;

use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag as FlashBag;

/**
 * AutoExpireFlashBagTest.
 *
 * @author Drak <drak@zikula.org>
 */
class AutoExpireFlashBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag
     */
    private $bag;

    /**
     * @var array
     */
    protected $array = [];

    protected function setUp()
    {
        parent::setUp();
        $this->bag = new FlashBag;
        $this->array = ['new' => ['notice' => ['A previous flash message']]];
        $this->bag->initialize($this->array);
    }

    protected function tearDown()
    {
        $this->bag = null;
        parent::tearDown();
    }

    public function test_initialize()
    {
        $bag = new FlashBag;
        $array = ['new' => ['notice' => ['A previous flash message']]];
        $bag->initialize($array);
        $this->assertEquals(['A previous flash message'], $bag->peek('notice'));
        $array = ['new' => [
            'notice' => ['Something else'],
            'error' => ['a'],
        ]];
        $bag->initialize($array);
        $this->assertEquals(['Something else'], $bag->peek('notice'));
        $this->assertEquals(['a'], $bag->peek('error'));
    }

    public function test_get_storage_key()
    {
        $this->assertEquals('_sf2_flashes', $this->bag->getStorageKey());
        $attributeBag = new FlashBag('test');
        $this->assertEquals('test', $attributeBag->getStorageKey());
    }

    public function test_get_set_name()
    {
        $this->assertEquals('flashes', $this->bag->getName());
        $this->bag->setName('foo');
        $this->assertEquals('foo', $this->bag->getName());
    }

    public function test_peek()
    {
        $this->assertEquals([], $this->bag->peek('non_existing'));
        $this->assertEquals(['default'], $this->bag->peek('non_existing', ['default']));
        $this->assertEquals(['A previous flash message'], $this->bag->peek('notice'));
        $this->assertEquals(['A previous flash message'], $this->bag->peek('notice'));
    }

    public function test_set()
    {
        $this->bag->set('notice', 'Foo');
        $this->assertEquals(['A previous flash message'], $this->bag->peek('notice'));
    }

    public function test_has()
    {
        $this->assertFalse($this->bag->has('nothing'));
        $this->assertTrue($this->bag->has('notice'));
    }

    public function test_keys()
    {
        $this->assertEquals(['notice'], $this->bag->keys());
    }

    public function test_peek_all()
    {
        $array = [
            'new' => [
                'notice' => 'Foo',
                'error' => 'Bar',
            ],
        ];

        $this->bag->initialize($array);
        $this->assertEquals([
            'notice' => 'Foo',
            'error' => 'Bar',
        ], $this->bag->peekAll()
        );

        $this->assertEquals([
            'notice' => 'Foo',
            'error' => 'Bar',
        ], $this->bag->peekAll()
        );
    }

    public function test_get()
    {
        $this->assertEquals([], $this->bag->get('non_existing'));
        $this->assertEquals(['default'], $this->bag->get('non_existing', ['default']));
        $this->assertEquals(['A previous flash message'], $this->bag->get('notice'));
        $this->assertEquals([], $this->bag->get('notice'));
    }

    public function test_set_all()
    {
        $this->bag->setAll(['a' => 'first', 'b' => 'second']);
        $this->assertFalse($this->bag->has('a'));
        $this->assertFalse($this->bag->has('b'));
    }

    public function test_all()
    {
        $this->bag->set('notice', 'Foo');
        $this->bag->set('error', 'Bar');
        $this->assertEquals([
            'notice' => ['A previous flash message'],
        ], $this->bag->all()
        );

        $this->assertEquals([], $this->bag->all());
    }

    public function test_clear()
    {
        $this->assertEquals(['notice' => ['A previous flash message']], $this->bag->clear());
    }
}
