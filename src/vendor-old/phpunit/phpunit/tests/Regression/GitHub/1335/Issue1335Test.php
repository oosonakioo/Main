<?php

/**
 * @runTestsInSeparateProcesses
 *
 * @preserveGlobalState enabled
 */
class Issue1335Test extends PHPUnit_Framework_TestCase
{
    public function test_global_string()
    {
        $this->assertEquals('Hello', $GLOBALS['globalString']);
    }

    public function test_global_int_truthy()
    {
        $this->assertEquals(1, $GLOBALS['globalIntTruthy']);
    }

    public function test_global_int_falsey()
    {
        $this->assertEquals(0, $GLOBALS['globalIntFalsey']);
    }

    public function test_global_float()
    {
        $this->assertEquals(1.123, $GLOBALS['globalFloat']);
    }

    public function test_global_bool_true()
    {
        $this->assertEquals(true, $GLOBALS['globalBoolTrue']);
    }

    public function test_global_bool_false()
    {
        $this->assertEquals(false, $GLOBALS['globalBoolFalse']);
    }

    public function test_global_null()
    {
        $this->assertEquals(null, $GLOBALS['globalNull']);
    }

    public function test_global_array()
    {
        $this->assertEquals(['foo'], $GLOBALS['globalArray']);
    }

    public function test_global_nested_array()
    {
        $this->assertEquals([['foo']], $GLOBALS['globalNestedArray']);
    }

    public function test_global_object()
    {
        $this->assertEquals((object) ['foo' => 'bar'], $GLOBALS['globalObject']);
    }

    public function test_global_object_with_back_slash_string()
    {
        $this->assertEquals((object) ['foo' => 'back\\slash'], $GLOBALS['globalObjectWithBackSlashString']);
    }

    public function test_global_object_with_double_back_slash_string()
    {
        $this->assertEquals((object) ['foo' => 'back\\\\slash'], $GLOBALS['globalObjectWithDoubleBackSlashString']);
    }
}
