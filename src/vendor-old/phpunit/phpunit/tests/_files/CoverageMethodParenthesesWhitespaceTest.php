<?php

class CoverageMethodParenthesesWhitespaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers CoveredClass::publicMethod ( )
     */
    public function test_something()
    {
        $o = new CoveredClass;
        $o->publicMethod();
    }
}
