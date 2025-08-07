<?php

class ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Exception message
     *
     * @var string
     */
    const ERROR_MESSAGE = 'Exception message';

    /**
     * Exception message
     *
     * @var string
     */
    const ERROR_MESSAGE_REGEX = '#regex#';

    /**
     * Exception code
     *
     * @var int
     */
    const ERROR_CODE = 500;

    /**
     * @expectedException FooBarBaz
     */
    public function test_one() {}

    /**
     * @expectedException Foo_Bar_Baz
     */
    public function test_two() {}

    /**
     * @expectedException Foo\Bar\Baz
     */
    public function test_three() {}

    /**
     * @expectedException ほげ
     */
    public function test_four() {}

    /**
     * @expectedException Class Message 1234
     */
    public function test_five() {}

    /**
     * @expectedException Class
     *
     * @expectedExceptionMessage Message
     *
     * @expectedExceptionCode 1234
     */
    public function test_six() {}

    /**
     * @expectedException Class
     *
     * @expectedExceptionMessage Message
     *
     * @expectedExceptionCode ExceptionCode
     */
    public function test_seven() {}

    /**
     * @expectedException Class
     *
     * @expectedExceptionMessage Message
     *
     * @expectedExceptionCode 0
     */
    public function test_eight() {}

    /**
     * @expectedException Class
     *
     * @expectedExceptionMessage ExceptionTest::ERROR_MESSAGE
     *
     * @expectedExceptionCode ExceptionTest::ERROR_CODE
     */
    public function test_nine() {}

    /** @expectedException Class */
    public function test_single_line() {}

    /**
     * @expectedException Class
     *
     * @expectedExceptionCode ExceptionTest::UNKNOWN_CODE_CONSTANT
     *
     * @expectedExceptionMessage ExceptionTest::UNKNOWN_MESSAGE_CONSTANT
     */
    public function test_unknown_constants() {}

    /**
     * @expectedException Class
     *
     * @expectedExceptionCode 1234
     *
     * @expectedExceptionMessage Message
     *
     * @expectedExceptionMessageRegExp #regex#
     */
    public function test_with_regex_message() {}

    /**
     * @expectedException Class
     *
     * @expectedExceptionCode 1234
     *
     * @expectedExceptionMessage Message
     *
     * @expectedExceptionMessageRegExp ExceptionTest::ERROR_MESSAGE_REGEX
     */
    public function test_with_regex_message_from_class_constant() {}

    /**
     * @expectedException Class
     *
     * @expectedExceptionCode 1234
     *
     * @expectedExceptionMessage Message
     *
     * @expectedExceptionMessageRegExp ExceptionTest::UNKNOWN_MESSAGE_REGEX_CONSTANT
     */
    public function test_with_unknow_regex_message_from_class_constant() {}
}
