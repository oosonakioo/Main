<?php

class IsolationTest extends PHPUnit_Framework_TestCase
{
    public function test_is_in_isolation_returns_false()
    {
        $this->assertFalse($this->isInIsolation());
    }

    public function test_is_in_isolation_returns_true()
    {
        $this->assertTrue($this->isInIsolation());
    }
}
