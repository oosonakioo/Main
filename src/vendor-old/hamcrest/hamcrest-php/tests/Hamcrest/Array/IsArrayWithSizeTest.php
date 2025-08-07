<?php

namespace Hamcrest\Arrays;

use Hamcrest\AbstractMatcherTest;

class IsArrayWithSizeTest extends AbstractMatcherTest
{
    protected function createMatcher()
    {
        return IsArrayWithSize::arrayWithSize(equalTo(2));
    }

    public function test_matches_when_size_is_correct()
    {
        $this->assertMatches(arrayWithSize(equalTo(3)), [1, 2, 3], 'correct size');
        $this->assertDoesNotMatch(arrayWithSize(equalTo(2)), [1, 2, 3], 'incorrect size');
    }

    public function test_provides_convenient_shortcut_for_array_with_size_equal_to()
    {
        $this->assertMatches(arrayWithSize(3), [1, 2, 3], 'correct size');
        $this->assertDoesNotMatch(arrayWithSize(2), [1, 2, 3], 'incorrect size');
    }

    public function test_empty_array()
    {
        $this->assertMatches(emptyArray(), [], 'correct size');
        $this->assertDoesNotMatch(emptyArray(), [1], 'incorrect size');
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('an array with size <3>', arrayWithSize(equalTo(3)));
        $this->assertDescription('an empty array', emptyArray());
    }
}
