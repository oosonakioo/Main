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

use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * FlashBagTest.
 *
 * @author Drak <drak@zikula.org>
 */
class FlashBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
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
        $this->array = ['notice' => ['A previous flash message']];
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
        $bag->initialize($this->array);
        $this->assertEquals($this->array, $bag->peekAll());
        $array = ['should' => ['change']];
        $bag->initialize($array);
        $this->assertEquals($array, $bag->peekAll());
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
        $this->assertEquals(['default'], $this->bag->peek('not_existing', ['default']));
        $this->assertEquals(['A previous flash message'], $this->bag->peek('notice'));
        $this->assertEquals(['A previous flash message'], $this->bag->peek('notice'));
    }

    public function test_get()
    {
        $this->assertEquals([], $this->bag->get('non_existing'));
        $this->assertEquals(['default'], $this->bag->get('not_existing', ['default']));
        $this->assertEquals(['A previous flash message'], $this->bag->get('notice'));
        $this->assertEquals([], $this->bag->get('notice'));
    }

    public function test_all()
    {
        $this->bag->set('notice', 'Foo');
        $this->bag->set('error', 'Bar');
        $this->assertEquals([
            'notice' => ['Foo'],
            'error' => ['Bar'], ], $this->bag->all()
        );

        $this->assertEquals([], $this->bag->all());
    }

    public function test_set()
    {
        $this->bag->set('notice', 'Foo');
        $this->bag->set('notice', 'Bar');
        $this->assertEquals(['Bar'], $this->bag->peek('notice'));
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
        $this->bag->set('notice', 'Foo');
        $this->bag->set('error', 'Bar');
        $this->assertEquals([
            'notice' => ['Foo'],
            'error' => ['Bar'],
        ], $this->bag->peekAll()
        );
        $this->assertTrue($this->bag->has('notice'));
        $this->assertTrue($this->bag->has('error'));
        $this->assertEquals([
            'notice' => ['Foo'],
            'error' => ['Bar'],
        ], $this->bag->peekAll()
        );
    }
}
