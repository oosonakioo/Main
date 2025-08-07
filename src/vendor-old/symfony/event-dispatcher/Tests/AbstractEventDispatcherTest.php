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
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractEventDispatcherTest extends TestCase
{
    /* Some pseudo events */
    const preFoo = 'pre.foo';

    const postFoo = 'post.foo';

    const preBar = 'pre.bar';

    const postBar = 'post.bar';

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    private $listener;

    protected function setUp()
    {
        $this->dispatcher = $this->createEventDispatcher();
        $this->listener = new TestEventListener;
    }

    protected function tearDown()
    {
        $this->dispatcher = null;
        $this->listener = null;
    }

    abstract protected function createEventDispatcher();

    public function test_initial_state()
    {
        $this->assertEquals([], $this->dispatcher->getListeners());
        $this->assertFalse($this->dispatcher->hasListeners(self::preFoo));
        $this->assertFalse($this->dispatcher->hasListeners(self::postFoo));
    }

    public function test_add_listener()
    {
        $this->dispatcher->addListener('pre.foo', [$this->listener, 'preFoo']);
        $this->dispatcher->addListener('post.foo', [$this->listener, 'postFoo']);
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertTrue($this->dispatcher->hasListeners(self::postFoo));
        $this->assertCount(1, $this->dispatcher->getListeners(self::preFoo));
        $this->assertCount(1, $this->dispatcher->getListeners(self::postFoo));
        $this->assertCount(2, $this->dispatcher->getListeners());
    }

    public function test_get_listeners_sorts_by_priority()
    {
        $listener1 = new TestEventListener;
        $listener2 = new TestEventListener;
        $listener3 = new TestEventListener;
        $listener1->name = '1';
        $listener2->name = '2';
        $listener3->name = '3';

        $this->dispatcher->addListener('pre.foo', [$listener1, 'preFoo'], -10);
        $this->dispatcher->addListener('pre.foo', [$listener2, 'preFoo'], 10);
        $this->dispatcher->addListener('pre.foo', [$listener3, 'preFoo']);

        $expected = [
            [$listener2, 'preFoo'],
            [$listener3, 'preFoo'],
            [$listener1, 'preFoo'],
        ];

        $this->assertSame($expected, $this->dispatcher->getListeners('pre.foo'));
    }

    public function test_get_all_listeners_sorts_by_priority()
    {
        $listener1 = new TestEventListener;
        $listener2 = new TestEventListener;
        $listener3 = new TestEventListener;
        $listener4 = new TestEventListener;
        $listener5 = new TestEventListener;
        $listener6 = new TestEventListener;

        $this->dispatcher->addListener('pre.foo', $listener1, -10);
        $this->dispatcher->addListener('pre.foo', $listener2);
        $this->dispatcher->addListener('pre.foo', $listener3, 10);
        $this->dispatcher->addListener('post.foo', $listener4, -10);
        $this->dispatcher->addListener('post.foo', $listener5);
        $this->dispatcher->addListener('post.foo', $listener6, 10);

        $expected = [
            'pre.foo' => [$listener3, $listener2, $listener1],
            'post.foo' => [$listener6, $listener5, $listener4],
        ];

        $this->assertSame($expected, $this->dispatcher->getListeners());
    }

    public function test_get_listener_priority()
    {
        $listener1 = new TestEventListener;
        $listener2 = new TestEventListener;

        $this->dispatcher->addListener('pre.foo', $listener1, -10);
        $this->dispatcher->addListener('pre.foo', $listener2);

        $this->assertSame(-10, $this->dispatcher->getListenerPriority('pre.foo', $listener1));
        $this->assertSame(0, $this->dispatcher->getListenerPriority('pre.foo', $listener2));
        $this->assertNull($this->dispatcher->getListenerPriority('pre.bar', $listener2));
        $this->assertNull($this->dispatcher->getListenerPriority('pre.foo', function () {}));
    }

    public function test_dispatch()
    {
        $this->dispatcher->addListener('pre.foo', [$this->listener, 'preFoo']);
        $this->dispatcher->addListener('post.foo', [$this->listener, 'postFoo']);
        $this->dispatcher->dispatch(self::preFoo);
        $this->assertTrue($this->listener->preFooInvoked);
        $this->assertFalse($this->listener->postFooInvoked);
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->dispatcher->dispatch('noevent'));
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->dispatcher->dispatch(self::preFoo));
        $event = new Event;
        $return = $this->dispatcher->dispatch(self::preFoo, $event);
        $this->assertSame($event, $return);
    }

    public function test_dispatch_for_closure()
    {
        $invoked = 0;
        $listener = function () use (&$invoked) {
            $invoked++;
        };
        $this->dispatcher->addListener('pre.foo', $listener);
        $this->dispatcher->addListener('post.foo', $listener);
        $this->dispatcher->dispatch(self::preFoo);
        $this->assertEquals(1, $invoked);
    }

    public function test_stop_event_propagation()
    {
        $otherListener = new TestEventListener;

        // postFoo() stops the propagation, so only one listener should
        // be executed
        // Manually set priority to enforce $this->listener to be called first
        $this->dispatcher->addListener('post.foo', [$this->listener, 'postFoo'], 10);
        $this->dispatcher->addListener('post.foo', [$otherListener, 'preFoo']);
        $this->dispatcher->dispatch(self::postFoo);
        $this->assertTrue($this->listener->postFooInvoked);
        $this->assertFalse($otherListener->postFooInvoked);
    }

    public function test_dispatch_by_priority()
    {
        $invoked = [];
        $listener1 = function () use (&$invoked) {
            $invoked[] = '1';
        };
        $listener2 = function () use (&$invoked) {
            $invoked[] = '2';
        };
        $listener3 = function () use (&$invoked) {
            $invoked[] = '3';
        };
        $this->dispatcher->addListener('pre.foo', $listener1, -10);
        $this->dispatcher->addListener('pre.foo', $listener2);
        $this->dispatcher->addListener('pre.foo', $listener3, 10);
        $this->dispatcher->dispatch(self::preFoo);
        $this->assertEquals(['3', '2', '1'], $invoked);
    }

    public function test_remove_listener()
    {
        $this->dispatcher->addListener('pre.bar', $this->listener);
        $this->assertTrue($this->dispatcher->hasListeners(self::preBar));
        $this->dispatcher->removeListener('pre.bar', $this->listener);
        $this->assertFalse($this->dispatcher->hasListeners(self::preBar));
        $this->dispatcher->removeListener('notExists', $this->listener);
    }

    public function test_add_subscriber()
    {
        $eventSubscriber = new TestEventSubscriber;
        $this->dispatcher->addSubscriber($eventSubscriber);
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertTrue($this->dispatcher->hasListeners(self::postFoo));
    }

    public function test_add_subscriber_with_priorities()
    {
        $eventSubscriber = new TestEventSubscriber;
        $this->dispatcher->addSubscriber($eventSubscriber);

        $eventSubscriber = new TestEventSubscriberWithPriorities;
        $this->dispatcher->addSubscriber($eventSubscriber);

        $listeners = $this->dispatcher->getListeners('pre.foo');
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertCount(2, $listeners);
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Tests\TestEventSubscriberWithPriorities', $listeners[0][0]);
    }

    public function test_add_subscriber_with_multiple_listeners()
    {
        $eventSubscriber = new TestEventSubscriberWithMultipleListeners;
        $this->dispatcher->addSubscriber($eventSubscriber);

        $listeners = $this->dispatcher->getListeners('pre.foo');
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertCount(2, $listeners);
        $this->assertEquals('preFoo2', $listeners[0][1]);
    }

    public function test_remove_subscriber()
    {
        $eventSubscriber = new TestEventSubscriber;
        $this->dispatcher->addSubscriber($eventSubscriber);
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertTrue($this->dispatcher->hasListeners(self::postFoo));
        $this->dispatcher->removeSubscriber($eventSubscriber);
        $this->assertFalse($this->dispatcher->hasListeners(self::preFoo));
        $this->assertFalse($this->dispatcher->hasListeners(self::postFoo));
    }

    public function test_remove_subscriber_with_priorities()
    {
        $eventSubscriber = new TestEventSubscriberWithPriorities;
        $this->dispatcher->addSubscriber($eventSubscriber);
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->dispatcher->removeSubscriber($eventSubscriber);
        $this->assertFalse($this->dispatcher->hasListeners(self::preFoo));
    }

    public function test_remove_subscriber_with_multiple_listeners()
    {
        $eventSubscriber = new TestEventSubscriberWithMultipleListeners;
        $this->dispatcher->addSubscriber($eventSubscriber);
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertCount(2, $this->dispatcher->getListeners(self::preFoo));
        $this->dispatcher->removeSubscriber($eventSubscriber);
        $this->assertFalse($this->dispatcher->hasListeners(self::preFoo));
    }

    public function test_event_receives_the_dispatcher_instance_as_argument()
    {
        $listener = new TestWithDispatcher;
        $this->dispatcher->addListener('test', [$listener, 'foo']);
        $this->assertNull($listener->name);
        $this->assertNull($listener->dispatcher);
        $this->dispatcher->dispatch('test');
        $this->assertEquals('test', $listener->name);
        $this->assertSame($this->dispatcher, $listener->dispatcher);
    }

    /**
     * @see https://bugs.php.net/bug.php?id=62976
     *
     * This bug affects:
     *  - The PHP 5.3 branch for versions < 5.3.18
     *  - The PHP 5.4 branch for versions < 5.4.8
     *  - The PHP 5.5 branch is not affected
     */
    public function test_workaround_for_php_bug62976()
    {
        $dispatcher = $this->createEventDispatcher();
        $dispatcher->addListener('bug.62976', new CallableClass);
        $dispatcher->removeListener('bug.62976', function () {});
        $this->assertTrue($dispatcher->hasListeners('bug.62976'));
    }

    public function test_has_listeners_when_added_callback_listener_is_removed()
    {
        $listener = function () {};
        $this->dispatcher->addListener('foo', $listener);
        $this->dispatcher->removeListener('foo', $listener);
        $this->assertFalse($this->dispatcher->hasListeners());
    }

    public function test_get_listeners_when_added_callback_listener_is_removed()
    {
        $listener = function () {};
        $this->dispatcher->addListener('foo', $listener);
        $this->dispatcher->removeListener('foo', $listener);
        $this->assertSame([], $this->dispatcher->getListeners());
    }

    public function test_has_listeners_without_events_returns_false_after_has_listeners_with_event_has_been_called()
    {
        $this->assertFalse($this->dispatcher->hasListeners('foo'));
        $this->assertFalse($this->dispatcher->hasListeners());
    }
}

class CallableClass
{
    public function __invoke() {}
}

class TestEventListener
{
    public $preFooInvoked = false;

    public $postFooInvoked = false;

    /* Listener methods */

    public function preFoo(Event $e)
    {
        $this->preFooInvoked = true;
    }

    public function postFoo(Event $e)
    {
        $this->postFooInvoked = true;

        $e->stopPropagation();
    }
}

class TestWithDispatcher
{
    public $name;

    public $dispatcher;

    public function foo(Event $e, $name, $dispatcher)
    {
        $this->name = $name;
        $this->dispatcher = $dispatcher;
    }
}

class TestEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['pre.foo' => 'preFoo', 'post.foo' => 'postFoo'];
    }
}

class TestEventSubscriberWithPriorities implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'pre.foo' => ['preFoo', 10],
            'post.foo' => ['postFoo'],
        ];
    }
}

class TestEventSubscriberWithMultipleListeners implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['pre.foo' => [
            ['preFoo1'],
            ['preFoo2', 10],
        ]];
    }
}
