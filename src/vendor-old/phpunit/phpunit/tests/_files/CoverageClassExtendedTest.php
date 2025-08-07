<?php

class CoverageClassExtendedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers CoveredClass<extended>
     */
    public function test_something()
    {
        $o = new CoveredClass;
        $o->publicMethod();
    }
}
