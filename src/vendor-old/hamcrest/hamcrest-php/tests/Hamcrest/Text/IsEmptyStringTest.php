<?php

namespace Hamcrest\Text;

class IsEmptyStringTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Text\IsEmptyString::isEmptyOrNullString();
    }

    public function test_empty_does_not_match_null()
    {
        $this->assertDoesNotMatch(emptyString(), null, 'null');
    }

    public function test_empty_does_not_match_zero()
    {
        $this->assertDoesNotMatch(emptyString(), 0, 'zero');
    }

    public function test_empty_does_not_match_false()
    {
        $this->assertDoesNotMatch(emptyString(), false, 'false');
    }

    public function test_empty_does_not_match_empty_array()
    {
        $this->assertDoesNotMatch(emptyString(), [], 'empty array');
    }

    public function test_empty_matches_empty_string()
    {
        $this->assertMatches(emptyString(), '', 'empty string');
    }

    public function test_empty_does_not_match_non_empty_string()
    {
        $this->assertDoesNotMatch(emptyString(), 'foo', 'non-empty string');
    }

    public function test_empty_has_a_readable_description()
    {
        $this->assertDescription('an empty string', emptyString());
    }

    public function test_empty_or_null_matches_null()
    {
        $this->assertMatches(nullOrEmptyString(), null, 'null');
    }

    public function test_empty_or_null_matches_empty_string()
    {
        $this->assertMatches(nullOrEmptyString(), '', 'empty string');
    }

    public function test_empty_or_null_does_not_match_non_empty_string()
    {
        $this->assertDoesNotMatch(nullOrEmptyString(), 'foo', 'non-empty string');
    }

    public function test_empty_or_null_has_a_readable_description()
    {
        $this->assertDescription('(null or an empty string)', nullOrEmptyString());
    }

    public function test_non_empty_does_not_match_null()
    {
        $this->assertDoesNotMatch(nonEmptyString(), null, 'null');
    }

    public function test_non_empty_does_not_match_empty_string()
    {
        $this->assertDoesNotMatch(nonEmptyString(), '', 'empty string');
    }

    public function test_non_empty_matches_non_empty_string()
    {
        $this->assertMatches(nonEmptyString(), 'foo', 'non-empty string');
    }

    public function test_non_empty_has_a_readable_description()
    {
        $this->assertDescription('a non-empty string', nonEmptyString());
    }
}
