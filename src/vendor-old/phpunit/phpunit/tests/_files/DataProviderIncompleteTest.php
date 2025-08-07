<?php

class DataProviderIncompleteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider incompleteTestProviderMethod
     */
    public function test_incomplete($a, $b, $c)
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

    public function incompleteTestProviderMethod()
    {
        $this->markTestIncomplete('incomplete');

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
