<?php

class CoveragePublicTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers CoveredClass::<public>
     */
    public function test_something()
    {
        $o = new CoveredClass;
        $o->publicMethod();
    }
}
