<?php

namespace Hamcrest\Core;

class IsNotTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\IsNot::not('something');
    }

    public function test_evaluates_to_the_the_logical_negation_of_another_matcher()
    {
        $this->assertMatches(not(equalTo('A')), 'B', 'should match');
        $this->assertDoesNotMatch(not(equalTo('B')), 'B', 'should not match');
    }

    public function test_provides_convenient_shortcut_for_not_equal_to()
    {
        $this->assertMatches(not('A'), 'B', 'should match');
        $this->assertMatches(not('B'), 'A', 'should match');
        $this->assertDoesNotMatch(not('A'), 'A', 'should not match');
        $this->assertDoesNotMatch(not('B'), 'B', 'should not match');
    }

    public function test_uses_description_of_negated_matcher_with_prefix()
    {
        $this->assertDescription('not a value greater than <2>', not(greaterThan(2)));
        $this->assertDescription('not "A"', not('A'));
    }
}
