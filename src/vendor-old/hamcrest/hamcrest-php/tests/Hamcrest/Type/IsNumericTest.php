<?php

namespace Hamcrest\Type;

class IsNumericTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsNumeric::numericValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat(5, numericValue());
        assertThat(0, numericValue());
        assertThat(-5, numericValue());
        assertThat(5.3, numericValue());
        assertThat(0.53, numericValue());
        assertThat(-5.3, numericValue());
        assertThat('5', numericValue());
        assertThat('0', numericValue());
        assertThat('-5', numericValue());
        assertThat('5.3', numericValue());
        assertThat('5e+3', numericValue());
        assertThat('0.053e-2', numericValue());
        assertThat('-53.253e+25', numericValue());
        assertThat('+53.253e+25', numericValue());
        assertThat('0x4F2a04', numericValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(numericValue()));
        assertThat('foo', not(numericValue()));
        assertThat('foo5', not(numericValue()));
        assertThat('5foo', not(numericValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a number', numericValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', numericValue(), null);
        $this->assertMismatchDescription('was a string "foo"', numericValue(), 'foo');
    }
}
