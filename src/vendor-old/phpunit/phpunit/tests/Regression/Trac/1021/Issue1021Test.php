<?php

class Issue1021Test extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function test_something($data)
    {
        $this->assertTrue($data);
    }

    /**
     * @depends test_something
     */
    public function test_something_else() {}

    public function provider()
    {
        return [[true]];
    }
}
