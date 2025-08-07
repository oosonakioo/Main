<?php

class NamespaceCoveragePublicTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Foo\CoveredClass::<public>
     */
    public function test_something()
    {
        $o = new Foo\CoveredClass;
        $o->publicMethod();
    }
}
