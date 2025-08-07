<?php

class Swift_Events_SendEventTest extends \PHPUnit_Framework_TestCase
{
    public function test_message_can_be_fetched_via_getter()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $ref = $evt->getMessage();
        $this->assertEquals($message, $ref,
            '%s: Message should be returned from getMessage()'
        );
    }

    public function test_transport_can_be_fetch_via_getter()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $ref = $evt->getTransport();
        $this->assertEquals($transport, $ref,
            '%s: Transport should be returned from getTransport()'
        );
    }

    public function test_transport_can_be_fetch_via_get_source()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $ref = $evt->getSource();
        $this->assertEquals($transport, $ref,
            '%s: Transport should be returned from getSource()'
        );
    }

    public function test_result_can_be_set_and_get()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $evt->setResult(
            Swift_Events_SendEvent::RESULT_SUCCESS | Swift_Events_SendEvent::RESULT_TENTATIVE
        );

        $this->assertTrue((bool) ($evt->getResult() & Swift_Events_SendEvent::RESULT_SUCCESS));
        $this->assertTrue((bool) ($evt->getResult() & Swift_Events_SendEvent::RESULT_TENTATIVE));
    }

    public function test_failed_recipients_can_be_set_and_get()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $evt->setFailedRecipients(['foo@bar', 'zip@button']);

        $this->assertEquals(['foo@bar', 'zip@button'], $evt->getFailedRecipients(),
            '%s: FailedRecipients should be returned from getter'
        );
    }

    public function test_failed_recipients_gets_picked_up_correctly()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);
        $this->assertEquals([], $evt->getFailedRecipients());
    }

    private function _createEvent(Swift_Transport $source,
        Swift_Mime_Message $message)
    {
        return new Swift_Events_SendEvent($source, $message);
    }

    private function _createTransport()
    {
        return $this->getMockBuilder('Swift_Transport')->getMock();
    }

    private function _createMessage()
    {
        return $this->getMockBuilder('Swift_Mime_Message')->getMock();
    }
}
