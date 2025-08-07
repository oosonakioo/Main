<?php

class Issue433Test extends PHPUnit_Framework_TestCase
{
    public function test_output_with_expectation_before()
    {
        $this->expectOutputString('test');
        echo 'test';
    }

    public function test_output_with_expectation_after()
    {
        echo 'test';
        $this->expectOutputString('test');
    }

    public function test_not_matching_output()
    {
        echo 'bar';
        $this->expectOutputString('foo');
    }
}
