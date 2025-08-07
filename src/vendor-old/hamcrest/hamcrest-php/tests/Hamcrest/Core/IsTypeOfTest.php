<?php

namespace Hamcrest\Core;

class IsTypeOfTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\IsTypeOf::typeOf('integer');
    }

    public function test_evaluates_to_true_if_argument_matches_type()
    {
        assertThat(['5', 5], typeOf('array'));
        assertThat(false, typeOf('boolean'));
        assertThat(5, typeOf('integer'));
        assertThat(5.2, typeOf('double'));
        assertThat(null, typeOf('null'));
        assertThat(tmpfile(), typeOf('resource'));
        assertThat('a string', typeOf('string'));
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(typeOf('array')));
        assertThat(['5', 5], not(typeOf('boolean')));
        assertThat(5.2, not(typeOf('integer')));
        assertThat(5, not(typeOf('double')));
        assertThat(false, not(typeOf('null')));
        assertThat('a string', not(typeOf('resource')));
        assertThat(tmpfile(), not(typeOf('string')));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a double', typeOf('double'));
        $this->assertDescription('an integer', typeOf('integer'));
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription('was null', typeOf('boolean'), null);
        $this->assertMismatchDescription('was an integer <5>', typeOf('float'), 5);
    }
}
