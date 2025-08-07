<?php

require_once __DIR__.'/AbstractSmtpTest.php';

abstract class Swift_Transport_AbstractSmtpEventSupportTest extends Swift_Transport_AbstractSmtpTest
{
    public function test_register_plugin_loads_plugin_in_event_dispatcher()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $listener = $this->getMockery('Swift_Events_EventListener');
        $smtp = $this->_getTransport($buf, $dispatcher);
        $dispatcher->shouldReceive('bindEventListener')
            ->once()
            ->with($listener);

        $smtp->registerPlugin($listener);
    }

    public function test_sending_dispatches_before_send_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $message = $this->_createMessage();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();

        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(['chris@swiftmailer.org' => null]);
        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['mark@swiftmailer.org' => 'Mark']);
        $dispatcher->shouldReceive('createSendEvent')
            ->once()
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'beforeSendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(1, $smtp->send($message));
    }

    public function test_sending_dispatches_send_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $message = $this->_createMessage();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();

        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(['chris@swiftmailer.org' => null]);
        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['mark@swiftmailer.org' => 'Mark']);
        $dispatcher->shouldReceive('createSendEvent')
            ->once()
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(1, $smtp->send($message));
    }

    public function test_send_event_captures_failures()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(['chris@swiftmailer.org' => null]);
        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['mark@swiftmailer.org' => 'Mark']);
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<chris@swiftmailer.org>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<mark@swiftmailer.org>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn("500 Not now\r\n");
        $dispatcher->shouldReceive('createSendEvent')
            ->zeroOrMoreTimes()
            ->with($smtp, \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $evt->shouldReceive('setFailedRecipients')
            ->once()
            ->with(['mark@swiftmailer.org']);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(0, $smtp->send($message));
    }

    public function test_send_event_has_result_failed_if_all_failures()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(['chris@swiftmailer.org' => null]);
        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['mark@swiftmailer.org' => 'Mark']);
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<chris@swiftmailer.org>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<mark@swiftmailer.org>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn("500 Not now\r\n");
        $dispatcher->shouldReceive('createSendEvent')
            ->zeroOrMoreTimes()
            ->with($smtp, \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $evt->shouldReceive('setResult')
            ->once()
            ->with(Swift_Events_SendEvent::RESULT_FAILED);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(0, $smtp->send($message));
    }

    public function test_send_event_has_result_tentative_if_some_failures()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(['chris@swiftmailer.org' => null]);
        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn([
                'mark@swiftmailer.org' => 'Mark',
                'chris@site.tld' => 'Chris',
            ]);
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<chris@swiftmailer.org>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<mark@swiftmailer.org>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn("500 Not now\r\n");
        $dispatcher->shouldReceive('createSendEvent')
            ->zeroOrMoreTimes()
            ->with($smtp, \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $evt->shouldReceive('setResult')
            ->once()
            ->with(Swift_Events_SendEvent::RESULT_TENTATIVE);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(1, $smtp->send($message));
    }

    public function test_send_event_has_result_success_if_no_failures()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(['chris@swiftmailer.org' => null]);
        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn([
                'mark@swiftmailer.org' => 'Mark',
                'chris@site.tld' => 'Chris',
            ]);
        $dispatcher->shouldReceive('createSendEvent')
            ->zeroOrMoreTimes()
            ->with($smtp, \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $evt->shouldReceive('setResult')
            ->once()
            ->with(Swift_Events_SendEvent::RESULT_SUCCESS);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(2, $smtp->send($message));
    }

    public function test_cancelling_event_bubble_before_send_stops_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(['chris@swiftmailer.org' => null]);
        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['mark@swiftmailer.org' => 'Mark']);
        $dispatcher->shouldReceive('createSendEvent')
            ->zeroOrMoreTimes()
            ->with($smtp, \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'beforeSendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(true);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(0, $smtp->send($message));
    }

    public function test_starting_transport_dispatches_transport_change_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
            ->atLeast()->once()
            ->with($smtp)
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'transportStarted');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(false);

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    public function test_starting_transport_dispatches_before_transport_change_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
            ->atLeast()->once()
            ->with($smtp)
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'beforeTransportStarted');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(false);

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    public function test_cancelling_bubble_before_transport_start_stops_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
            ->atLeast()->once()
            ->with($smtp)
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'beforeTransportStarted');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(true);

        $this->_finishBuffer($buf);
        $smtp->start();

        $this->assertFalse($smtp->isStarted(),
            '%s: Transport should not be started since event bubble was cancelled'
        );
    }

    public function test_stopping_transport_dispatches_transport_change_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
            ->atLeast()->once()
            ->with($smtp)
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'transportStopped');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->stop();
    }

    public function test_stopping_transport_dispatches_before_transport_change_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
            ->atLeast()->once()
            ->with($smtp)
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'beforeTransportStopped');
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->stop();
    }

    public function test_cancelling_bubble_before_transport_stopped_stops_event()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $hasRun = false;
        $dispatcher->shouldReceive('createTransportChangeEvent')
            ->atLeast()->once()
            ->with($smtp)
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'beforeTransportStopped')
            ->andReturnUsing(function () use (&$hasRun) {
                $hasRun = true;
            });
        $dispatcher->shouldReceive('dispatchEvent')
            ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function () use (&$hasRun) {
                return $hasRun;
            });

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->stop();

        $this->assertTrue($smtp->isStarted(),
            '%s: Transport should not be stopped since event bubble was cancelled'
        );
    }

    public function test_response_events_are_generated()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_ResponseEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createResponseEvent')
            ->atLeast()->once()
            ->with($smtp, \Mockery::any(), \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->atLeast()->once()
            ->with($evt, 'responseReceived');

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    public function test_command_events_are_generated()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_CommandEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createCommandEvent')
            ->once()
            ->with($smtp, \Mockery::any(), \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'commandSent');

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    public function test_exceptions_cause_exception_events()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportExceptionEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $buf->shouldReceive('readLine')
            ->atLeast()->once()
            ->andReturn("503 I'm sleepy, go away!\r\n");
        $dispatcher->shouldReceive('createTransportExceptionEvent')
            ->zeroOrMoreTimes()
            ->with($smtp, \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->once()
            ->with($evt, 'exceptionThrown');
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(false);

        try {
            $smtp->start();
            $this->fail('TransportException should be thrown on invalid response');
        } catch (Swift_TransportException $e) {
        }
    }

    public function test_exception_bubbles_can_be_cancelled()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportExceptionEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $buf->shouldReceive('readLine')
            ->atLeast()->once()
            ->andReturn("503 I'm sleepy, go away!\r\n");
        $dispatcher->shouldReceive('createTransportExceptionEvent')
            ->twice()
            ->with($smtp, \Mockery::any())
            ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
            ->twice()
            ->with($evt, 'exceptionThrown');
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(true);

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    protected function _createEventDispatcher($stub = true)
    {
        return $this->getMockery('Swift_Events_EventDispatcher')->shouldIgnoreMissing();
    }
}
