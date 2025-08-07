<?php

class Swift_Mime_Headers_PathHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_type_is_path_header()
    {
        $header = $this->_getHeader('Return-Path');
        $this->assertEquals(Swift_Mime_Header::TYPE_PATH, $header->getFieldType());
    }

    public function test_single_address_can_be_set_and_fetched()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setAddress('chris@swiftmailer.org');
        $this->assertEquals('chris@swiftmailer.org', $header->getAddress());
    }

    public function test_address_must_comply_with_rfc2822()
    {
        try {
            $header = $this->_getHeader('Return-Path');
            $header->setAddress('chr is@swiftmailer.org');
            $this->fail('Addresses not valid according to RFC 2822 addr-spec grammar must be rejected.');
        } catch (Exception $e) {
        }
    }

    public function test_value_is_angle_addr_with_valid_address()
    {
        /* -- RFC 2822, 3.6.7.

            return          =       "Return-Path:" path CRLF

            path            =       ([CFWS] "<" ([CFWS] / addr-spec) ">" [CFWS]) /
                                                            obs-path
     */

        $header = $this->_getHeader('Return-Path');
        $header->setAddress('chris@swiftmailer.org');
        $this->assertEquals('<chris@swiftmailer.org>', $header->getFieldBody());
    }

    public function test_value_is_empty_angle_brackets_if_empty_address_set()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setAddress('');
        $this->assertEquals('<>', $header->getFieldBody());
    }

    public function test_set_body_model()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setFieldBodyModel('foo@bar.tld');
        $this->assertEquals('foo@bar.tld', $header->getAddress());
    }

    public function test_get_body_model()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setAddress('foo@bar.tld');
        $this->assertEquals('foo@bar.tld', $header->getFieldBodyModel());
    }

    public function test_to_string()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setAddress('chris@swiftmailer.org');
        $this->assertEquals('Return-Path: <chris@swiftmailer.org>'."\r\n",
            $header->toString()
        );
    }

    private function _getHeader($name)
    {
        return new Swift_Mime_Headers_PathHeader($name, new Swift_Mime_Grammar);
    }
}
