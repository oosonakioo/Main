<?php

class Issue1471Test extends PHPUnit_Framework_TestCase
{
    public function test_failure()
    {
        $this->expectOutputString('*');

        echo '*';

        $this->assertTrue(false);
    }
}
