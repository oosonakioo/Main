<?php

class Swift_CharacterReader_Utf8ReaderTest extends \PHPUnit_Framework_TestCase
{
    private $_reader;

    protected function setUp()
    {
        $this->_reader = new Swift_CharacterReader_Utf8Reader;
    }

    public function test_leading7_bit_octet_causes_return_zero()
    {
        for ($ordinal = 0x00; $ordinal <= 0x7F; $ordinal++) {
            $this->assertSame(
                0, $this->_reader->validateByteSequence([$ordinal], 1)
            );
        }
    }

    public function test_leading_byte_of2_octet_char_causes_return1()
    {
        for ($octet = 0xC0; $octet <= 0xDF; $octet++) {
            $this->assertSame(
                1, $this->_reader->validateByteSequence([$octet], 1)
            );
        }
    }

    public function test_leading_byte_of3_octet_char_causes_return2()
    {
        for ($octet = 0xE0; $octet <= 0xEF; $octet++) {
            $this->assertSame(
                2, $this->_reader->validateByteSequence([$octet], 1)
            );
        }
    }

    public function test_leading_byte_of4_octet_char_causes_return3()
    {
        for ($octet = 0xF0; $octet <= 0xF7; $octet++) {
            $this->assertSame(
                3, $this->_reader->validateByteSequence([$octet], 1)
            );
        }
    }

    public function test_leading_byte_of5_octet_char_causes_return4()
    {
        for ($octet = 0xF8; $octet <= 0xFB; $octet++) {
            $this->assertSame(
                4, $this->_reader->validateByteSequence([$octet], 1)
            );
        }
    }

    public function test_leading_byte_of6_octet_char_causes_return5()
    {
        for ($octet = 0xFC; $octet <= 0xFD; $octet++) {
            $this->assertSame(
                5, $this->_reader->validateByteSequence([$octet], 1)
            );
        }
    }
}
