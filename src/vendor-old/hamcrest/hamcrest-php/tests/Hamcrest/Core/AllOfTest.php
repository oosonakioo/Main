<?php

namespace Hamcrest\Core;

class AllOfTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\AllOf::allOf('irrelevant');
    }

    public function test_evaluates_to_the_logical_conjunction_of_two_other_matchers()
    {
        assertThat('good', allOf('good', 'good'));

        assertThat('good', not(allOf('bad', 'good')));
        assertThat('good', not(allOf('good', 'bad')));
        assertThat('good', not(allOf('bad', 'bad')));
    }

    public function test_evaluates_to_the_logical_conjunction_of_many_other_matchers()
    {
        assertThat('good', allOf('good', 'good', 'good', 'good', 'good'));
        assertThat('good', not(allOf('good', endsWith('d'), 'bad', 'good', 'good')));
    }

    public function test_supports_mixed_types()
    {
        $all = allOf(
            equalTo(new \Hamcrest\Core\SampleBaseClass('good')),
            equalTo(new \Hamcrest\Core\SampleBaseClass('good')),
            equalTo(new \Hamcrest\Core\SampleSubClass('ugly'))
        );

        $negated = not($all);

        assertThat(new \Hamcrest\Core\SampleSubClass('good'), $negated);
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription(
            '("good" and "bad" and "ugly")',
            allOf('good', 'bad', 'ugly')
        );
    }

    public function test_mismatch_description_describes_first_failing_match()
    {
        $this->assertMismatchDescription(
            '"good" was "bad"',
            allOf('bad', 'good'),
            'bad'
        );
    }
}
