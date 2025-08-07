<?php

class DataProviderSkippedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider skippedTestProviderMethod
     */
    public function test_skipped($a, $b, $c)
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider providerMethod
     */
    public function test_add($a, $b, $c)
    {
        $this->assertEquals($c, $a + $b);
    }

    public function skippedTestProviderMethod()
    {
        $this->markTestSkipped('skipped');

        return [
            [0, 0, 0],
            [0, 1, 1],
        ];
    }

    public static function providerMethod()
    {
        return [
            [0, 0, 0],
            [0, 1, 1],
        ];
    }
}
