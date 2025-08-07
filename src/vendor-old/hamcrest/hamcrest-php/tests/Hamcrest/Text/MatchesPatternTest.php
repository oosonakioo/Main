<?php

namespace Hamcrest\Text;

class MatchesPatternTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return matchesPattern('/o+b/');
    }

    public function test_evaluates_to_true_if_argumentmatches_pattern()
    {
        assertThat('foobar', matchesPattern('/o+b/'));
        assertThat('foobar', matchesPattern('/^foo/'));
        assertThat('foobar', matchesPattern('/ba*r$/'));
        assertThat('foobar', matchesPattern('/^foobar$/'));
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_regex()
    {
        assertThat('foobar', not(matchesPattern('/^foob$/')));
        assertThat('foobar', not(matchesPattern('/oobe/')));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a string matching "pattern"', matchesPattern('pattern'));
    }
}
