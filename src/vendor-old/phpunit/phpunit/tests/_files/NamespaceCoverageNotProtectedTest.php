<?php

class NamespaceCoverageNotProtectedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Foo\CoveredClass::<!protected>
     */
    public function test_something()
    {
        $o = new Foo\CoveredClass;
        $o->publicMethod();
    }
}
