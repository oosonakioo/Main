<?php

class NotExistingCoveredElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers NotExistingClass
     */
    public function test_one() {}

    /**
     * @covers NotExistingClass::notExistingMethod
     */
    public function test_two() {}

    /**
     * @covers NotExistingClass::<public>
     */
    public function test_three() {}
}
