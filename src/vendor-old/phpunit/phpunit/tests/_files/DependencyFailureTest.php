<?php

class DependencyFailureTest extends PHPUnit_Framework_TestCase
{
    public function test_one()
    {
        $this->fail();
    }

    /**
     * @depends test_one
     */
    public function test_two() {}

    /**
     * @depends test_two
     */
    public function test_three() {}
}
