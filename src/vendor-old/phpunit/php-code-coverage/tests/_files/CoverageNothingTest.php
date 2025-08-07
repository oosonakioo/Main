<?php

class CoverageNothingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers CoveredClass::publicMethod
     *
     * @coversNothing
     */
    public function test_something()
    {
        $o = new CoveredClass;
        $o->publicMethod();
    }
}
