<?php

namespace Hamcrest\Core;

class IsNullTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\IsNull::nullValue();
    }

    public function test_evaluates_to_true_if_argument_is_null()
    {
        assertThat(null, nullValue());
        assertThat(self::ANY_NON_NULL_ARGUMENT, not(nullValue()));

        assertThat(self::ANY_NON_NULL_ARGUMENT, notNullValue());
        assertThat(null, not(notNullValue()));
    }
}
