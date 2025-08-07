<?php

namespace Hamcrest\Collection;

class IsTraversableWithSizeTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Collection\IsTraversableWithSize::traversableWithSize(
            equalTo(2)
        );
    }

    public function test_matches_when_size_is_correct()
    {
        $this->assertMatches(
            traversableWithSize(equalTo(3)),
            new \ArrayObject([1, 2, 3]),
            'correct size'
        );
    }

    public function test_does_not_match_when_size_is_incorrect()
    {
        $this->assertDoesNotMatch(
            traversableWithSize(equalTo(2)),
            new \ArrayObject([1, 2, 3]),
            'incorrect size'
        );
    }

    public function test_does_not_match_null()
    {
        $this->assertDoesNotMatch(
            traversableWithSize(3),
            null,
            'should not match null'
        );
    }

    public function test_provides_convenient_shortcut_for_traversable_with_size_equal_to()
    {
        $this->assertMatches(
            traversableWithSize(3),
            new \ArrayObject([1, 2, 3]),
            'correct size'
        );
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription(
            'a traversable with size <3>',
            traversableWithSize(equalTo(3))
        );
    }
}
