<?php

namespace Hamcrest\Core;

class IsSameTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\IsSame::sameInstance(new \stdClass);
    }

    public function test_evaluates_to_true_if_argument_is_reference_to_a_specified_object()
    {
        $o1 = new \stdClass;
        $o2 = new \stdClass;

        assertThat($o1, sameInstance($o1));
        assertThat($o2, not(sameInstance($o1)));
    }

    public function test_returns_readable_description_from_to_string()
    {
        $this->assertDescription('sameInstance("ARG")', sameInstance('ARG'));
    }

    public function test_returns_readable_description_from_to_string_when_initialised_with_null()
    {
        $this->assertDescription('sameInstance(null)', sameInstance(null));
    }
}
