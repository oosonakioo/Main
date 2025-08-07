<?php

namespace Hamcrest\Collection;

use Hamcrest\AbstractMatcherTest;

class IsEmptyTraversableTest extends AbstractMatcherTest
{
    protected function createMatcher()
    {
        return IsEmptyTraversable::emptyTraversable();
    }

    public function test_empty_matcher_matches_when_empty()
    {
        $this->assertMatches(
            emptyTraversable(),
            new \ArrayObject([]),
            'an empty traversable'
        );
    }

    public function test_empty_matcher_does_not_match_when_not_empty()
    {
        $this->assertDoesNotMatch(
            emptyTraversable(),
            new \ArrayObject([1, 2, 3]),
            'a non-empty traversable'
        );
    }

    public function test_empty_matcher_does_not_match_null()
    {
        $this->assertDoesNotMatch(
            emptyTraversable(),
            null,
            'should not match null'
        );
    }

    public function test_empty_matcher_has_a_readable_description()
    {
        $this->assertDescription('an empty traversable', emptyTraversable());
    }

    public function test_non_empty_does_not_match_null()
    {
        $this->assertDoesNotMatch(
            nonEmptyTraversable(),
            null,
            'should not match null'
        );
    }

    public function test_non_empty_does_not_match_when_empty()
    {
        $this->assertDoesNotMatch(
            nonEmptyTraversable(),
            new \ArrayObject([]),
            'an empty traversable'
        );
    }

    public function test_non_empty_matches_when_not_empty()
    {
        $this->assertMatches(
            nonEmptyTraversable(),
            new \ArrayObject([1, 2, 3]),
            'a non-empty traversable'
        );
    }

    public function test_non_empty_non_empty_matcher_has_a_readable_description()
    {
        $this->assertDescription('a non-empty traversable', nonEmptyTraversable());
    }
}
