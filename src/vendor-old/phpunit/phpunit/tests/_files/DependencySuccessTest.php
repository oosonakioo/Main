<?php

class DependencySuccessTest extends PHPUnit_Framework_TestCase
{
    public function test_one() {}

    /**
     * @depends test_one
     */
    public function test_two() {}

    /**
     * @depends DependencySuccessTest::test_two
     */
    public function test_three() {}
}
