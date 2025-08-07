<?php

class FatalTest extends PHPUnit_Framework_TestCase
{
    public function test_fatal_error()
    {
        if (extension_loaded('xdebug')) {
            xdebug_disable();
        }

        eval('class FatalTest {}');
    }
}
