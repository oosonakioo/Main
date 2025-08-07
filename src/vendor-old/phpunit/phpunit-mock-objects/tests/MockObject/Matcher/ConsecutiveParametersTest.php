<?php

class Framework_MockObject_Matcher_ConsecutiveParametersTest extends PHPUnit_Framework_TestCase
{
    public function test_integration()
    {
        $mock = $this->getMock('stdClass', ['foo']);
        $mock
            ->expects($this->any())
            ->method('foo')
            ->withConsecutive(
                ['bar'],
                [21, 42]
            );
        $mock->foo('bar');
        $mock->foo(21, 42);
    }

    public function test_integration_with_less_assertions_then_method_calls()
    {
        $mock = $this->getMock('stdClass', ['foo']);
        $mock
            ->expects($this->any())
            ->method('foo')
            ->withConsecutive(
                ['bar']
            );
        $mock->foo('bar');
        $mock->foo(21, 42);
    }

    public function test_integration_expecting_exception()
    {
        $mock = $this->getMock('stdClass', ['foo']);
        $mock
            ->expects($this->any())
            ->method('foo')
            ->withConsecutive(
                ['bar'],
                [21, 42]
            );
        $mock->foo('bar');
        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $mock->foo('invalid');
    }
}
