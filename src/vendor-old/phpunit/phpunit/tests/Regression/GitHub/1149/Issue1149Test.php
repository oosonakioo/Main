<?php

class Issue1149Test extends PHPUnit_Framework_TestCase
{
    public function test_one()
    {
        $this->assertTrue(true);
        echo '1';
    }

    /**
     * @runInSeparateProcess
     */
    public function test_two()
    {
        $this->assertTrue(true);
        echo '2';
    }
}
