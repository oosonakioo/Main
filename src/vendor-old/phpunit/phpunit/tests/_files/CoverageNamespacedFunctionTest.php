<?php

class CoverageNamespacedFunctionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers foo\func()
     */
    public function test_func()
    {
        foo\func();
    }
}
