<?php

class Swift_Events_EventObjectTest extends \PHPUnit_Framework_TestCase
{
    public function test_event_source_can_be_returned_via_getter()
    {
        $source = new stdClass;
        $evt = $this->_createEvent($source);
        $ref = $evt->getSource();
        $this->assertEquals($source, $ref);
    }

    public function test_event_does_not_have_cancelled_bubble_when_new()
    {
        $source = new stdClass;
        $evt = $this->_createEvent($source);
        $this->assertFalse($evt->bubbleCancelled());
    }

    public function test_bubble_can_be_cancelled_in_event()
    {
        $source = new stdClass;
        $evt = $this->_createEvent($source);
        $evt->cancelBubble();
        $this->assertTrue($evt->bubbleCancelled());
    }

    private function _createEvent($source)
    {
        return new Swift_Events_EventObject($source);
    }
}
