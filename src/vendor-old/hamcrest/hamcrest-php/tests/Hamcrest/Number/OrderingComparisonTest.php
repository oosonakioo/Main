<?php

namespace Hamcrest\Number;

class OrderingComparisonTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Number\OrderingComparison::greaterThan(1);
    }

    public function test_compares_values_for_greater_than()
    {
        assertThat(2, greaterThan(1));
        assertThat(0, not(greaterThan(1)));
    }

    public function test_compares_values_for_less_than()
    {
        assertThat(2, lessThan(3));
        assertThat(0, lessThan(1));
    }

    public function test_compares_values_for_equality()
    {
        assertThat(3, comparesEqualTo(3));
        assertThat('aa', comparesEqualTo('aa'));
    }

    public function test_allows_for_inclusive_comparisons()
    {
        assertThat(1, lessThanOrEqualTo(1));
        assertThat(1, greaterThanOrEqualTo(1));
    }

    public function test_supports_different_types_of_comparable_values()
    {
        assertThat(1.1, greaterThan(1.0));
        assertThat('cc', greaterThan('bb'));
    }
}
