<?php

class Swift_Plugins_LoggerPluginTest extends \SwiftMailerTestCase
{
    public function test_logger_delegates_adding_entries()
    {
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('add')
            ->with('foo');

        $plugin = $this->_createPlugin($logger);
        $plugin->add('foo');
    }

    public function test_logger_delegates_dumping_entries()
    {
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('dump')
            ->will($this->returnValue('foobar'));

        $plugin = $this->_createPlugin($logger);
        $this->assertEquals('foobar', $plugin->dump());
    }

    public function test_logger_delegates_clearing_entries()
    {
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('clear');

        $plugin = $this->_createPlugin($logger);
        $plugin->clear();
    }

    public function test_command_is_sent_to_logger()
    {
        $evt = $this->_createCommandEvent("foo\r\n");
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('add')
            ->with($this->regExp('~foo\r\n~'));

        $plugin = $this->_createPlugin($logger);
        $plugin->commandSent($evt);
    }

    public function test_response_is_sent_to_logger()
    {
        $evt = $this->_createResponseEvent("354 Go ahead\r\n");
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('add')
            ->with($this->regExp('~354 Go ahead\r\n~'));

        $plugin = $this->_createPlugin($logger);
        $plugin->responseReceived($evt);
    }

    public function test_transport_before_start_change_is_sent_to_logger()
    {
        $evt = $this->_createTransportChangeEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('add')
            ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        $plugin->beforeTransportStarted($evt);
    }

    public function test_transport_start_change_is_sent_to_logger()
    {
        $evt = $this->_createTransportChangeEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('add')
            ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        $plugin->transportStarted($evt);
    }

    public function test_transport_stop_change_is_sent_to_logger()
    {
        $evt = $this->_createTransportChangeEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('add')
            ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        $plugin->transportStopped($evt);
    }

    public function test_transport_before_stop_change_is_sent_to_logger()
    {
        $evt = $this->_createTransportChangeEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('add')
            ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        $plugin->beforeTransportStopped($evt);
    }

    public function test_exceptions_are_passed_to_delegate_and_left_to_bubble_up()
    {
        $transport = $this->_createTransport();
        $evt = $this->_createTransportExceptionEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
            ->method('add')
            ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        try {
            $plugin->exceptionThrown($evt);
            $this->fail('Exception should bubble up.');
        } catch (Swift_TransportException $ex) {
        }
    }

    private function _createLogger()
    {
        return $this->getMockBuilder('Swift_Plugins_Logger')->getMock();
    }

    private function _createPlugin($logger)
    {
        return new Swift_Plugins_LoggerPlugin($logger);
    }

    private function _createCommandEvent($command)
    {
        $evt = $this->getMockBuilder('Swift_Events_CommandEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $evt->expects($this->any())
            ->method('getCommand')
            ->will($this->returnValue($command));

        return $evt;
    }

    private function _createResponseEvent($response)
    {
        $evt = $this->getMockBuilder('Swift_Events_ResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $evt->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response));

        return $evt;
    }

    private function _createTransport()
    {
        return $this->getMockBuilder('Swift_Transport')->getMock();
    }

    private function _createTransportChangeEvent()
    {
        $evt = $this->getMockBuilder('Swift_Events_TransportChangeEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $evt->expects($this->any())
            ->method('getSource')
            ->will($this->returnValue($this->_createTransport()));

        return $evt;
    }

    public function _createTransportExceptionEvent()
    {
        $evt = $this->getMockBuilder('Swift_Events_TransportExceptionEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $evt->expects($this->any())
            ->method('getException')
            ->will($this->returnValue(new Swift_TransportException('')));

        return $evt;
    }
}
