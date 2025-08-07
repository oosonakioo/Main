<?php

class IncompleteTest extends PHPUnit_Framework_TestCase
{
    public function test_incomplete()
    {
        $this->markTestIncomplete('Test incomplete');
    }
}
