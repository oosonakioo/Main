<?php

class Issue1348Test extends PHPUnit_Framework_TestCase
{
    public function test_stdout()
    {
        fwrite(STDOUT, "\nSTDOUT does not break test result\n");
        $this->assertTrue(true);
    }

    public function test_stderr()
    {
        fwrite(STDERR, 'STDERR works as usual.');
    }
}
