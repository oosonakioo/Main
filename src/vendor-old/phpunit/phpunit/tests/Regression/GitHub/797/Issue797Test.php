<?php

class Issue797Test extends PHPUnit_Framework_TestCase
{
    protected $preserveGlobalState = false;

    public function test_bootstrap_php_is_executed_in_isolation()
    {
        $this->assertEquals(GITHUB_ISSUE, 797);
    }
}
