<?php

class Swift_Bug274Test extends \PHPUnit_Framework_TestCase
{
    public function test_empty_file_name_as_attachment()
    {
        $message = new Swift_Message;
        $this->setExpectedException('Swift_IoException', 'The path cannot be empty');
        $message->attach(Swift_Attachment::fromPath(''));
    }

    public function test_non_empty_file_name_as_attachment()
    {
        $message = new Swift_Message;
        try {
            $message->attach(Swift_Attachment::fromPath(__FILE__));
        } catch (Exception $e) {
            $this->fail('Path should not be empty');
        }
    }
}
