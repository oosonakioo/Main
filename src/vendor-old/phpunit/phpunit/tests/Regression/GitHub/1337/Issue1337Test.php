<?php

class Issue1337Test extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test_provider($a)
    {
        $this->assertTrue($a);
    }

    public function dataProvider()
    {
        return [
            'c:\\' => [true],
            0.9 => [true],
        ];
    }
}
