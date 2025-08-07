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

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContainerAwareEventDispatcherTest extends AbstractEventDispatcherTest
{
    protected function createEventDispatcher()
    {
        $container = new Container;

        return new ContainerAwareEventDispatcher($container);
    }

    public function test_add_a_listener_service()
    {
        $event = new Event;

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $service
            ->expects($this->once())
            ->method('onEvent')
            ->with($event);

        $container = new Container;
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $dispatcher->dispatch('onEvent', $event);
    }

    public function test_add_a_subscriber_service()
    {
        $event = new Event;

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\SubscriberService')->getMock();

        $service
            ->expects($this->once())
            ->method('onEvent')
            ->with($event);

        $service
            ->expects($this->once())
            ->method('onEventWithPriority')
            ->with($event);

        $service
            ->expects($this->once())
            ->method('onEventNested')
            ->with($event);

        $container = new Container;
        $container->set('service.subscriber', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addSubscriberService('service.subscriber', 'Symfony\Component\EventDispatcher\Tests\SubscriberService');

        $dispatcher->dispatch('onEvent', $event);
        $dispatcher->dispatch('onEventWithPriority', $event);
        $dispatcher->dispatch('onEventNested', $event);
    }

    public function test_prevent_duplicate_listener_service()
    {
        $event = new Event;

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $service
            ->expects($this->once())
            ->method('onEvent')
            ->with($event);

        $container = new Container;
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent'], 5);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent'], 10);

        $dispatcher->dispatch('onEvent', $event);
    }

    public function test_has_listeners_on_lazy_load()
    {
        $event = new Event;

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $container = new Container;
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $service
            ->expects($this->once())
            ->method('onEvent')
            ->with($event);

        $this->assertTrue($dispatcher->hasListeners());

        if ($dispatcher->hasListeners('onEvent')) {
            $dispatcher->dispatch('onEvent');
        }
    }

    public function test_get_listeners_on_lazy_load()
    {
        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $container = new Container;
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $listeners = $dispatcher->getListeners();

        $this->assertTrue(isset($listeners['onEvent']));

        $this->assertCount(1, $dispatcher->getListeners('onEvent'));
    }

    public function test_remove_after_dispatch()
    {
        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $container = new Container;
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $dispatcher->dispatch('onEvent', new Event);
        $dispatcher->removeListener('onEvent', [$container->get('service.listener'), 'onEvent']);
        $this->assertFalse($dispatcher->hasListeners('onEvent'));
    }

    public function test_remove_before_dispatch()
    {
        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $container = new Container;
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $dispatcher->removeListener('onEvent', [$container->get('service.listener'), 'onEvent']);
        $this->assertFalse($dispatcher->hasListeners('onEvent'));
    }
}

class Service
{
    public function onEvent(Event $e) {}
}

class SubscriberService implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'onEvent' => 'onEvent',
            'onEventWithPriority' => ['onEventWithPriority', 10],
            'onEventNested' => [['onEventNested']],
        ];
    }

    public function onEvent(Event $e) {}

    public function onEventWithPriority(Event $e) {}

    public function onEventNested(Event $e) {}
}
