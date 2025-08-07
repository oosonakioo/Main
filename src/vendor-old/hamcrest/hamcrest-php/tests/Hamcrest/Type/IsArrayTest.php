<?php

namespace Hamcrest\Type;

class IsArrayTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsArray::arrayValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat(['5', 5], arrayValue());
        assertThat([], arrayValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(arrayValue()));
        assertThat(5, not(arrayValue()));
        assertThat('foo', not(arrayValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('an array', arrayValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', arrayValue(), null);
        $this->assertMismatchDescription('was a string "foo"', arrayValue(), 'foo');
    }
}
