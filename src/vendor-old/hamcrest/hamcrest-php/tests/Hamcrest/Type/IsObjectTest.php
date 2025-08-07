<?php

namespace Hamcrest\Type;

class IsObjectTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Type\IsObject::objectValue();
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat(new \stdClass, objectValue());
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(objectValue()));
        assertThat(5, not(objectValue()));
        assertThat('foo', not(objectValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('an object', objectValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', objectValue(), null);
        $this->assertMismatchDescription('was a string "foo"', objectValue(), 'foo');
    }
}
