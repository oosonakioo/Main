<?php

class ChangeCurrentWorkingDirectoryTest extends PHPUnit_Framework_TestCase
{
    public function test_something_that_changes_the_cwd()
    {
        chdir('../');
        $this->assertTrue(true);
    }
}
