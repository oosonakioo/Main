<?php

class NamespaceCoverageMethodTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Foo\CoveredClass::publicMethod
     */
    public function test_something()
    {
        $o = new Foo\CoveredClass;
        $o->publicMethod();
    }
}
