<?php

class Swift_Bug118Test extends \PHPUnit_Framework_TestCase
{
    private $_message;

    protected function setUp()
    {
        $this->_message = new Swift_Message;
    }

    public function test_calling_generate_id_changes_the_message_id()
    {
        $currentId = $this->_message->getId();
        $this->_message->generateId();
        $newId = $this->_message->getId();

        $this->assertNotEquals($currentId, $newId);
    }
}
