<?php

class Issue1437Test extends PHPUnit_Framework_TestCase
{
    public function test_failure()
    {
        ob_start();
        $this->assertTrue(false);
    }
}
