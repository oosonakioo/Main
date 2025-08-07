<?php

class Issue765Test extends PHPUnit_Framework_TestCase
{
    public function test_dependee()
    {
        $this->assertTrue(true);
    }

    /**
     * @depends test_dependee
     *
     * @dataProvider dependentProvider
     */
    public function test_dependent($a)
    {
        $this->assertTrue(true);
    }

    public function dependentProvider()
    {
        throw new Exception;
    }
}
