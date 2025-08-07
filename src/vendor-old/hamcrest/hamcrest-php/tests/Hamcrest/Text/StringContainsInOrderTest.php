<?php

namespace Hamcrest\Text;

class StringContainsInOrderTest extends \Hamcrest\AbstractMatcherTest
{
    private $_m;

    protected function setUp()
    {
        $this->_m = \Hamcrest\Text\StringContainsInOrder::stringContainsInOrder(['a', 'b', 'c']);
    }

    protected function createMatcher()
    {
        return $this->_m;
    }

    public function test_matches_only_if_string_contains_given_substrings_in_the_same_order()
    {
        $this->assertMatches($this->_m, 'abc', 'substrings in order');
        $this->assertMatches($this->_m, '1a2b3c4', 'substrings separated');

        $this->assertDoesNotMatch($this->_m, 'cab', 'substrings out of order');
        $this->assertDoesNotMatch($this->_m, 'xyz', 'no substrings in string');
        $this->assertDoesNotMatch($this->_m, 'ac', 'substring missing');
        $this->assertDoesNotMatch($this->_m, '', 'empty string');
    }

    public function test_accepts_variable_arguments()
    {
        $this->assertMatches(stringContainsInOrder('a', 'b', 'c'), 'abc', 'substrings as variable arguments');
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription(
            'a string containing "a", "b", "c" in order',
            $this->_m
        );
    }
}
