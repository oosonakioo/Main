<?php

class TemplateMethodsTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        echo __METHOD__."\n";
    }

    protected function setUp()
    {
        echo __METHOD__."\n";
    }

    protected function assertPreConditions()
    {
        echo __METHOD__."\n";
    }

    public function test_one()
    {
        echo __METHOD__."\n";
        $this->assertTrue(true);
    }

    public function test_two()
    {
        echo __METHOD__."\n";
        $this->assertTrue(false);
    }

    protected function assertPostConditions()
    {
        echo __METHOD__."\n";
    }

    protected function tearDown()
    {
        echo __METHOD__."\n";
    }

    public static function tearDownAfterClass()
    {
        echo __METHOD__."\n";
    }

    protected function onNotSuccessfulTest(Exception $e)
    {
        echo __METHOD__."\n";
        throw $e;
    }
}
