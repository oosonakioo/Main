<?php

class OutputTestCase extends PHPUnit_Framework_TestCase
{
    public function test_expect_output_string_foo_actual_foo()
    {
        $this->expectOutputString('foo');
        echo 'foo';
    }

    public function test_expect_output_string_foo_actual_bar()
    {
        $this->expectOutputString('foo');
        echo 'bar';
    }

    public function test_expect_output_regex_foo_actual_foo()
    {
        $this->expectOutputRegex('/foo/');
        echo 'foo';
    }

    public function test_expect_output_regex_foo_actual_bar()
    {
        $this->expectOutputRegex('/foo/');
        echo 'bar';
    }
}
