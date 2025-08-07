<?php

class NotPublicTestCase extends PHPUnit_Framework_TestCase
{
    public function test_public() {}

    protected function test_not_public() {}
}
