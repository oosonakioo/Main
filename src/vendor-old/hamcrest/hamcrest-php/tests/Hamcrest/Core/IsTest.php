<?php

namespace Hamcrest\Core;

class IsTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\Is::is('something');
    }

    public function test_just_matches_the_same_way_the_underyling_matcher_does()
    {
        $this->assertMatches(is(equalTo(true)), true, 'should match');
        $this->assertMatches(is(equalTo(false)), false, 'should match');
        $this->assertDoesNotMatch(is(equalTo(true)), false, 'should not match');
        $this->assertDoesNotMatch(is(equalTo(false)), true, 'should not match');
    }

    public function test_generates_is_prefix_in_description()
    {
        $this->assertDescription('is <true>', is(equalTo(true)));
    }

    public function test_provides_convenient_shortcut_for_is_equal_to()
    {
        $this->assertMatches(is('A'), 'A', 'should match');
        $this->assertMatches(is('B'), 'B', 'should match');
        $this->assertDoesNotMatch(is('A'), 'B', 'should not match');
        $this->assertDoesNotMatch(is('B'), 'A', 'should not match');
        $this->assertDescription('is "A"', is('A'));
    }
}
