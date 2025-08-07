<?php

namespace Hamcrest\Type;

class IsIntegerTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsInteger::integerValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat(5, integerValue());
        assertThat(0, integerValue());
        assertThat(-5, integerValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(integerValue()));
        assertThat(5.2, not(integerValue()));
        assertThat('foo', not(integerValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('an integer', integerValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', integerValue(), null);
        $this->assertMismatchDescription('was a string "foo"', integerValue(), 'foo');
    }
}
