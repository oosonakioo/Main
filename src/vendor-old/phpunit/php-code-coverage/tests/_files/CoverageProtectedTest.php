<?php

class CoverageProtectedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers CoveredClass::<protected>
     */
    public function test_something()
    {
        $o = new CoveredClass;
        $o->publicMethod();
    }
}
