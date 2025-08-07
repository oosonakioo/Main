<?php

namespace Hamcrest\Type;

class IsCallableTest extends \Hamcrest\AbstractMatcherTest
{
    public static function callableFunction() {}

    public function __invoke() {}

    protected function createMatcher()
    {
        return \Hamcrest\Type\IsCallable::callableValue();
    }

    public function test_evaluates_to_true_if_argument_is_function_name()
    {
        assertThat('preg_match', callableValue());
    }

    public function test_evaluates_to_true_if_argument_is_static_method_callback()
    {
        assertThat(
            ['Hamcrest\Type\IsCallableTest', 'callableFunction'],
            callableValue()
        );
    }

    public function test_evaluates_to_true_if_argument_is_instance_method_callback()
    {
        assertThat(
            [$this, 'testEvaluatesToTrueIfArgumentIsInstanceMethodCallback'],
            callableValue()
        );
    }

    public function test_evaluates_to_true_if_argument_is_closure()
    {
        if (! version_compare(PHP_VERSION, '5.3', '>=')) {
            $this->markTestSkipped('Closures require php 5.3');
        }
        eval('assertThat(function () {}, callableValue());');
    }

    public function test_evaluates_to_true_if_argument_implements_invoke()
    {
        if (! version_compare(PHP_VERSION, '5.3', '>=')) {
            $this->markTestSkipped('Magic method __invoke() requires php 5.3');
        }
        assertThat($this, callableValue());
    }

    public function test_evaluates_to_false_if_argument_is_invalid_function_name()
    {
        if (function_exists('not_a_Hamcrest_function')) {
            $this->markTestSkipped('Function "not_a_Hamcrest_function" must not exist');
        }

        assertThat('not_a_Hamcrest_function', not(callableValue()));
    }

    public function test_evaluates_to_false_if_argument_is_invalid_static_method_callback()
    {
        assertThat(
            ['Hamcrest\Type\IsCallableTest', 'noMethod'],
            not(callableValue())
        );
    }

    public function test_evaluates_to_false_if_argument_is_invalid_instance_method_callback()
    {
        assertThat([$this, 'noMethod'], not(callableValue()));
    }

    public function test_evaluates_to_false_if_argument_doesnt_implement_invoke()
    {
        assertThat(new \stdClass, not(callableValue()));
    }

    public function test_evaluates_to_false_if_argument_doesnt_match_type()
    {
        assertThat(false, not(callableValue()));
        assertThat(5.2, not(callableValue()));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a callable', callableValue());
    }

    public function test_decribes_actual_type_in_mismatch_message()
    {
        $this->assertMismatchDescription(
            'was a string "invalid-function"',
            callableValue(),
            'invalid-function'
        );
    }
}
