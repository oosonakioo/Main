<?php

namespace Hamcrest\Arrays;

use Hamcrest\AbstractMatcherTest;

class IsArrayTest extends AbstractMatcherTest
{
    protected function createMatcher()
    {
        return IsArray::anArray([equalTo('irrelevant')]);
    }

    public function test_matches_an_array_that_matches_all_the_element_matchers()
    {
        $this->assertMatches(
            anArray([equalTo('a'), equalTo('b'), equalTo('c')]),
            ['a', 'b', 'c'],
            'should match array with matching elements'
        );
    }

    public function test_does_not_match_an_array_when_elements_do_not_match()
    {
        $this->assertDoesNotMatch(
            anArray([equalTo('a'), equalTo('b')]),
            ['b', 'c'],
            'should not match array with different elements'
        );
    }

    public function test_does_not_match_an_array_of_different_size()
    {
        $this->assertDoesNotMatch(
            anArray([equalTo('a'), equalTo('b')]),
            ['a', 'b', 'c'],
            'should not match larger array'
        );
        $this->assertDoesNotMatch(
            anArray([equalTo('a'), equalTo('b')]),
            ['a'],
            'should not match smaller array'
        );
    }

    public function test_does_not_match_null()
    {
        $this->assertDoesNotMatch(
            anArray([equalTo('a')]),
            null,
            'should not match null'
        );
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription(
            '["a", "b"]',
            anArray([equalTo('a'), equalTo('b')])
        );
    }

    public function test_has_a_readable_mismatch_description_when_keys_dont_match()
    {
        $this->assertMismatchDescription(
            'array keys were [<1>, <2>]',
            anArray([equalTo('a'), equalTo('b')]),
            [1 => 'a', 2 => 'b']
        );
    }

    public function test_supports_matches_associative_arrays()
    {
        $this->assertMatches(
            anArray(['x' => equalTo('a'), 'y' => equalTo('b'), 'z' => equalTo('c')]),
            ['x' => 'a', 'y' => 'b', 'z' => 'c'],
            'should match associative array with matching elements'
        );
    }

    public function test_does_not_match_an_associative_array_when_keys_do_not_match()
    {
        $this->assertDoesNotMatch(
            anArray(['x' => equalTo('a'), 'y' => equalTo('b')]),
            ['x' => 'b', 'z' => 'c'],
            'should not match array with different keys'
        );
    }
}
