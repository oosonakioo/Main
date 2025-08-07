<?php

/**
 * @requires extension I_DO_NOT_EXIST
 */
class Issue1374Test extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        echo __FUNCTION__;
    }

    public function test_something()
    {
        $this->fail('This should not be reached');
    }

    protected function tearDown()
    {
        echo __FUNCTION__;
    }
}
