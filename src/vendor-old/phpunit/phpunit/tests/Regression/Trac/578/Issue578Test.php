<?php

class Issue578Test extends PHPUnit_Framework_TestCase
{
    public function test_notices_double_print_stack_trace()
    {
        $this->iniSet('error_reporting', E_ALL | E_NOTICE);
        trigger_error('Stack Trace Test Notice', E_NOTICE);
    }

    public function test_warnings_double_print_stack_trace()
    {
        $this->iniSet('error_reporting', E_ALL | E_NOTICE);
        trigger_error('Stack Trace Test Notice', E_WARNING);
    }

    public function test_unexpected_exceptions_prints_correctly()
    {
        throw new Exception('Double printed exception');
    }
}
