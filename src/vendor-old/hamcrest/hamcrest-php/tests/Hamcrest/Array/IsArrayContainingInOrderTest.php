<?php

namespace Hamcrest\Arrays;

use Hamcrest\AbstractMatcherTest;

class IsArrayContainingInOrderTest extends AbstractMatcherTest
{
    protected function createMatcher()
    {
        return IsArrayContainingInOrder::arrayContaining([1, 2]);
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('[<1>, <2>]', arrayContaining([1, 2]));
    }

    public function test_matches_items_in_order()
    {
        $this->assertMatches(arrayContaining([1, 2, 3]), [1, 2, 3], 'in order');
        $this->assertMatches(arrayContaining([1]), [1], 'single');
    }

    public function test_applies_matchers_in_order()
    {
        $this->assertMatches(
            arrayContaining([1, 2, 3]),
            [1, 2, 3],
            'in order'
        );
        $this->assertMatches(arrayContaining([1]), [1], 'single');
    }

    public function test_mismatches_items_in_any_order()
    {
        $matcher = arrayContaining([1, 2, 3]);
        $this->assertMismatchDescription('was null', $matcher, null);
        $this->assertMismatchDescription('No item matched: <1>', $matcher, []);
        $this->assertMismatchDescription('No item matched: <2>', $matcher, [1]);
        $this->assertMismatchDescription('item with key 0: was <4>', $matcher, [4, 3, 2, 1]);
        $this->assertMismatchDescription('item with key 2: was <4>', $matcher, [1, 2, 4]);
    }
}
