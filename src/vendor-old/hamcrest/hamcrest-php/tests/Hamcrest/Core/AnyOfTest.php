<?php

namespace Hamcrest\Core;

class AnyOfTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\AnyOf::anyOf('irrelevant');
    }

    public function test_any_of_evaluates_to_the_logical_disjunction_of_two_other_matchers()
    {
        assertThat('good', anyOf('bad', 'good'));
        assertThat('good', anyOf('good', 'good'));
        assertThat('good', anyOf('good', 'bad'));

        assertThat('good', not(anyOf('bad', startsWith('b'))));
    }

    public function test_any_of_evaluates_to_the_logical_disjunction_of_many_other_matchers()
    {
        assertThat('good', anyOf('bad', 'good', 'bad', 'bad', 'bad'));
        assertThat('good', not(anyOf('bad', 'bad', 'bad', 'bad', 'bad')));
    }

    public function test_any_of_supports_mixed_types()
    {
        $combined = anyOf(
            equalTo(new \Hamcrest\Core\SampleBaseClass('good')),
            equalTo(new \Hamcrest\Core\SampleBaseClass('ugly')),
            equalTo(new \Hamcrest\Core\SampleSubClass('good'))
        );

        assertThat(new \Hamcrest\Core\SampleSubClass('good'), $combined);
    }

    public function test_any_of_has_a_readable_description()
    {
        $this->assertDescription(
            '("good" or "bad" or "ugly")',
            anyOf('good', 'bad', 'ugly')
        );
    }

    public function test_none_of_evaluates_to_the_logical_disjunction_of_two_other_matchers()
    {
        assertThat('good', not(noneOf('bad', 'good')));
        assertThat('good', not(noneOf('good', 'good')));
        assertThat('good', not(noneOf('good', 'bad')));

        assertThat('good', noneOf('bad', startsWith('b')));
    }

    public function test_none_of_evaluates_to_the_logical_disjunction_of_many_other_matchers()
    {
        assertThat('good', not(noneOf('bad', 'good', 'bad', 'bad', 'bad')));
        assertThat('good', noneOf('bad', 'bad', 'bad', 'bad', 'bad'));
    }

    public function test_none_of_supports_mixed_types()
    {
        $combined = noneOf(
            equalTo(new \Hamcrest\Core\SampleBaseClass('good')),
            equalTo(new \Hamcrest\Core\SampleBaseClass('ugly')),
            equalTo(new \Hamcrest\Core\SampleSubClass('good'))
        );

        assertThat(new \Hamcrest\Core\SampleSubClass('bad'), $combined);
    }

    public function test_none_of_has_a_readable_description()
    {
        $this->assertDescription(
            'not ("good" or "bad" or "ugly")',
            noneOf('good', 'bad', 'ugly')
        );
    }
}
