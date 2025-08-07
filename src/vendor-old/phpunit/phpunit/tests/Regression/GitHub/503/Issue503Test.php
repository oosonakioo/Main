<?php

class Issue503Test extends PHPUnit_Framework_TestCase
{
    public function test_compare_different_line_endings()
    {
        $this->assertSame(
            "foo\n",
            "foo\r\n"
        );
    }
}
