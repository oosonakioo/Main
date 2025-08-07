<?php

class NamespaceCoverageClassExtendedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Foo\CoveredClass<extended>
     */
    public function test_something()
    {
        $o = new Foo\CoveredClass;
        $o->publicMethod();
    }
}
