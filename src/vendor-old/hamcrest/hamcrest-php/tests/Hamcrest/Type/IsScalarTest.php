<?php

namespace Hamcrest\Type;

class IsScalarTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsScalar::scalarValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat(true, scalarValue());
        assertThat(5, scalarValue());
        assertThat(5.3, scalarValue());
        assertThat('5', scalarValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(null, not(scalarValue()));
        assertThat([], not(scalarValue()));
        assertThat([5], not(scalarValue()));
        assertThat(tmpfile(), not(scalarValue()));
        assertThat(new \stdClass, not(scalarValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a scalar', scalarValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', scalarValue(), null);
        $this->assertMismatchDescription('was an array ["foo"]', scalarValue(), ['foo']);
    }
}
