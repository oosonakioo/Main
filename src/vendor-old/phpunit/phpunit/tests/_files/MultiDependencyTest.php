<?php

class MultiDependencyTest extends PHPUnit_Framework_TestCase
{
    public function test_one()
    {
        return 'foo';
    }

    public function test_two()
    {
        return 'bar';
    }

    /**
     * @depends test_one
     * @depends test_two
     */
    public function test_three($a, $b)
    {
        $this->assertEquals('foo', $a);
        $this->assertEquals('bar', $b);
    }
}
