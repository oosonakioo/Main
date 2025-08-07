<?php

require_once 'swift_required.php';

class Swift_EncodingAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    public function test_get7_bit_encoding_returns7_bit_encoder()
    {
        $encoder = Swift_Encoding::get7BitEncoding();
        $this->assertEquals('7bit', $encoder->getName());
    }

    public function test_get8_bit_encoding_returns8_bit_encoder()
    {
        $encoder = Swift_Encoding::get8BitEncoding();
        $this->assertEquals('8bit', $encoder->getName());
    }

    public function test_get_qp_encoding_returns_qp_encoder()
    {
        $encoder = Swift_Encoding::getQpEncoding();
        $this->assertEquals('quoted-printable', $encoder->getName());
    }

    public function test_get_base64_encoding_returns_base64_encoder()
    {
        $encoder = Swift_Encoding::getBase64Encoding();
        $this->assertEquals('base64', $encoder->getName());
    }
}
