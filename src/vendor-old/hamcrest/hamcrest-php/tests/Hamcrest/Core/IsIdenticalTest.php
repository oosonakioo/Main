<?php

namespace Hamcrest\Core;

class IsIdenticalTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\IsIdentical::identicalTo('irrelevant');
    }

    public function test_evaluates_to_true_if_argument_is_reference_to_a_specified_object()
    {
        $o1 = new \stdClass;
        $o2 = new \stdClass;

        assertThat($o1, identicalTo($o1));
        assertThat($o2, not(identicalTo($o1)));
    }

    public function test_returns_readable_description_from_to_string()
    {
        $this->assertDescription('"ARG"', identicalTo('ARG'));
    }

    public function test_returns_readable_description_from_to_string_when_initialised_with_null()
    {
        $this->assertDescription('null', identicalTo(null));
    }
}
