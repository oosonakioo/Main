<?php

namespace Hamcrest\Arrays;

use Hamcrest\AbstractMatcherTest;

class IsArrayContainingKeyTest extends AbstractMatcherTest
{
    protected function createMatcher()
    {
        return IsArrayContainingKey::hasKeyInArray('irrelevant');
    }

    public function test_matches_single_element_array_containing_key()
    {
        $array = ['a' => 1];

        $this->assertMatches(hasKey('a'), $array, 'Matches single key');
    }

    public function test_matches_array_containing_key()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertMatches(hasKey('a'), $array, 'Matches a');
        $this->assertMatches(hasKey('c'), $array, 'Matches c');
    }

    public function test_matches_array_containing_key_with_integer_keys()
    {
        $array = [1 => 'A', 2 => 'B'];

        assertThat($array, hasKey(1));
    }

    public function test_matches_array_containing_key_with_number_keys()
    {
        $array = [1 => 'A', 2 => 'B'];

        assertThat($array, hasKey(1));

        // very ugly version!
        assertThat($array, IsArrayContainingKey::hasKeyInArray(2));
    }

    public function test_has_readable_description()
    {
        $this->assertDescription('array with key "a"', hasKey('a'));
    }

    public function test_does_not_match_empty_array()
    {
        $this->assertMismatchDescription('array was []', hasKey('Foo'), []);
    }

    public function test_does_not_match_array_missing_key()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertMismatchDescription('array was ["a" => <1>, "b" => <2>, "c" => <3>]', hasKey('d'), $array);
    }
}
