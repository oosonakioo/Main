<?php

class Swift_Events_SimpleEventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    private $_dispatcher;

    protected function setUp()
    {
        $this->_dispatcher = new Swift_Events_SimpleEventDispatcher;
    }

    public function test_send_event_can_be_created()
    {
        $transport = $this->getMockBuilder('Swift_Transport')->getMock();
        $message = $this->getMockBuilder('Swift_Mime_Message')->getMock();
        $evt = $this->_dispatcher->createSendEvent($transport, $message);
        $this->assertInstanceOf('Swift_Events_SendEvent', $evt);
        $this->assertSame($message, $evt->getMessage());
        $this->assertSame($transport, $evt->getTransport());
    }

    public function test_command_event_can_be_created()
    {
        $buf = $this->getMockBuilder('Swift_Transport')->getMock();
        $evt = $this->_dispatcher->createCommandEvent($buf, "FOO\r\n", [250]);
        $this->assertInstanceOf('Swift_Events_CommandEvent', $evt);
        $this->assertSame($buf, $evt->getSource());
        $this->assertEquals("FOO\r\n", $evt->getCommand());
        $this->assertEquals([250], $evt->getSuccessCodes());
    }

    public function test_response_event_can_be_created()
    {
        $buf = $this->getMockBuilder('Swift_Transport')->getMock();
        $evt = $this->_dispatcher->createResponseEvent($buf, "250 Ok\r\n", true);
        $this->assertInstanceOf('Swift_Events_ResponseEvent', $evt);
        $this->assertSame($buf, $evt->getSource());
        $this->assertEquals("250 Ok\r\n", $evt->getResponse());
        $this->assertTrue($evt->isValid());
    }

    public function test_transport_change_event_can_be_created()
    {
        $transport = $this->getMockBuilder('Swift_Transport')->getMock();
        $evt = $this->_dispatcher->createTransportChangeEvent($transport);
        $this->assertInstanceOf('Swift_Events_TransportChangeEvent', $evt);
        $this->assertSame($transport, $evt->getSource());
    }

    public function test_transport_exception_event_can_be_created()
    {
        $transport = $this->getMockBuilder('Swift_Transport')->getMock();
        $ex = new Swift_TransportException('');
        $evt = $this->_dispatcher->createTransportExceptionEvent($transport, $ex);
        $this->assertInstanceOf('Swift_Events_TransportExceptionEvent', $evt);
        $this->assertSame($transport, $evt->getSource());
        $this->assertSame($ex, $evt->getException());
    }

    public function test_listeners_are_notified_of_dispatched_event()
    {
        $transport = $this->getMockBuilder('Swift_Transport')->getMock();

        $evt = $this->_dispatcher->createTransportChangeEvent($transport);

        $listenerA = $this->getMockBuilder('Swift_Events_TransportChangeListener')->getMock();
        $listenerB = $this->getMockBuilder('Swift_Events_TransportChangeListener')->getMock();

        $this->_dispatcher->bindEventListener($listenerA);
        $this->_dispatcher->bindEventListener($listenerB);

        $listenerA->expects($this->once())
            ->method('transportStarted')
            ->with($evt);
        $listenerB->expects($this->once())
            ->method('transportStarted')
            ->with($evt);

        $this->_dispatcher->dispatchEvent($evt, 'transportStarted');
    }

    public function test_listeners_are_only_called_if_implementing_correct_interface()
    {
        $transport = $this->getMockBuilder('Swift_Transport')->getMock();
        $message = $this->getMockBuilder('Swift_Mime_Message')->getMock();

        $evt = $this->_dispatcher->createSendEvent($transport, $message);

        $targetListener = $this->getMockBuilder('Swift_Events_SendListener')->getMock();
        $otherListener = $this->getMockBuilder('DummyListener')->getMock();

        $this->_dispatcher->bindEventListener($targetListener);
        $this->_dispatcher->bindEventListener($otherListener);

        $targetListener->expects($this->once())
            ->method('sendPerformed')
            ->with($evt);
        $otherListener->expects($this->never())
            ->method('sendPerformed');

        $this->_dispatcher->dispatchEvent($evt, 'sendPerformed');
    }

    public function test_listeners_can_cancel_bubbling_of_event()
    {
        $transport = $this->getMockBuilder('Swift_Transport')->getMock();
        $message = $this->getMockBuilder('Swift_Mime_Message')->getMock();

        $evt = $this->_dispatcher->createSendEvent($transport, $message);

        $listenerA = $this->getMockBuilder('Swift_Events_SendListener')->getMock();
        $listenerB = $this->getMockBuilder('Swift_Events_SendListener')->getMock();

        $this->_dispatcher->bindEventListener($listenerA);
        $this->_dispatcher->bindEventListener($listenerB);

        $listenerA->expects($this->once())
            ->method('sendPerformed')
            ->with($evt)
            ->will($this->returnCallback(function ($object) {
                $object->cancelBubble(true);
            }));
        $listenerB->expects($this->never())
            ->method('sendPerformed');

        $this->_dispatcher->dispatchEvent($evt, 'sendPerformed');

        $this->assertTrue($evt->bubbleCancelled());
    }

    private function _createDispatcher(array $map)
    {
        return new Swift_Events_SimpleEventDispatcher($map);
    }
}

class DummyListener implements Swift_Events_EventListener
{
    public function sendPerformed(Swift_Events_SendEvent $evt) {}
}
