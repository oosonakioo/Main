<?php

namespace Hamcrest\Core;

class IsAnythingTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\IsAnything::anything();
    }

    public function test_always_evaluates_to_true()
    {
        assertThat(null, anything());
        assertThat(new \stdClass, anything());
        assertThat('hi', anything());
    }

    public function test_has_useful_default_description()
    {
        $this->assertDescription('ANYTHING', anything());
    }

    public function test_can_override_description()
    {
        $description = 'description';
        $this->assertDescription($description, anything($description));
    }
}
