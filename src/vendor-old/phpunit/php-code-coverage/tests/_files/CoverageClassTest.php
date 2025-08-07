<?php

class CoverageClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers CoveredClass
     */
    public function test_something()
    {
        $o = new CoveredClass;
        $o->publicMethod();
    }
}
