<?php

namespace test\Mockery;

use Mockery as m;

class SpyTest extends \PHPUnit_Framework_TestCase
{
    protected function setup()
    {
        $this->container = new \Mockery\Container;
    }

    protected function teardown()
    {
        $this->container->mockery_close();
    }

    /** @test */
    public function it_verifies_a_method_was_called()
    {
        $spy = m::spy();
        $spy->myMethod();
        $spy->shouldHaveReceived('myMethod');

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->shouldHaveReceived('someMethodThatWasNotCalled');
    }

    /** @test */
    public function it_verifies_a_method_was_not_called()
    {
        $spy = m::spy();
        $spy->shouldNotHaveReceived('myMethod');

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->myMethod();
        $spy->shouldNotHaveReceived('myMethod');
    }

    /** @test */
    public function it_verifies_a_method_was_not_called_with_particular_arguments()
    {
        $spy = m::spy();
        $spy->myMethod(123, 456);

        $spy->shouldNotHaveReceived('myMethod', [789, 10]);

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->shouldNotHaveReceived('myMethod', [123, 456]);
    }

    /** @test */
    public function it_verifies_a_method_was_called_a_specific_number_of_times()
    {
        $spy = m::spy();
        $spy->myMethod();
        $spy->myMethod();
        $spy->shouldHaveReceived('myMethod')->twice();

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->myMethod();
        $spy->shouldHaveReceived('myMethod')->twice();
    }

    /** @test */
    public function it_verifies_a_method_was_called_with_specific_arguments()
    {
        $spy = m::spy();
        $spy->myMethod(123, 'a string');
        $spy->shouldHaveReceived('myMethod')->with(123, 'a string');
        $spy->shouldHaveReceived('myMethod', [123, 'a string']);

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->shouldHaveReceived('myMethod')->with(123);
    }
}
