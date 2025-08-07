<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Test class for Event.
 */
class GenericEventTest extends TestCase
{
    /**
     * @var GenericEvent
     */
    private $event;

    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new \stdClass;
        $this->event = new GenericEvent($this->subject, ['name' => 'Event']);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->subject = null;
        $this->event = null;

        parent::tearDown();
    }

    public function test_construct()
    {
        $this->assertEquals($this->event, new GenericEvent($this->subject, ['name' => 'Event']));
    }

    /**
     * Tests Event->getArgs().
     */
    public function test_get_arguments()
    {
        // test getting all
        $this->assertSame(['name' => 'Event'], $this->event->getArguments());
    }

    public function test_set_arguments()
    {
        $result = $this->event->setArguments(['foo' => 'bar']);
        $this->assertAttributeSame(['foo' => 'bar'], 'arguments', $this->event);
        $this->assertSame($this->event, $result);
    }

    public function test_set_argument()
    {
        $result = $this->event->setArgument('foo2', 'bar2');
        $this->assertAttributeSame(['name' => 'Event', 'foo2' => 'bar2'], 'arguments', $this->event);
        $this->assertEquals($this->event, $result);
    }

    public function test_get_argument()
    {
        // test getting key
        $this->assertEquals('Event', $this->event->getArgument('name'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_get_arg_exception()
    {
        $this->event->getArgument('nameNotExist');
    }

    public function test_offset_get()
    {
        // test getting key
        $this->assertEquals('Event', $this->event['name']);

        // test getting invalid arg
        $this->{method_exists($this, $_ = 'expectException') ? $_ : 'setExpectedException'}('InvalidArgumentException');
        $this->assertFalse($this->event['nameNotExist']);
    }

    public function test_offset_set()
    {
        $this->event['foo2'] = 'bar2';
        $this->assertAttributeSame(['name' => 'Event', 'foo2' => 'bar2'], 'arguments', $this->event);
    }

    public function test_offset_unset()
    {
        unset($this->event['name']);
        $this->assertAttributeSame([], 'arguments', $this->event);
    }

    public function test_offset_isset()
    {
        $this->assertTrue(isset($this->event['name']));
        $this->assertFalse(isset($this->event['nameNotExist']));
    }

    public function test_has_argument()
    {
        $this->assertTrue($this->event->hasArgument('name'));
        $this->assertFalse($this->event->hasArgument('nameNotExist'));
    }

    public function test_get_subject()
    {
        $this->assertSame($this->subject, $this->event->getSubject());
    }

    public function test_has_iterator()
    {
        $data = [];
        foreach ($this->event as $key => $value) {
            $data[$key] = $value;
        }
        $this->assertEquals(['name' => 'Event'], $data);
    }
}
