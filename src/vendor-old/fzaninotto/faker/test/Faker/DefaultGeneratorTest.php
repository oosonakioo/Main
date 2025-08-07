<?php

namespace Faker\Test;

use Faker\DefaultGenerator;

class DefaultGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function test_generator_returns_null_by_default()
    {
        $generator = new DefaultGenerator;
        $this->assertSame(null, $generator->value);
    }

    public function test_generator_returns_default_value_for_any_property_get()
    {
        $generator = new DefaultGenerator(123);
        $this->assertSame(123, $generator->foo);
        $this->assertNotSame(null, $generator->bar);
    }

    public function test_generator_returns_default_value_for_any_method_call()
    {
        $generator = new DefaultGenerator(123);
        $this->assertSame(123, $generator->foobar());
    }
}
