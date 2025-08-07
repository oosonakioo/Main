<?php

class NamespaceCoverageClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Foo\CoveredClass
     */
    public function test_something()
    {
        $o = new Foo\CoveredClass;
        $o->publicMethod();
    }
}
