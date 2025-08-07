<?php

class CoverageNoneTest extends PHPUnit_Framework_TestCase
{
    public function test_something()
    {
        $o = new CoveredClass;
        $o->publicMethod();
    }
}
