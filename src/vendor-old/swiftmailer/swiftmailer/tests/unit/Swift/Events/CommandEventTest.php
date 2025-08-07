<?php

class Swift_Events_CommandEventTest extends \PHPUnit_Framework_TestCase
{
    public function test_command_can_be_fetched_by_getter()
    {
        $evt = $this->_createEvent($this->_createTransport(), "FOO\r\n");
        $this->assertEquals("FOO\r\n", $evt->getCommand());
    }

    public function test_success_codes_can_be_fetched_via_getter()
    {
        $evt = $this->_createEvent($this->_createTransport(), "FOO\r\n", [250]);
        $this->assertEquals([250], $evt->getSuccessCodes());
    }

    public function test_source_is_buffer()
    {
        $transport = $this->_createTransport();
        $evt = $this->_createEvent($transport, "FOO\r\n");
        $ref = $evt->getSource();
        $this->assertEquals($transport, $ref);
    }

    private function _createEvent(Swift_Transport $source, $command, $successCodes = [])
    {
        return new Swift_Events_CommandEvent($source, $command, $successCodes);
    }

    private function _createTransport()
    {
        return $this->getMockBuilder('Swift_Transport')->getMock();
    }
}
