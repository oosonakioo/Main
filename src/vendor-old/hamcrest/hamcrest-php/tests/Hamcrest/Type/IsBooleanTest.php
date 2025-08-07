<?php

namespace Hamcrest\Type;

class IsBooleanTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsBoolean::booleanValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat(false, booleanValue());
        assertThat(true, booleanValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat([], not(booleanValue()));
        assertThat(5, not(booleanValue()));
        assertThat('foo', not(booleanValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a boolean', booleanValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', booleanValue(), null);
        $this->assertMismatchDescription('was a string "foo"', booleanValue(), 'foo');
    }
}
