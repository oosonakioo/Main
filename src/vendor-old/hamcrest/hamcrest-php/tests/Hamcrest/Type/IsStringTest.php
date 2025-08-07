<?php

namespace Hamcrest\Type;

class IsStringTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsString::stringValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat('', stringValue());
        assertThat('foo', stringValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(stringValue()));
        assertThat(5, not(stringValue()));
        assertThat([1, 2, 3], not(stringValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a string', stringValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', stringValue(), null);
        $this->assertMismatchDescription('was a double <5.2F>', stringValue(), 5.2);
    }
}
