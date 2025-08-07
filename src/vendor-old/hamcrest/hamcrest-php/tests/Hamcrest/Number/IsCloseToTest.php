<?php

namespace Hamcrest\Number;

class IsCloseToTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        $irrelevant = 0.1;

        return \Hamcrest\Number\IsCloseTo::closeTo($irrelevant, $irrelevant);
    }

    public function test_evaluates_to_true_if_argument_is_equal_to_a_double_value_within_some_error()
    {
        $p = closeTo(1.0, 0.5);

        $this->assertTrue($p->matches(1.0));
        $this->assertTrue($p->matches(0.5));
        $this->assertTrue($p->matches(1.5));

        $this->assertDoesNotMatch($p, 2.0, 'too large');
        $this->assertMismatchDescription('<2F> differed by <0.5F>', $p, 2.0);
        $this->assertDoesNotMatch($p, 0.0, 'number too small');
        $this->assertMismatchDescription('<0F> differed by <0.5F>', $p, 0.0);
    }
}
