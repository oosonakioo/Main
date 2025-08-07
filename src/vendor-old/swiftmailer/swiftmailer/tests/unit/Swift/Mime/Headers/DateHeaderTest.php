<?php

class Swift_Mime_Headers_DateHeaderTest extends \PHPUnit_Framework_TestCase
{
    /* --
    The following tests refer to RFC 2822, section 3.6.1 and 3.3.
    */

    public function test_type_is_date_header()
    {
        $header = $this->_getHeader('Date');
        $this->assertEquals(Swift_Mime_Header::TYPE_DATE, $header->getFieldType());
    }

    public function test_get_timestamp()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame($timestamp, $header->getTimestamp());
    }

    public function test_timestamp_can_be_set_by_setter()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame($timestamp, $header->getTimestamp());
    }

    public function test_integer_timestamp_is_converted_to_rfc2822_date()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertEquals(date('r', $timestamp), $header->getFieldBody());
    }

    public function test_set_body_model()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setFieldBodyModel($timestamp);
        $this->assertEquals(date('r', $timestamp), $header->getFieldBody());
    }

    public function test_get_body_model()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertEquals($timestamp, $header->getFieldBodyModel());
    }

    public function test_to_string()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertEquals('Date: '.date('r', $timestamp)."\r\n",
            $header->toString()
        );
    }

    private function _getHeader($name)
    {
        return new Swift_Mime_Headers_DateHeader($name, new Swift_Mime_Grammar);
    }
}
