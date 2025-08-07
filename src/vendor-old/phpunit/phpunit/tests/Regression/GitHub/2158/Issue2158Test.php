<?php

class Issue2158Test extends PHPUnit_Framework_TestCase
{
    /**
     * Set constant in main process
     */
    public function test_something()
    {
        include __DIR__.'/constant.inc';
        $this->assertTrue(true);
    }

    /**
     * Constant defined previously in main process constant should be available and
     * no errors should be yielded by reload of included files
     *
     * @runInSeparateProcess
     */
    public function test_something_else()
    {
        $this->assertTrue(defined('TEST_CONSTANT'));
    }
}
