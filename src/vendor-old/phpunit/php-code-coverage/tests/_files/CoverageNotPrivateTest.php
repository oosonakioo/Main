<?php

class CoverageNotPrivateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers CoveredClass::<!private>
     */
    public function test_something()
    {
        $o = new CoveredClass;
        $o->publicMethod();
    }
}
