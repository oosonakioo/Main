<?php

class Swift_Bug71Test extends \PHPUnit_Framework_TestCase
{
    private $_message;

    protected function setUp()
    {
        $this->_message = new Swift_Message('test');
    }

    public function test_calling_to_string_after_setting_new_body_reflects_changes()
    {
        $this->_message->setBody('BODY1');
        $this->assertRegExp('/BODY1/', $this->_message->toString());

        $this->_message->setBody('BODY2');
        $this->assertRegExp('/BODY2/', $this->_message->toString());
    }
}
