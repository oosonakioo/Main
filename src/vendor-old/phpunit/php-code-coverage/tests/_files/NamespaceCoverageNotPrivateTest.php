<?php

class NamespaceCoverageNotPrivateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Foo\CoveredClass::<!private>
     */
    public function test_something()
    {
        $o = new Foo\CoveredClass;
        $o->publicMethod();
    }
}
