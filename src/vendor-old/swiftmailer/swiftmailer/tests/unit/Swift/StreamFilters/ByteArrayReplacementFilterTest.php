<?php

class Swift_StreamFilters_ByteArrayReplacementFilterTest extends \PHPUnit_Framework_TestCase
{
    public function test_basic_replacements_are_made()
    {
        $filter = $this->_createFilter([0x61, 0x62], [0x63, 0x64]);
        $this->assertEquals(
            [0x59, 0x60, 0x63, 0x64, 0x65],
            $filter->filter([0x59, 0x60, 0x61, 0x62, 0x65])
        );
    }

    public function test_should_buffer_returns_true_if_partial_match_at_end_of_buffer()
    {
        $filter = $this->_createFilter([0x61, 0x62], [0x63, 0x64]);
        $this->assertTrue($filter->shouldBuffer([0x59, 0x60, 0x61]),
            '%s: Filter should buffer since 0x61 0x62 is the needle and the ending '.
            '0x61 could be from 0x61 0x62'
        );
    }

    public function test_filter_can_make_multiple_replacements()
    {
        $filter = $this->_createFilter([[0x61], [0x62]], [0x63]);
        $this->assertEquals(
            [0x60, 0x63, 0x60, 0x63, 0x60],
            $filter->filter([0x60, 0x61, 0x60, 0x62, 0x60])
        );
    }

    public function test_multiple_replacements_can_be_different()
    {
        $filter = $this->_createFilter([[0x61], [0x62]], [[0x63], [0x64]]);
        $this->assertEquals(
            [0x60, 0x63, 0x60, 0x64, 0x60],
            $filter->filter([0x60, 0x61, 0x60, 0x62, 0x60])
        );
    }

    public function test_should_buffer_returns_false_if_partial_match_not_at_end_of_string()
    {
        $filter = $this->_createFilter([0x0D, 0x0A], [0x0A]);
        $this->assertFalse($filter->shouldBuffer([0x61, 0x62, 0x0D, 0x0A, 0x63]),
            '%s: Filter should not buffer since x0Dx0A is the needle and is not at EOF'
        );
    }

    public function test_should_buffer_returns_true_if_any_of_multiple_matches_at_end_of_string()
    {
        $filter = $this->_createFilter([[0x61, 0x62], [0x63]], [0x64]);
        $this->assertTrue($filter->shouldBuffer([0x59, 0x60, 0x61]),
            '%s: Filter should buffer since 0x61 0x62 is a needle and the ending '.
            '0x61 could be from 0x61 0x62'
        );
    }

    public function test_converting_all_line_endings_to_crlf_when_input_is_lf()
    {
        $filter = $this->_createFilter(
            [[0x0D, 0x0A], [0x0D], [0x0A]],
            [[0x0A], [0x0A], [0x0D, 0x0A]]
        );

        $this->assertEquals(
            [0x60, 0x0D, 0x0A, 0x61, 0x0D, 0x0A, 0x62, 0x0D, 0x0A, 0x63],
            $filter->filter([0x60, 0x0A, 0x61, 0x0A, 0x62, 0x0A, 0x63])
        );
    }

    public function test_converting_all_line_endings_to_crlf_when_input_is_cr()
    {
        $filter = $this->_createFilter(
            [[0x0D, 0x0A], [0x0D], [0x0A]],
            [[0x0A], [0x0A], [0x0D, 0x0A]]
        );

        $this->assertEquals(
            [0x60, 0x0D, 0x0A, 0x61, 0x0D, 0x0A, 0x62, 0x0D, 0x0A, 0x63],
            $filter->filter([0x60, 0x0D, 0x61, 0x0D, 0x62, 0x0D, 0x63])
        );
    }

    public function test_converting_all_line_endings_to_crlf_when_input_is_crlf()
    {
        $filter = $this->_createFilter(
            [[0x0D, 0x0A], [0x0D], [0x0A]],
            [[0x0A], [0x0A], [0x0D, 0x0A]]
        );

        $this->assertEquals(
            [0x60, 0x0D, 0x0A, 0x61, 0x0D, 0x0A, 0x62, 0x0D, 0x0A, 0x63],
            $filter->filter([0x60, 0x0D, 0x0A, 0x61, 0x0D, 0x0A, 0x62, 0x0D, 0x0A, 0x63])
        );
    }

    public function test_converting_all_line_endings_to_crlf_when_input_is_lfcr()
    {
        $filter = $this->_createFilter(
            [[0x0D, 0x0A], [0x0D], [0x0A]],
            [[0x0A], [0x0A], [0x0D, 0x0A]]
        );

        $this->assertEquals(
            [0x60, 0x0D, 0x0A, 0x0D, 0x0A, 0x61, 0x0D, 0x0A, 0x0D, 0x0A, 0x62, 0x0D, 0x0A, 0x0D, 0x0A, 0x63],
            $filter->filter([0x60, 0x0A, 0x0D, 0x61, 0x0A, 0x0D, 0x62, 0x0A, 0x0D, 0x63])
        );
    }

    public function test_converting_all_line_endings_to_crlf_when_input_contains_lflf()
    {
        // Lighthouse Bug #23

        $filter = $this->_createFilter(
            [[0x0D, 0x0A], [0x0D], [0x0A]],
            [[0x0A], [0x0A], [0x0D, 0x0A]]
        );

        $this->assertEquals(
            [0x60, 0x0D, 0x0A, 0x0D, 0x0A, 0x61, 0x0D, 0x0A, 0x0D, 0x0A, 0x62, 0x0D, 0x0A, 0x0D, 0x0A, 0x63],
            $filter->filter([0x60, 0x0A, 0x0A, 0x61, 0x0A, 0x0A, 0x62, 0x0A, 0x0A, 0x63])
        );
    }

    private function _createFilter($search, $replace)
    {
        return new Swift_StreamFilters_ByteArrayReplacementFilter($search, $replace);
    }
}
