<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TraceableEventDispatcherTest extends TestCase
{
    public function test_add_remove_listener()
    {
        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch);

        $tdispatcher->addListener('foo', $listener = function () {});
        $listeners = $dispatcher->getListeners('foo');
        $this->assertCount(1, $listeners);
        $this->assertSame($listener, $listeners[0]);

        $tdispatcher->removeListener('foo', $listener);
        $this->assertCount(0, $dispatcher->getListeners('foo'));
    }

    public function test_get_listeners()
    {
        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch);

        $tdispatcher->addListener('foo', $listener = function () {});
        $this->assertSame($dispatcher->getListeners('foo'), $tdispatcher->getListeners('foo'));
    }

    public function test_has_listeners()
    {
        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch);

        $this->assertFalse($dispatcher->hasListeners('foo'));
        $this->assertFalse($tdispatcher->hasListeners('foo'));

        $tdispatcher->addListener('foo', $listener = function () {});
        $this->assertTrue($dispatcher->hasListeners('foo'));
        $this->assertTrue($tdispatcher->hasListeners('foo'));
    }

    public function test_get_listener_priority()
    {
        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch);

        $tdispatcher->addListener('foo', function () {}, 123);

        $listeners = $dispatcher->getListeners('foo');
        $this->assertSame(123, $tdispatcher->getListenerPriority('foo', $listeners[0]));

        // Verify that priority is preserved when listener is removed and re-added
        // in preProcess() and postProcess().
        $tdispatcher->dispatch('foo', new Event);
        $listeners = $dispatcher->getListeners('foo');
        $this->assertSame(123, $tdispatcher->getListenerPriority('foo', $listeners[0]));
    }

    public function test_add_remove_subscriber()
    {
        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch);

        $subscriber = new EventSubscriber;

        $tdispatcher->addSubscriber($subscriber);
        $listeners = $dispatcher->getListeners('foo');
        $this->assertCount(1, $listeners);
        $this->assertSame([$subscriber, 'call'], $listeners[0]);

        $tdispatcher->removeSubscriber($subscriber);
        $this->assertCount(0, $dispatcher->getListeners('foo'));
    }

    public function test_get_called_listeners()
    {
        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch);
        $tdispatcher->addListener('foo', $listener = function () {});

        $listeners = $tdispatcher->getNotCalledListeners();
        $this->assertArrayHasKey('data', $listeners['foo.closure']);
        unset($listeners['foo.closure']['data']);
        $this->assertEquals([], $tdispatcher->getCalledListeners());
        $this->assertEquals(['foo.closure' => ['event' => 'foo', 'pretty' => 'closure', 'priority' => 0]], $listeners);

        $tdispatcher->dispatch('foo');

        $listeners = $tdispatcher->getCalledListeners();
        $this->assertArrayHasKey('data', $listeners['foo.closure']);
        unset($listeners['foo.closure']['data']);
        $this->assertEquals(['foo.closure' => ['event' => 'foo', 'pretty' => 'closure', 'priority' => null]], $listeners);
        $this->assertEquals([], $tdispatcher->getNotCalledListeners());
    }

    public function test_get_called_listeners_nested()
    {
        $tdispatcher = null;
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher, new Stopwatch);
        $dispatcher->addListener('foo', function (Event $event, $eventName, $dispatcher) use (&$tdispatcher) {
            $tdispatcher = $dispatcher;
            $dispatcher->dispatch('bar');
        });
        $dispatcher->addListener('bar', function (Event $event) {});
        $dispatcher->dispatch('foo');
        $this->assertSame($dispatcher, $tdispatcher);
        $this->assertCount(2, $dispatcher->getCalledListeners());
    }

    public function test_logger()
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch, $logger);
        $tdispatcher->addListener('foo', $listener1 = function () {});
        $tdispatcher->addListener('foo', $listener2 = function () {});

        $logger->expects($this->at(0))->method('debug')->with('Notified event "{event}" to listener "{listener}".', ['event' => 'foo', 'listener' => 'closure']);
        $logger->expects($this->at(1))->method('debug')->with('Notified event "{event}" to listener "{listener}".', ['event' => 'foo', 'listener' => 'closure']);

        $tdispatcher->dispatch('foo');
    }

    public function test_logger_with_stopped_event()
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch, $logger);
        $tdispatcher->addListener('foo', $listener1 = function (Event $event) {
            $event->stopPropagation();
        });
        $tdispatcher->addListener('foo', $listener2 = function () {});

        $logger->expects($this->at(0))->method('debug')->with('Notified event "{event}" to listener "{listener}".', ['event' => 'foo', 'listener' => 'closure']);
        $logger->expects($this->at(1))->method('debug')->with('Listener "{listener}" stopped propagation of the event "{event}".', ['event' => 'foo', 'listener' => 'closure']);
        $logger->expects($this->at(2))->method('debug')->with('Listener "{listener}" was not called for event "{event}".', ['event' => 'foo', 'listener' => 'closure']);

        $tdispatcher->dispatch('foo');
    }

    public function test_dispatch_call_listeners()
    {
        $called = [];

        $dispatcher = new EventDispatcher;
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch);
        $tdispatcher->addListener('foo', function () use (&$called) {
            $called[] = 'foo1';
        }, 10);
        $tdispatcher->addListener('foo', function () use (&$called) {
            $called[] = 'foo2';
        }, 20);

        $tdispatcher->dispatch('foo');

        $this->assertSame(['foo2', 'foo1'], $called);
    }

    public function test_dispatch_nested()
    {
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher, new Stopwatch);
        $loop = 1;
        $dispatchedEvents = 0;
        $dispatcher->addListener('foo', $listener1 = function () use ($dispatcher, &$loop) {
            $loop++;
            if ($loop == 2) {
                $dispatcher->dispatch('foo');
            }
        });
        $dispatcher->addListener('foo', function () use (&$dispatchedEvents) {
            $dispatchedEvents++;
        });

        $dispatcher->dispatch('foo');

        $this->assertSame(2, $dispatchedEvents);
    }

    public function test_dispatch_reused_event_nested()
    {
        $nestedCall = false;
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher, new Stopwatch);
        $dispatcher->addListener('foo', function (Event $e) use ($dispatcher) {
            $dispatcher->dispatch('bar', $e);
        });
        $dispatcher->addListener('bar', function (Event $e) use (&$nestedCall) {
            $nestedCall = true;
        });

        $this->assertFalse($nestedCall);
        $dispatcher->dispatch('foo');
        $this->assertTrue($nestedCall);
    }

    public function test_listener_can_remove_itself_when_executed()
    {
        $eventDispatcher = new TraceableEventDispatcher(new EventDispatcher, new Stopwatch);
        $listener1 = function ($event, $eventName, EventDispatcherInterface $dispatcher) use (&$listener1) {
            $dispatcher->removeListener('foo', $listener1);
        };
        $eventDispatcher->addListener('foo', $listener1);
        $eventDispatcher->addListener('foo', function () {});
        $eventDispatcher->dispatch('foo');

        $this->assertCount(1, $eventDispatcher->getListeners('foo'), 'expected listener1 to be removed');
    }
}

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['foo' => 'call'];
    }
}
