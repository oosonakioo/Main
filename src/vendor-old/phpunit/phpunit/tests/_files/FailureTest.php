<?php

class FailureTest extends PHPUnit_Framework_TestCase
{
    public function test_assert_array_equals_array()
    {
        $this->assertEquals([1], [2], 'message');
    }

    public function test_assert_integer_equals_integer()
    {
        $this->assertEquals(1, 2, 'message');
    }

    public function test_assert_object_equals_object()
    {
        $a = new StdClass;
        $a->foo = 'bar';

        $b = new StdClass;
        $b->bar = 'foo';

        $this->assertEquals($a, $b, 'message');
    }

    public function test_assert_null_equals_string()
    {
        $this->assertEquals(null, 'bar', 'message');
    }

    public function test_assert_string_equals_string()
    {
        $this->assertEquals('foo', 'bar', 'message');
    }

    public function test_assert_text_equals_text()
    {
        $this->assertEquals("foo\nbar\n", "foo\nbaz\n", 'message');
    }

    public function test_assert_string_matches_format()
    {
        $this->assertStringMatchesFormat('*%s*', '**', 'message');
    }

    public function test_assert_numeric_equals_numeric()
    {
        $this->assertEquals(1, 2, 'message');
    }

    public function test_assert_text_same_text()
    {
        $this->assertSame('foo', 'bar', 'message');
    }

    public function test_assert_object_same_object()
    {
        $this->assertSame(new StdClass, new StdClass, 'message');
    }

    public function test_assert_object_same_null()
    {
        $this->assertSame(new StdClass, null, 'message');
    }

    public function test_assert_float_same_float()
    {
        $this->assertSame(1.0, 1.5, 'message');
    }

    // Note that due to the implementation of this assertion it counts as 2 asserts
    public function test_assert_string_matches_format_file()
    {
        $this->assertStringMatchesFormatFile(__DIR__.'/expectedFileFormat.txt', '...BAR...');
    }
}
