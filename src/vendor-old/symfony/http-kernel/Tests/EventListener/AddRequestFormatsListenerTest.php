<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\EventListener\AddRequestFormatsListener;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Test AddRequestFormatsListener class.
 *
 * @author Gildas Quemener <gildas.quemener@gmail.com>
 */
class AddRequestFormatsListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddRequestFormatsListener
     */
    private $listener;

    protected function setUp()
    {
        $this->listener = new AddRequestFormatsListener(['csv' => ['text/csv', 'text/plain']]);
    }

    protected function tearDown()
    {
        $this->listener = null;
    }

    public function test_is_an_event_subscriber()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
    }

    public function test_registered_event()
    {
        $this->assertEquals(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            AddRequestFormatsListener::getSubscribedEvents()
        );
    }

    public function test_set_additional_formats()
    {
        $request = $this->getRequestMock();
        $event = $this->getGetResponseEventMock($request);

        $request->expects($this->once())
            ->method('setFormat')
            ->with('csv', ['text/csv', 'text/plain']);

        $this->listener->onKernelRequest($event);
    }

    protected function getRequestMock()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Request');
    }

    protected function getGetResponseEventMock(Request $request)
    {
        $event = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        return $event;
    }
}
