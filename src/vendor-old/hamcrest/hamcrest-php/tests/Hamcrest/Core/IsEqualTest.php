<?php

namespace Hamcrest\Core;

class DummyToStringClass
{
    private $_arg;

    public function __construct($arg)
    {
        $this->_arg = $arg;
    }

    public function __toString()
    {
        return $this->_arg;
    }
}

class IsEqualTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\IsEqual::equalTo('irrelevant');
    }

    public function test_compares_objects_using_equality_operator()
    {
        assertThat('hi', equalTo('hi'));
        assertThat('bye', not(equalTo('hi')));

        assertThat(1, equalTo(1));
        assertThat(1, not(equalTo(2)));

        assertThat('2', equalTo(2));
    }

    public function test_can_compare_null_values()
    {
        assertThat(null, equalTo(null));

        assertThat(null, not(equalTo('hi')));
        assertThat('hi', not(equalTo(null)));
    }

    public function test_compares_the_elements_of_an_array()
    {
        $s1 = ['a', 'b'];
        $s2 = ['a', 'b'];
        $s3 = ['c', 'd'];
        $s4 = ['a', 'b', 'c', 'd'];

        assertThat($s1, equalTo($s1));
        assertThat($s2, equalTo($s1));
        assertThat($s3, not(equalTo($s1)));
        assertThat($s4, not(equalTo($s1)));
    }

    public function test_compares_the_elements_of_an_array_of_primitive_types()
    {
        $i1 = [1, 2];
        $i2 = [1, 2];
        $i3 = [3, 4];
        $i4 = [1, 2, 3, 4];

        assertThat($i1, equalTo($i1));
        assertThat($i2, equalTo($i1));
        assertThat($i3, not(equalTo($i1)));
        assertThat($i4, not(equalTo($i1)));
    }

    public function test_recursively_tests_elements_of_arrays()
    {
        $i1 = [[1, 2], [3, 4]];
        $i2 = [[1, 2], [3, 4]];
        $i3 = [[5, 6], [7, 8]];
        $i4 = [[1, 2, 3, 4], [3, 4]];

        assertThat($i1, equalTo($i1));
        assertThat($i2, equalTo($i1));
        assertThat($i3, not(equalTo($i1)));
        assertThat($i4, not(equalTo($i1)));
    }

    public function test_includes_the_result_of_calling_to_string_on_its_argument_in_the_description()
    {
        $argumentDescription = 'ARGUMENT DESCRIPTION';
        $argument = new \Hamcrest\Core\DummyToStringClass($argumentDescription);
        $this->assertDescription('<'.$argumentDescription.'>', equalTo($argument));
    }

    public function test_returns_an_obvious_description_if_created_with_a_nested_matcher_by_mistake()
    {
        $innerMatcher = equalTo('NestedMatcher');
        $this->assertDescription('<'.(string) $innerMatcher.'>', equalTo($innerMatcher));
    }

    public function test_returns_good_description_if_created_with_null_reference()
    {
        $this->assertDescription('null', equalTo(null));
    }
}
