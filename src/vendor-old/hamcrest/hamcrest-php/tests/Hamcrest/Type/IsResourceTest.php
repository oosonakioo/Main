<?php

namespace Hamcrest\Type;

class IsResourceTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsResource::resourceValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat(tmpfile(), resourceValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(resourceValue()));
        assertThat(5, not(resourceValue()));
        assertThat('foo', not(resourceValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a resource', resourceValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', resourceValue(), null);
        $this->assertMismatchDescription('was a string "foo"', resourceValue(), 'foo');
    }
}
