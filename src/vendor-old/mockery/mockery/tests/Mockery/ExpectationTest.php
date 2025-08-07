<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 *
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery\Adapter\Phpunit\MockeryTestCase;

class ExpectationTest extends MockeryTestCase
{
    protected function setup()
    {
        $this->container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
        $this->mock = $this->container->mock('foo');
    }

    protected function teardown()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        $this->container->mockery_close();
    }

    public function test_returns_null_when_no_args()
    {
        $this->mock->shouldReceive('foo');
        $this->assertNull($this->mock->foo());
    }

    public function test_returns_null_when_single_arg()
    {
        $this->mock->shouldReceive('foo');
        $this->assertNull($this->mock->foo(1));
    }

    public function test_returns_null_when_many_args()
    {
        $this->mock->shouldReceive('foo');
        $this->assertNull($this->mock->foo('foo', [], new stdClass));
    }

    public function test_returns_null_if_null_is_return_value()
    {
        $this->mock->shouldReceive('foo')->andReturn(null);
        $this->assertNull($this->mock->foo());
    }

    public function test_returns_null_for_mocked_existing_class_if_andreturnnull_called()
    {
        $mock = $this->container->mock('MockeryTest_Foo');
        $mock->shouldReceive('foo')->andReturn(null);
        $this->assertNull($mock->foo());
    }

    public function test_returns_null_for_mocked_existing_class_if_null_is_return_value()
    {
        $mock = $this->container->mock('MockeryTest_Foo');
        $mock->shouldReceive('foo')->andReturnNull();
        $this->assertNull($mock->foo());
    }

    public function test_returns_same_value_for_all_if_no_args_expectation_and_none_given()
    {
        $this->mock->shouldReceive('foo')->andReturn(1);
        $this->assertEquals(1, $this->mock->foo());
    }

    public function test_sets_public_property_when_requested()
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')->andSet('bar', 'baz');
        $this->assertNull($this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('baz', $this->mock->bar);
    }

    public function test_sets_public_property_when_requested_using_alias()
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')->set('bar', 'baz');
        $this->assertNull($this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('baz', $this->mock->bar);
    }

    public function test_sets_public_properties_when_requested()
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')->andSet('bar', 'baz', 'bazz', 'bazzz');
        $this->assertNull($this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('baz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazzz', $this->mock->bar);
    }

    public function test_sets_public_properties_when_requested_using_alias()
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')->set('bar', 'baz', 'bazz', 'bazzz');
        $this->assertAttributeEmpty('bar', $this->mock);
        $this->mock->foo();
        $this->assertEquals('baz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazzz', $this->mock->bar);
    }

    public function test_sets_public_properties_when_requested_more_times_than_set_values()
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')->andSet('bar', 'baz', 'bazz');
        $this->assertNull($this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('baz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazz', $this->mock->bar);
    }

    public function test_sets_public_properties_when_requested_more_times_than_set_values_using_alias()
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')->andSet('bar', 'baz', 'bazz');
        $this->assertNull($this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('baz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazz', $this->mock->bar);
    }

    public function test_sets_public_properties_when_requested_more_times_than_set_values_with_direct_set()
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')->andSet('bar', 'baz', 'bazz');
        $this->assertNull($this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('baz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazz', $this->mock->bar);
        $this->mock->bar = null;
        $this->mock->foo();
        $this->assertNull($this->mock->bar);
    }

    public function test_sets_public_properties_when_requested_more_times_than_set_values_with_direct_set_using_alias()
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')->set('bar', 'baz', 'bazz');
        $this->assertNull($this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('baz', $this->mock->bar);
        $this->mock->foo();
        $this->assertEquals('bazz', $this->mock->bar);
        $this->mock->bar = null;
        $this->mock->foo();
        $this->assertNull($this->mock->bar);
    }

    public function test_returns_same_value_for_all_if_no_args_expectation_and_some_given()
    {
        $this->mock->shouldReceive('foo')->andReturn(1);
        $this->assertEquals(1, $this->mock->foo('foo'));
    }

    public function test_returns_value_from_sequence_sequentially()
    {
        $this->mock->shouldReceive('foo')->andReturn(1, 2, 3);
        $this->mock->foo('foo');
        $this->assertEquals(2, $this->mock->foo('foo'));
    }

    public function test_returns_value_from_sequence_sequentially_and_repeatedly_returns_final_value_on_extra_calls()
    {
        $this->mock->shouldReceive('foo')->andReturn(1, 2, 3);
        $this->mock->foo('foo');
        $this->mock->foo('foo');
        $this->assertEquals(3, $this->mock->foo('foo'));
        $this->assertEquals(3, $this->mock->foo('foo'));
    }

    public function test_returns_value_from_sequence_sequentially_and_repeatedly_returns_final_value_on_extra_calls_with_many_and_return_calls()
    {
        $this->mock->shouldReceive('foo')->andReturn(1)->andReturn(2, 3);
        $this->mock->foo('foo');
        $this->mock->foo('foo');
        $this->assertEquals(3, $this->mock->foo('foo'));
        $this->assertEquals(3, $this->mock->foo('foo'));
    }

    public function test_returns_value_of_closure()
    {
        $this->mock->shouldReceive('foo')->with(5)->andReturnUsing(function ($v) {
            return $v + 1;
        });
        $this->assertEquals(6, $this->mock->foo(5));
    }

    public function test_returns_undefined()
    {
        $this->mock->shouldReceive('foo')->andReturnUndefined();
        $this->assertTrue($this->mock->foo() instanceof \Mockery\Undefined);
    }

    public function test_returns_values_set_as_array()
    {
        $this->mock->shouldReceive('foo')->andReturnValues([1, 2, 3]);
        $this->assertEquals(1, $this->mock->foo());
        $this->assertEquals(2, $this->mock->foo());
        $this->assertEquals(3, $this->mock->foo());
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function test_throws_exception()
    {
        $this->mock->shouldReceive('foo')->andThrow(new OutOfBoundsException);
        $this->mock->foo();
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function test_throws_exception_based_on_args()
    {
        $this->mock->shouldReceive('foo')->andThrow('OutOfBoundsException');
        $this->mock->foo();
    }

    public function test_throws_exception_based_on_args_with_message()
    {
        $this->mock->shouldReceive('foo')->andThrow('OutOfBoundsException', 'foo');
        try {
            $this->mock->foo();
        } catch (OutOfBoundsException $e) {
            $this->assertEquals('foo', $e->getMessage());
        }
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function test_throws_exception_sequentially()
    {
        $this->mock->shouldReceive('foo')->andThrow(new Exception)->andThrow(new OutOfBoundsException);
        try {
            $this->mock->foo();
        } catch (Exception $e) {
        }
        $this->mock->foo();
    }

    public function test_and_throw_exceptions()
    {
        $this->mock->shouldReceive('foo')->andThrowExceptions([
            new OutOfBoundsException,
            new InvalidArgumentException,
        ]);

        try {
            $this->mock->foo();
            throw new Exception('Expected OutOfBoundsException, non thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf('OutOfBoundsException', $e, "Wrong or no exception thrown: {$e->getMessage()}");
        }

        try {
            $this->mock->foo();
            throw new Exception('Expected InvalidArgumentException, non thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e, "Wrong or no exception thrown: {$e->getMessage()}");
        }
    }

    /**
     * @expectedException Mockery\Exception
     *
     * @expectedExceptionMessage You must pass an array of exception objects to andThrowExceptions
     */
    public function test_and_throw_exceptions_catch_non_exception_argument()
    {
        $this->mock
            ->shouldReceive('foo')
            ->andThrowExceptions(['NotAnException']);
    }

    public function test_multiple_expectations_with_returns()
    {
        $this->mock->shouldReceive('foo')->with(1)->andReturn(10);
        $this->mock->shouldReceive('bar')->with(2)->andReturn(20);
        $this->assertEquals(10, $this->mock->foo(1));
        $this->assertEquals(20, $this->mock->bar(2));
    }

    public function test_expects_no_arguments()
    {
        $this->mock->shouldReceive('foo')->withNoArgs();
        $this->mock->foo();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_expects_no_arguments_throws_exception_if_any_passed()
    {
        $this->mock->shouldReceive('foo')->withNoArgs();
        $this->mock->foo(1);
    }

    public function test_expects_arguments_array()
    {
        $this->mock->shouldReceive('foo')->withArgs([1, 2]);
        $this->mock->foo(1, 2);
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_expects_arguments_array_throws_exception_if_passed_empty_array()
    {
        $this->mock->shouldReceive('foo')->withArgs([]);
        $this->mock->foo(1, 2);
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_expects_arguments_array_throws_exception_if_no_arguments_passed()
    {
        $this->mock->shouldReceive('foo')->with();
        $this->mock->foo(1);
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_expects_arguments_array_throws_exception_if_passed_wrong_arguments()
    {
        $this->mock->shouldReceive('foo')->withArgs([1, 2]);
        $this->mock->foo(3, 4);
    }

    /**
     * @expectedException \Mockery\Exception
     *
     * @expectedExceptionMessageRegExp /foo\(NULL\)/
     */
    public function test_expects_string_argument_exception_message_differentiates_between_null_and_empty_string()
    {
        $this->mock->shouldReceive('foo')->withArgs(['a string']);
        $this->mock->foo(null);
    }

    public function test_expects_any_arguments()
    {
        $this->mock->shouldReceive('foo')->withAnyArgs();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 'k', new stdClass);
    }

    public function test_expects_argument_matching_regular_expression()
    {
        $this->mock->shouldReceive('foo')->with('/bar/i');
        $this->mock->foo('xxBARxx');
    }

    public function test_expects_argument_matching_object_type()
    {
        $this->mock->shouldReceive('foo')->with('\stdClass');
        $this->mock->foo(new stdClass);
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_throws_exception_on_no_argument_match()
    {
        $this->mock->shouldReceive('foo')->with(1);
        $this->mock->foo(2);
    }

    public function test_never_called()
    {
        $this->mock->shouldReceive('foo')->never();
        $this->container->mockery_verify();
    }

    public function test_should_not_receive()
    {
        $this->mock->shouldNotReceive('foo');
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception\InvalidCountException
     */
    public function test_should_not_receive_throws_exception_if_method_called()
    {
        $this->mock->shouldNotReceive('foo');
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception\InvalidCountException
     */
    public function test_should_not_receive_with_argument_throws_exception_if_method_called()
    {
        $this->mock->shouldNotReceive('foo')->with(2);
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_never_called_throws_exception_on_call()
    {
        $this->mock->shouldReceive('foo')->never();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_called_once()
    {
        $this->mock->shouldReceive('foo')->once();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_called_once_throws_exception_if_not_called()
    {
        $this->mock->shouldReceive('foo')->once();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_called_once_throws_exception_if_called_twice()
    {
        $this->mock->shouldReceive('foo')->once();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_called_twice()
    {
        $this->mock->shouldReceive('foo')->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_called_twice_throws_exception_if_not_called()
    {
        $this->mock->shouldReceive('foo')->twice();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_called_once_throws_exception_if_called_three_times()
    {
        $this->mock->shouldReceive('foo')->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_called_zero_or_more_times_at_zero_calls()
    {
        $this->mock->shouldReceive('foo')->zeroOrMoreTimes();
        $this->container->mockery_verify();
    }

    public function test_called_zero_or_more_times_at_three_calls()
    {
        $this->mock->shouldReceive('foo')->zeroOrMoreTimes();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_times_count_calls()
    {
        $this->mock->shouldReceive('foo')->times(4);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_times_count_call_throws_exception_on_too_few_calls()
    {
        $this->mock->shouldReceive('foo')->times(2);
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_times_count_call_throws_exception_on_too_many_calls()
    {
        $this->mock->shouldReceive('foo')->times(2);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_called_at_least_once_at_exactly_one_call()
    {
        $this->mock->shouldReceive('foo')->atLeast()->once();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_called_at_least_once_at_exactly_three_calls()
    {
        $this->mock->shouldReceive('foo')->atLeast()->times(3);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_called_at_least_throws_exception_on_too_few_calls()
    {
        $this->mock->shouldReceive('foo')->atLeast()->twice();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_called_at_most_once_at_exactly_one_call()
    {
        $this->mock->shouldReceive('foo')->atMost()->once();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_called_at_most_at_exactly_three_calls()
    {
        $this->mock->shouldReceive('foo')->atMost()->times(3);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_called_at_least_throws_exception_on_too_many_calls()
    {
        $this->mock->shouldReceive('foo')->atMost()->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_exact_counters_override_any_prior_set_non_exact_counters()
    {
        $this->mock->shouldReceive('foo')->atLeast()->once()->once();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_combo_of_least_and_most_calls_with_one_call()
    {
        $this->mock->shouldReceive('foo')->atleast()->once()->atMost()->twice();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_combo_of_least_and_most_calls_with_two_calls()
    {
        $this->mock->shouldReceive('foo')->atleast()->once()->atMost()->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_combo_of_least_and_most_calls_throws_exception_at_too_few_calls()
    {
        $this->mock->shouldReceive('foo')->atleast()->once()->atMost()->twice();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_combo_of_least_and_most_calls_throws_exception_at_too_many_calls()
    {
        $this->mock->shouldReceive('foo')->atleast()->once()->atMost()->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_call_counting_only_applies_to_matched_expectations()
    {
        $this->mock->shouldReceive('foo')->with(1)->once();
        $this->mock->shouldReceive('foo')->with(2)->twice();
        $this->mock->shouldReceive('foo')->with(3);
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(2);
        $this->mock->foo(3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_call_counting_throws_exception_on_any_mismatch()
    {
        $this->mock->shouldReceive('foo')->with(1)->once();
        $this->mock->shouldReceive('foo')->with(2)->twice();
        $this->mock->shouldReceive('foo')->with(3);
        $this->mock->shouldReceive('bar');
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(3);
        $this->mock->bar();
        $this->container->mockery_verify();
    }

    public function test_ordered_calls_without_error()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_ordered_calls_with_out_of_order_error()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->bar();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_different_arguments_and_orderings_pass_without_exception()
    {
        $this->mock->shouldReceive('foo')->with(1)->ordered();
        $this->mock->shouldReceive('foo')->with(2)->ordered();
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_different_arguments_and_orderings_throw_exception_when_in_wrong_order()
    {
        $this->mock->shouldReceive('foo')->with(1)->ordered();
        $this->mock->shouldReceive('foo')->with(2)->ordered();
        $this->mock->foo(2);
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_unordered_calls_ignored_for_ordering()
    {
        $this->mock->shouldReceive('foo')->with(1)->ordered();
        $this->mock->shouldReceive('foo')->with(2);
        $this->mock->shouldReceive('foo')->with(3)->ordered();
        $this->mock->foo(2);
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(3);
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    public function test_ordering_of_default_grouping()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_ordering_of_default_grouping_throws_exception_on_wrong_order()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->bar();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_ordering_using_numbered_groups()
    {
        $this->mock->shouldReceive('start')->ordered(1);
        $this->mock->shouldReceive('foo')->ordered(2);
        $this->mock->shouldReceive('bar')->ordered(2);
        $this->mock->shouldReceive('final')->ordered();
        $this->mock->start();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->final();
        $this->container->mockery_verify();
    }

    public function test_ordering_using_named_groups()
    {
        $this->mock->shouldReceive('start')->ordered('start');
        $this->mock->shouldReceive('foo')->ordered('foobar');
        $this->mock->shouldReceive('bar')->ordered('foobar');
        $this->mock->shouldReceive('final')->ordered();
        $this->mock->start();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->final();
        $this->container->mockery_verify();
    }

    /**
     * @group 2A
     */
    public function test_grouped_ungrouped_ordering_do_not_overlap()
    {
        $s = $this->mock->shouldReceive('start')->ordered();
        $m = $this->mock->shouldReceive('mid')->ordered('foobar');
        $e = $this->mock->shouldReceive('end')->ordered();
        $this->assertTrue($s->getOrderNumber() < $m->getOrderNumber());
        $this->assertTrue($m->getOrderNumber() < $e->getOrderNumber());
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_grouped_ordering_throws_exception_when_calls_disordered()
    {
        $this->mock->shouldReceive('foo')->ordered('first');
        $this->mock->shouldReceive('bar')->ordered('second');
        $this->mock->bar();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_expectation_matching_with_no_args_orderings()
    {
        $this->mock->shouldReceive('foo')->withNoArgs()->once()->ordered();
        $this->mock->shouldReceive('bar')->withNoArgs()->once()->ordered();
        $this->mock->shouldReceive('foo')->withNoArgs()->once()->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_expectation_matching_with_any_args_orderings()
    {
        $this->mock->shouldReceive('foo')->withAnyArgs()->once()->ordered();
        $this->mock->shouldReceive('bar')->withAnyArgs()->once()->ordered();
        $this->mock->shouldReceive('foo')->withAnyArgs()->once()->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_ensures_ordering_is_not_cross_mock_by_default()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $mock2 = $this->container->mock('bar');
        $mock2->shouldReceive('bar')->ordered();
        $mock2->bar();
        $this->mock->foo();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_ensures_ordering_is_cross_mock_when_globally_flag_set()
    {
        $this->mock->shouldReceive('foo')->globally()->ordered();
        $mock2 = $this->container->mock('bar');
        $mock2->shouldReceive('bar')->globally()->ordered();
        $mock2->bar();
        $this->mock->foo();
    }

    public function test_expectation_cast_to_string_formatting()
    {
        $exp = $this->mock->shouldReceive('foo')->with(1, 'bar', new stdClass, ['Spam' => 'Ham', 'Bar' => 'Baz']);
        $this->assertEquals('[foo(1, "bar", object(stdClass), array(\'Spam\'=>\'Ham\',\'Bar\'=>\'Baz\',))]', (string) $exp);
    }

    public function test_long_expectation_cast_to_string_formatting()
    {
        $exp = $this->mock->shouldReceive('foo')->with(['Spam' => 'Ham', 'Bar' => 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'Bar', 'Baz', 'End']);
        $this->assertEquals("[foo(array('Spam'=>'Ham','Bar'=>'Baz',0=>'Bar',1=>'Baz',2=>'Bar',3=>'Baz',4=>'Bar',5=>'Baz',6=>'Bar',7=>'Baz',8=>'Bar',9=>'Baz',10=>'Bar',11=>'Baz',12=>'Bar',13=>'Baz',14=>'Bar',15=>'Baz',16=>'Bar',17=>'Baz',18=>'Bar',19=>'Baz',20=>'Bar',21=>'Baz',22=>'Bar',23=>'Baz',24=>'Bar',25=>'Baz',26=>'Bar',27=>'Baz',28=>'Bar',29=>'Baz',30=>'Bar',31=>'Baz',32=>'Bar',33=>'Baz',34=>'Bar',35=>'Baz',36=>'Bar',37=>'Baz',38=>'Bar',39=>'Baz',40=>'Bar',41=>'Baz',42=>'Bar',43=>'Baz',44=>'Bar',45=>'Baz',46=>'Baz',47=>'Bar',48=>'Baz',49=>'Bar',50=>'Baz',51=>'Bar',52=>'Baz',53=>'Bar',54=>'Baz',55=>'Bar',56=>'Baz',57=>'Baz',58=>'Bar',59=>'Baz',60=>'Bar',61=>'Baz',62=>'Bar',63=>'Baz',64=>'Bar',65=>'Baz',66=>'Bar',67=>'Baz',68=>'Baz',69=>'Bar',70=>'Baz',71=>'Bar',72=>'Baz',73=>'Bar',74=>'Baz',75=>'Bar',76=>'Baz',77=>'Bar',78=>'Baz',79=>'Baz',80=>'Bar',81=>'Baz',82=>'Bar',83=>'Baz',84=>'Bar',85=>'Baz',86=>'Bar',87=>'Baz',88=>'Bar',89=>'Baz',90=>'Baz',91=>'Bar',92=>'Baz',93=>'Bar',94=>'Baz',95=>'Bar',96=>'Baz',97=>'Ba...))]", (string) $exp);
    }

    public function test_multiple_expectation_cast_to_string_formatting()
    {
        $exp = $this->mock->shouldReceive('foo', 'bar')->with(1);
        $this->assertEquals('[foo(1), bar(1)]', (string) $exp);
    }

    public function test_grouped_ordering_with_limits_allows_multiple_return_values()
    {
        $this->mock->shouldReceive('foo')->with(2)->once()->andReturn('first');
        $this->mock->shouldReceive('foo')->with(2)->twice()->andReturn('second/third');
        $this->mock->shouldReceive('foo')->with(2)->andReturn('infinity');
        $this->assertEquals('first', $this->mock->foo(2));
        $this->assertEquals('second/third', $this->mock->foo(2));
        $this->assertEquals('second/third', $this->mock->foo(2));
        $this->assertEquals('infinity', $this->mock->foo(2));
        $this->assertEquals('infinity', $this->mock->foo(2));
        $this->assertEquals('infinity', $this->mock->foo(2));
        $this->container->mockery_verify();
    }

    public function test_expectations_can_be_marked_as_defaults()
    {
        $this->mock->shouldReceive('foo')->andReturn('bar')->byDefault();
        $this->assertEquals('bar', $this->mock->foo());
        $this->container->mockery_verify();
    }

    public function test_default_expectations_validated_in_correct_order()
    {
        $this->mock->shouldReceive('foo')->with(1)->once()->andReturn('first')->byDefault();
        $this->mock->shouldReceive('foo')->with(2)->once()->andReturn('second')->byDefault();
        $this->assertEquals('first', $this->mock->foo(1));
        $this->assertEquals('second', $this->mock->foo(2));
        $this->container->mockery_verify();
    }

    public function test_default_expectations_are_replaced_by_later_concrete_expectations()
    {
        $this->mock->shouldReceive('foo')->andReturn('bar')->once()->byDefault();
        $this->mock->shouldReceive('foo')->andReturn('bar')->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_default_expectations_can_be_changed_by_later_expectations()
    {
        $this->mock->shouldReceive('foo')->with(1)->andReturn('bar')->once()->byDefault();
        $this->mock->shouldReceive('foo')->with(2)->andReturn('baz')->once();
        try {
            $this->mock->foo(1);
            $this->fail('Expected exception not thrown');
        } catch (\Mockery\Exception $e) {
        }
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_default_expectations_can_be_ordered()
    {
        $this->mock->shouldReceive('foo')->ordered()->byDefault();
        $this->mock->shouldReceive('bar')->ordered()->byDefault();
        $this->mock->bar();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_default_expectations_can_be_ordered_and_replaced()
    {
        $this->mock->shouldReceive('foo')->ordered()->byDefault();
        $this->mock->shouldReceive('bar')->ordered()->byDefault();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->bar();
        $this->mock->foo();
        $this->container->mockery_verify();
    }

    public function test_by_default_operates_from_mock_construction()
    {
        $container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
        $mock = $container->mock('f', ['foo' => 'rfoo', 'bar' => 'rbar', 'baz' => 'rbaz'])->byDefault();
        $mock->shouldReceive('foo')->andReturn('foobar');
        $this->assertEquals('foobar', $mock->foo());
        $this->assertEquals('rbar', $mock->bar());
        $this->assertEquals('rbaz', $mock->baz());
        $mock->mockery_verify();
    }

    public function test_by_default_on_a_mock_does_squat_without_expectations()
    {
        $container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
        $mock = $container->mock('f')->byDefault();
    }

    public function test_default_expectations_can_be_overridden()
    {
        $this->mock->shouldReceive('foo')->with('test')->andReturn('bar')->byDefault();
        $this->mock->shouldReceive('foo')->with('test')->andReturn('newbar')->byDefault();
        $this->mock->foo('test');
        $this->assertEquals('newbar', $this->mock->foo('test'));
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_by_default_prevented_from_setting_default_when_defaulting_expectation_was_replaced()
    {
        $exp = $this->mock->shouldReceive('foo')->andReturn(1);
        $this->mock->shouldReceive('foo')->andReturn(2);
        $exp->byDefault();
    }

    /**
     * Argument Constraint Tests
     */
    public function test_any_constraint_matches_any_arg()
    {
        $this->mock->shouldReceive('foo')->with(1, Mockery::any())->twice();
        $this->mock->foo(1, 2);
        $this->mock->foo(1, 'str');
        $this->container->mockery_verify();
    }

    public function test_any_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::any())->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    public function test_array_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('array'))->once();
        $this->mock->foo([]);
        $this->container->mockery_verify();
    }

    public function test_array_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('array'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_array_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('array'))->once();
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_bool_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('bool'))->once();
        $this->mock->foo(true);
        $this->container->mockery_verify();
    }

    public function test_bool_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('bool'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_bool_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('bool'))->once();
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_callable_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('callable'))->once();
        $this->mock->foo(function () {
            return 'f';
        });
        $this->container->mockery_verify();
    }

    public function test_callable_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('callable'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_callable_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('callable'))->once();
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_double_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('double'))->once();
        $this->mock->foo(2.25);
        $this->container->mockery_verify();
    }

    public function test_double_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('double'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_double_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('double'))->once();
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_float_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('float'))->once();
        $this->mock->foo(2.25);
        $this->container->mockery_verify();
    }

    public function test_float_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('float'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_float_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('float'))->once();
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_int_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('int'))->once();
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    public function test_int_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('int'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_int_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('int'))->once();
        $this->mock->foo('f');
        $this->container->mockery_verify();
    }

    public function test_long_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('long'))->once();
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    public function test_long_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('long'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_long_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('long'))->once();
        $this->mock->foo('f');
        $this->container->mockery_verify();
    }

    public function test_null_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('null'))->once();
        $this->mock->foo(null);
        $this->container->mockery_verify();
    }

    public function test_null_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('null'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_null_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('null'))->once();
        $this->mock->foo('f');
        $this->container->mockery_verify();
    }

    public function test_numeric_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('numeric'))->once();
        $this->mock->foo('2');
        $this->container->mockery_verify();
    }

    public function test_numeric_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('numeric'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_numeric_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('numeric'))->once();
        $this->mock->foo('f');
        $this->container->mockery_verify();
    }

    public function test_object_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('object'))->once();
        $this->mock->foo(new stdClass);
        $this->container->mockery_verify();
    }

    public function test_object_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('object`'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_object_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('object'))->once();
        $this->mock->foo('f');
        $this->container->mockery_verify();
    }

    public function test_real_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('real'))->once();
        $this->mock->foo(2.25);
        $this->container->mockery_verify();
    }

    public function test_real_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('real'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_real_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('real'))->once();
        $this->mock->foo('f');
        $this->container->mockery_verify();
    }

    public function test_resource_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('resource'))->once();
        $r = fopen(dirname(__FILE__).'/_files/file.txt', 'r');
        $this->mock->foo($r);
        $this->container->mockery_verify();
    }

    public function test_resource_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('resource'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_resource_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('resource'))->once();
        $this->mock->foo('f');
        $this->container->mockery_verify();
    }

    public function test_scalar_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('scalar'))->once();
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    public function test_scalar_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('scalar'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_scalar_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('scalar'))->once();
        $this->mock->foo([]);
        $this->container->mockery_verify();
    }

    public function test_string_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('string'))->once();
        $this->mock->foo('2');
        $this->container->mockery_verify();
    }

    public function test_string_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('string'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_string_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('string'))->once();
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_class_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('stdClass'))->once();
        $this->mock->foo(new stdClass);
        $this->container->mockery_verify();
    }

    public function test_class_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::type('stdClass'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_class_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::type('stdClass'))->once();
        $this->mock->foo(new Exception);
        $this->container->mockery_verify();
    }

    public function test_ducktype_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::ducktype('quack', 'swim'))->once();
        $this->mock->foo(new Mockery_Duck);
        $this->container->mockery_verify();
    }

    public function test_ducktype_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::ducktype('quack', 'swim'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_ducktype_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::ducktype('quack', 'swim'))->once();
        $this->mock->foo(new Mockery_Duck_Nonswimmer);
        $this->container->mockery_verify();
    }

    public function test_array_content_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::subset(['a' => 1, 'b' => 2]))->once();
        $this->mock->foo(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->container->mockery_verify();
    }

    public function test_array_content_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::subset(['a' => 1, 'b' => 2]))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_array_content_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::subset(['a' => 1, 'b' => 2]))->once();
        $this->mock->foo(['a' => 1, 'c' => 3]);
        $this->container->mockery_verify();
    }

    public function test_contains_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::contains(1, 2))->once();
        $this->mock->foo(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->container->mockery_verify();
    }

    public function test_contains_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::contains(1, 2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_contains_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::contains(1, 2))->once();
        $this->mock->foo(['a' => 1, 'c' => 3]);
        $this->container->mockery_verify();
    }

    public function test_has_key_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::hasKey('c'))->once();
        $this->mock->foo(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->container->mockery_verify();
    }

    public function test_has_key_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::hasKey('a'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, ['a' => 1], 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_has_key_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::hasKey('c'))->once();
        $this->mock->foo(['a' => 1, 'b' => 3]);
        $this->container->mockery_verify();
    }

    public function test_has_value_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::hasValue(1))->once();
        $this->mock->foo(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->container->mockery_verify();
    }

    public function test_has_value_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::hasValue(1))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, ['a' => 1], 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_has_value_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::hasValue(2))->once();
        $this->mock->foo(['a' => 1, 'b' => 3]);
        $this->container->mockery_verify();
    }

    public function test_on_constraint_matches_argument_closure_evaluates_to_true()
    {
        $function = function ($arg) {
            return $arg % 2 == 0;
        };
        $this->mock->shouldReceive('foo')->with(Mockery::on($function))->once();
        $this->mock->foo(4);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_on_constraint_throws_exception_when_constraint_unmatched_closure_evaluates_to_false()
    {
        $function = function ($arg) {
            return $arg % 2 == 0;
        };
        $this->mock->shouldReceive('foo')->with(Mockery::on($function))->once();
        $this->mock->foo(5);
        $this->container->mockery_verify();
    }

    public function test_must_be_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::mustBe(2))->once();
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    public function test_must_be_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::mustBe(2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_must_be_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::mustBe(2))->once();
        $this->mock->foo('2');
        $this->container->mockery_verify();
    }

    public function test_must_be_constraint_matches_object_argument_with_equals_comparison_not_identical()
    {
        $a = new stdClass;
        $a->foo = 1;
        $b = new stdClass;
        $b->foo = 1;
        $this->mock->shouldReceive('foo')->with(Mockery::mustBe($a))->once();
        $this->mock->foo($b);
        $this->container->mockery_verify();
    }

    public function test_must_be_constraint_non_matching_case_with_object()
    {
        $a = new stdClass;
        $a->foo = 1;
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::mustBe($a))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, $a, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_must_be_constraint_throws_exception_when_constraint_unmatched_with_object()
    {
        $a = new stdClass;
        $a->foo = 1;
        $b = new stdClass;
        $b->foo = 2;
        $this->mock->shouldReceive('foo')->with(Mockery::mustBe($a))->once();
        $this->mock->foo($b);
        $this->container->mockery_verify();
    }

    public function test_match_precedence_based_on_expected_calls_favouring_explicit_match()
    {
        $this->mock->shouldReceive('foo')->with(1)->once();
        $this->mock->shouldReceive('foo')->with(Mockery::any())->never();
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_match_precedence_based_on_expected_calls_favouring_any_match()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::any())->once();
        $this->mock->shouldReceive('foo')->with(1)->never();
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_return_null_if_ignore_missing_methods_set()
    {
        $this->mock->shouldIgnoreMissing();
        $this->assertNull($this->mock->g(1, 2));
    }

    public function test_return_undefined_if_ignore_missing_methods_set()
    {
        $this->mock->shouldIgnoreMissing()->asUndefined();
        $this->assertTrue($this->mock->g(1, 2) instanceof \Mockery\Undefined);
    }

    public function test_return_as_undefined_allows_for_infinite_self_returning_chain()
    {
        $this->mock->shouldIgnoreMissing()->asUndefined();
        $this->assertTrue($this->mock->g(1, 2)->a()->b()->c() instanceof \Mockery\Undefined);
    }

    public function test_should_ignore_missing_fluent_interface()
    {
        $this->assertTrue($this->mock->shouldIgnoreMissing() instanceof \Mockery\MockInterface);
    }

    public function test_should_ignore_missing_as_undefined_fluent_interface()
    {
        $this->assertTrue($this->mock->shouldIgnoreMissing()->asUndefined() instanceof \Mockery\MockInterface);
    }

    public function test_should_ignore_missing_as_defined_proxies_to_undefined_allowing_to_string()
    {
        $this->mock->shouldIgnoreMissing()->asUndefined();
        $string = "Method call: {$this->mock->g()}";
        $string = "Mock: {$this->mock}";
    }

    public function test_should_ignore_missing_default_return_value()
    {
        $this->mock->shouldIgnoreMissing(1);
        $this->assertEquals(1, $this->mock->a());
    }

    /** @issue #253 */
    public function test_should_ignore_missing_default_self_and_returns_self()
    {
        $this->mock->shouldIgnoreMissing($this->container->self());
        $this->assertSame($this->mock, $this->mock->a()->b());
    }

    public function test_to_string_magic_method_can_be_mocked()
    {
        $this->mock->shouldReceive('__toString')->andReturn('dave');
        $this->assertEquals("{$this->mock}", 'dave');
    }

    public function test_optional_mock_retrieval()
    {
        $m = $this->container->mock('f')->shouldReceive('foo')->with(1)->andReturn(3)->mock();
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function test_not_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::not(1))->once();
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    public function test_not_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::not(2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_not_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::not(2))->once();
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    public function test_any_of_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::anyOf(1, 2))->twice();
        $this->mock->foo(2);
        $this->mock->foo(1);
        $this->container->mockery_verify();
    }

    public function test_any_of_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::anyOf(1, 2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_any_of_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::anyOf(1, 2))->once();
        $this->mock->foo(3);
        $this->container->mockery_verify();
    }

    public function test_not_any_of_constraint_matches_argument()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::notAnyOf(1, 2))->once();
        $this->mock->foo(3);
        $this->container->mockery_verify();
    }

    public function test_not_any_of_constraint_non_matching_case()
    {
        $this->mock->shouldReceive('foo')->times(3);
        $this->mock->shouldReceive('foo')->with(1, Mockery::notAnyOf(1, 2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 4, 3);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_not_any_of_constraint_throws_exception_when_constraint_unmatched()
    {
        $this->mock->shouldReceive('foo')->with(Mockery::notAnyOf(1, 2))->once();
        $this->mock->foo(2);
        $this->container->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_global_config_may_forbid_mocking_non_existent_methods_on_classes()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = $this->container->mock('stdClass');
        $mock->shouldReceive('foo');
    }

    /**
     * @expectedException \Mockery\Exception
     *
     * @expectedExceptionMessage Mockery's configuration currently forbids mocking
     */
    public function test_global_config_may_forbid_mocking_non_existent_methods_on_auto_declared_classes()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = $this->container->mock('SomeMadeUpClass');
        $mock->shouldReceive('foo');
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_global_config_may_forbid_mocking_non_existent_methods_on_objects()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = $this->container->mock(new stdClass);
        $mock->shouldReceive('foo');
    }

    public function test_an_example_with_some_expectation_amends()
    {
        $service = $this->container->mock('MyService');
        $service->shouldReceive('login')->with('user', 'pass')->once()->andReturn(true);
        $service->shouldReceive('hasBookmarksTagged')->with('php')->once()->andReturn(false);
        $service->shouldReceive('addBookmark')->with('/^http:/', \Mockery::type('string'))->times(3)->andReturn(true);
        $service->shouldReceive('hasBookmarksTagged')->with('php')->once()->andReturn(true);

        $this->assertTrue($service->login('user', 'pass'));
        $this->assertFalse($service->hasBookmarksTagged('php'));
        $this->assertTrue($service->addBookmark('http://example.com/1', 'some_tag1'));
        $this->assertTrue($service->addBookmark('http://example.com/2', 'some_tag2'));
        $this->assertTrue($service->addBookmark('http://example.com/3', 'some_tag3'));
        $this->assertTrue($service->hasBookmarksTagged('php'));

        $this->container->mockery_verify();
    }

    public function test_an_example_with_some_expectation_amends_on_call_counts()
    {
        $service = $this->container->mock('MyService');
        $service->shouldReceive('login')->with('user', 'pass')->once()->andReturn(true);
        $service->shouldReceive('hasBookmarksTagged')->with('php')->once()->andReturn(false);
        $service->shouldReceive('addBookmark')->with('/^http:/', \Mockery::type('string'))->times(3)->andReturn(true);
        $service->shouldReceive('hasBookmarksTagged')->with('php')->twice()->andReturn(true);

        $this->assertTrue($service->login('user', 'pass'));
        $this->assertFalse($service->hasBookmarksTagged('php'));
        $this->assertTrue($service->addBookmark('http://example.com/1', 'some_tag1'));
        $this->assertTrue($service->addBookmark('http://example.com/2', 'some_tag2'));
        $this->assertTrue($service->addBookmark('http://example.com/3', 'some_tag3'));
        $this->assertTrue($service->hasBookmarksTagged('php'));
        $this->assertTrue($service->hasBookmarksTagged('php'));

        $this->container->mockery_verify();
    }

    public function test_an_example_with_some_expectation_amends_on_call_counts_php_unit_test()
    {
        $service = $this->getMock('MyService2');
        $service->expects($this->once())->method('login')->with('user', 'pass')->will($this->returnValue(true));
        $service->expects($this->exactly(3))->method('hasBookmarksTagged')->with('php')
            ->will($this->onConsecutiveCalls(false, true, true));
        $service->expects($this->exactly(3))->method('addBookmark')
            ->with($this->matchesRegularExpression('/^http:/'), $this->isType('string'))
            ->will($this->returnValue(true));

        $this->assertTrue($service->login('user', 'pass'));
        $this->assertFalse($service->hasBookmarksTagged('php'));
        $this->assertTrue($service->addBookmark('http://example.com/1', 'some_tag1'));
        $this->assertTrue($service->addBookmark('http://example.com/2', 'some_tag2'));
        $this->assertTrue($service->addBookmark('http://example.com/3', 'some_tag3'));
        $this->assertTrue($service->hasBookmarksTagged('php'));
        $this->assertTrue($service->hasBookmarksTagged('php'));
    }

    public function test_mocked_methods_callable_from_within_original_class()
    {
        $mock = $this->container->mock('MockeryTest_InterMethod1[doThird]');
        $mock->shouldReceive('doThird')->andReturn(true);
        $this->assertTrue($mock->doFirst());
    }

    /**
     * @group issue #20
     */
    public function test_mocking_demeter_chains_passes_mockery_expectation_to_composite_expectation()
    {
        $mock = $this->container->mock('Mockery_Demeterowski');
        $mock->shouldReceive('foo->bar->baz')->andReturn('Spam!');
        $demeter = new Mockery_UseDemeter($mock);
        $this->assertSame('Spam!', $demeter->doit());
    }

    /**
     * @group issue #20 - with args in demeter chain
     */
    public function test_mocking_demeter_chains_passes_mockery_expectation_to_composite_expectation_with_args()
    {
        $mock = $this->container->mock('Mockery_Demeterowski');
        $mock->shouldReceive('foo->bar->baz')->andReturn('Spam!');
        $demeter = new Mockery_UseDemeter($mock);
        $this->assertSame('Spam!', $demeter->doitWithArgs());
    }

    public function test_passthru_ensures_real_method_called_for_return_values()
    {
        $mock = $this->container->mock('MockeryTest_SubjectCall1');
        $mock->shouldReceive('foo')->once()->passthru();
        $this->assertEquals('bar', $mock->foo());
        $this->container->mockery_verify();
    }

    public function test_should_ignore_missing_expectation_based_on_args()
    {
        $mock = $this->container->mock('MyService2')->shouldIgnoreMissing();
        $mock->shouldReceive('hasBookmarksTagged')->with('dave')->once();
        $mock->hasBookmarksTagged('dave');
        $mock->hasBookmarksTagged('padraic');
        $this->container->mockery_verify();
    }

    public function test_should_defer_missing_expectation_based_on_args()
    {
        $mock = $this->container->mock('MockeryTest_SubjectCall1')->shouldDeferMissing();

        $this->assertEquals('bar', $mock->foo());
        $this->assertEquals('bar', $mock->foo('baz'));
        $this->assertEquals('bar', $mock->foo('qux'));

        $mock->shouldReceive('foo')->with('baz')->twice()->andReturn('123');
        $this->assertEquals('bar', $mock->foo());
        $this->assertEquals('123', $mock->foo('baz'));
        $this->assertEquals('bar', $mock->foo('qux'));

        $mock->shouldReceive('foo')->withNoArgs()->once()->andReturn('456');
        $this->assertEquals('456', $mock->foo());
        $this->assertEquals('123', $mock->foo('baz'));
        $this->assertEquals('bar', $mock->foo('qux'));

        $this->container->mockery_verify();
    }

    public function test_can_return_self()
    {
        $this->mock->shouldReceive('foo')->andReturnSelf();
        $this->assertSame($this->mock, $this->mock->foo());
    }

    public function test_expectation_can_be_overridden()
    {
        $this->mock->shouldReceive('foo')->once()->andReturn('green');
        $this->mock->shouldReceive('foo')->andReturn('blue');
        $this->assertEquals($this->mock->foo(), 'green');
        $this->assertEquals($this->mock->foo(), 'blue');
    }
}

class MockeryTest_SubjectCall1
{
    public function foo()
    {
        return 'bar';
    }
}

class MockeryTest_InterMethod1
{
    public function doFirst()
    {
        return $this->doSecond();
    }

    private function doSecond()
    {
        return $this->doThird();
    }

    public function doThird()
    {
        return false;
    }
}

class MyService2
{
    public function login($user, $pass) {}

    public function hasBookmarksTagged($tag) {}

    public function addBookmark($uri, $tag) {}
}

class Mockery_Duck
{
    public function quack() {}

    public function swim() {}
}

class Mockery_Duck_Nonswimmer
{
    public function quack() {}
}

class Mockery_Demeterowski
{
    public function foo()
    {
        return $this;
    }

    public function bar()
    {
        return $this;
    }

    public function baz()
    {
        return 'Ham!';
    }
}

class Mockery_UseDemeter
{
    public function __construct($demeter)
    {
        $this->demeter = $demeter;
    }

    public function doit()
    {
        return $this->demeter->foo()->bar()->baz();
    }

    public function doitWithArgs()
    {
        return $this->demeter->foo('foo')->bar('bar')->baz('baz');
    }
}

class MockeryTest_Foo
{
    public function foo() {}
}
