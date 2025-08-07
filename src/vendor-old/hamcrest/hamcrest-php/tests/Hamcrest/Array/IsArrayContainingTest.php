<?php

namespace Hamcrest\Arrays;

use Hamcrest\AbstractMatcherTest;

class IsArrayContainingTest extends AbstractMatcherTest
{
    protected function createMatcher()
    {
        return IsArrayContaining::hasItemInArray('irrelevant');
    }

    public function test_matches_an_array_that_contains_an_element_matching_the_given_matcher()
    {
        $this->assertMatches(
            hasItemInArray('a'),
            ['a', 'b', 'c'],
            "should matches array that contains 'a'"
        );
    }

    public function test_does_not_match_an_array_that_doesnt_contain_an_element_matching_the_given_matcher()
    {
        $this->assertDoesNotMatch(
            hasItemInArray('a'),
            ['b', 'c'],
            "should not matches array that doesn't contain 'a'"
        );
        $this->assertDoesNotMatch(
            hasItemInArray('a'),
            [],
            'should not match empty array'
        );
    }

    public function test_does_not_match_null()
    {
        $this->assertDoesNotMatch(
            hasItemInArray('a'),
            null,
            'should not match null'
        );
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('an array containing "a"', hasItemInArray('a'));
    }
}
