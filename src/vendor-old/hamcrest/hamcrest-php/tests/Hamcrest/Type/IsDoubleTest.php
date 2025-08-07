<?php

namespace Hamcrest\Type;

class IsDoubleTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsDouble::doubleValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat((float) 5.2, floatValue());
        assertThat((float) 5.3, doubleValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(doubleValue()));
        assertThat(5, not(doubleValue()));
        assertThat('foo', not(doubleValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a double', doubleValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', doubleValue(), null);
        $this->assertMismatchDescription('was a string "foo"', doubleValue(), 'foo');
    }
}
