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
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ImmutableEventDispatcherTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $innerDispatcher;

    /**
     * @var ImmutableEventDispatcher
     */
    private $dispatcher;

    protected function setUp()
    {
        $this->innerDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->dispatcher = new ImmutableEventDispatcher($this->innerDispatcher);
    }

    public function test_dispatch_delegates()
    {
        $event = new Event;

        $this->innerDispatcher->expects($this->once())
            ->method('dispatch')
            ->with('event', $event)
            ->will($this->returnValue('result'));

        $this->assertSame('result', $this->dispatcher->dispatch('event', $event));
    }

    public function test_get_listeners_delegates()
    {
        $this->innerDispatcher->expects($this->once())
            ->method('getListeners')
            ->with('event')
            ->will($this->returnValue('result'));

        $this->assertSame('result', $this->dispatcher->getListeners('event'));
    }

    public function test_has_listeners_delegates()
    {
        $this->innerDispatcher->expects($this->once())
            ->method('hasListeners')
            ->with('event')
            ->will($this->returnValue('result'));

        $this->assertSame('result', $this->dispatcher->hasListeners('event'));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test_add_listener_disallowed()
    {
        $this->dispatcher->addListener('event', function () {
            return 'foo';
        });
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test_add_subscriber_disallowed()
    {
        $subscriber = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')->getMock();

        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test_remove_listener_disallowed()
    {
        $this->dispatcher->removeListener('event', function () {
            return 'foo';
        });
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test_remove_subscriber_disallowed()
    {
        $subscriber = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')->getMock();

        $this->dispatcher->removeSubscriber($subscriber);
    }
}
