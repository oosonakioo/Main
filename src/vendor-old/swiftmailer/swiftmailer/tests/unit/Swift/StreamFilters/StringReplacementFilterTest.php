<?php

class Swift_StreamFilters_StringReplacementFilterTest extends \PHPUnit_Framework_TestCase
{
    public function test_basic_replacements_are_made()
    {
        $filter = $this->_createFilter('foo', 'bar');
        $this->assertEquals('XbarYbarZ', $filter->filter('XfooYfooZ'));
    }

    public function test_should_buffer_returns_true_if_partial_match_at_end_of_buffer()
    {
        $filter = $this->_createFilter('foo', 'bar');
        $this->assertTrue($filter->shouldBuffer('XfooYf'),
            '%s: Filter should buffer since "foo" is the needle and the ending '.
            '"f" could be from "foo"'
        );
    }

    public function test_filter_can_make_multiple_replacements()
    {
        $filter = $this->_createFilter(['a', 'b'], 'foo');
        $this->assertEquals('XfooYfooZ', $filter->filter('XaYbZ'));
    }

    public function test_multiple_replacements_can_be_different()
    {
        $filter = $this->_createFilter(['a', 'b'], ['foo', 'zip']);
        $this->assertEquals('XfooYzipZ', $filter->filter('XaYbZ'));
    }

    public function test_should_buffer_returns_false_if_partial_match_not_at_end_of_string()
    {
        $filter = $this->_createFilter("\r\n", "\n");
        $this->assertFalse($filter->shouldBuffer("foo\r\nbar"),
            '%s: Filter should not buffer since x0Dx0A is the needle and is not at EOF'
        );
    }

    public function test_should_buffer_returns_true_if_any_of_multiple_matches_at_end_of_string()
    {
        $filter = $this->_createFilter(['foo', 'zip'], 'bar');
        $this->assertTrue($filter->shouldBuffer('XfooYzi'),
            '%s: Filter should buffer since "zip" is a needle and the ending '.
            '"zi" could be from "zip"'
        );
    }

    public function test_should_buffer_returns_false_on_empty_buffer()
    {
        $filter = $this->_createFilter("\r\n", "\n");
        $this->assertFalse($filter->shouldBuffer(''));
    }

    private function _createFilter($search, $replace)
    {
        return new Swift_StreamFilters_StringReplacementFilter($search, $replace);
    }
}
