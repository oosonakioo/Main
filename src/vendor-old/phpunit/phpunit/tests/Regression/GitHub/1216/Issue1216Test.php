<?php

class Issue1216Test extends PHPUnit_Framework_TestCase
{
    public function test_config_available_in_bootstrap()
    {
        $this->assertTrue($_ENV['configAvailableInBootstrap']);
    }
}
