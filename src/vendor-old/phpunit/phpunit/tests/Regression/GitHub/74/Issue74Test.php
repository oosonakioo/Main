<?php

class Issue74Test extends PHPUnit_Framework_TestCase
{
    public function test_create_and_throw_new_exception_in_process_isolation()
    {
        require_once __DIR__.'/NewException.php';
        throw new NewException('Testing GH-74');
    }
}
