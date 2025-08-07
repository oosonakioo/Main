<?php

class StatusTest extends PHPUnit_Framework_TestCase
{
    public function test_success()
    {
        $this->assertTrue(true);
    }

    public function test_failure()
    {
        $this->assertTrue(false);
    }

    public function test_error()
    {
        throw new \Exception;
    }

    public function test_incomplete()
    {
        $this->markTestIncomplete();
    }

    public function test_skipped()
    {
        $this->markTestSkipped();
    }

    public function test_risky() {}
}
