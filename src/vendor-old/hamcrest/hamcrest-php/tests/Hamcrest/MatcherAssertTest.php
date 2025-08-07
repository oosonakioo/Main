<?php

namespace Hamcrest;

class MatcherAssertTest extends \PhpUnit_Framework_TestCase
{
    protected function setUp()
    {
        \Hamcrest\MatcherAssert::resetCount();
    }

    public function test_reset_count()
    {
        \Hamcrest\MatcherAssert::assertThat(true);
        self::assertEquals(1, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
        \Hamcrest\MatcherAssert::resetCount();
        self::assertEquals(0, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
    }

    public function test_assert_that_with_true_arg_passes()
    {
        \Hamcrest\MatcherAssert::assertThat(true);
        \Hamcrest\MatcherAssert::assertThat('non-empty');
        \Hamcrest\MatcherAssert::assertThat(1);
        \Hamcrest\MatcherAssert::assertThat(3.14159);
        \Hamcrest\MatcherAssert::assertThat([true]);
        self::assertEquals(5, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
    }

    public function test_assert_that_with_false_arg_fails()
    {
        try {
            \Hamcrest\MatcherAssert::assertThat(false);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat(null);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat('');
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat(0);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat(0.0);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat([]);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('', $ex->getMessage());
        }
        self::assertEquals(6, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
    }

    public function test_assert_that_with_identifier_and_true_arg_passes()
    {
        \Hamcrest\MatcherAssert::assertThat('identifier', true);
        \Hamcrest\MatcherAssert::assertThat('identifier', 'non-empty');
        \Hamcrest\MatcherAssert::assertThat('identifier', 1);
        \Hamcrest\MatcherAssert::assertThat('identifier', 3.14159);
        \Hamcrest\MatcherAssert::assertThat('identifier', [true]);
        self::assertEquals(5, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
    }

    public function test_assert_that_with_identifier_and_false_arg_fails()
    {
        try {
            \Hamcrest\MatcherAssert::assertThat('identifier', false);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('identifier', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat('identifier', null);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('identifier', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat('identifier', '');
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('identifier', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat('identifier', 0);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('identifier', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat('identifier', 0.0);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('identifier', $ex->getMessage());
        }
        try {
            \Hamcrest\MatcherAssert::assertThat('identifier', []);
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals('identifier', $ex->getMessage());
        }
        self::assertEquals(6, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
    }

    public function test_assert_that_with_actual_value_and_matcher_args_that_match_passes()
    {
        \Hamcrest\MatcherAssert::assertThat(true, is(true));
        self::assertEquals(1, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
    }

    public function test_assert_that_with_actual_value_and_matcher_args_that_dont_match_fails()
    {
        $expected = 'expected';
        $actual = 'actual';

        $expectedMessage =
            'Expected: "expected"'.PHP_EOL.
            '     but: was "actual"';

        try {
            \Hamcrest\MatcherAssert::assertThat($actual, equalTo($expected));
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals($expectedMessage, $ex->getMessage());
            self::assertEquals(1, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
        }
    }

    public function test_assert_that_with_identifier_and_actual_value_and_matcher_args_that_match_passes()
    {
        \Hamcrest\MatcherAssert::assertThat('identifier', true, is(true));
        self::assertEquals(1, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
    }

    public function test_assert_that_with_identifier_and_actual_value_and_matcher_args_that_dont_match_fails()
    {
        $expected = 'expected';
        $actual = 'actual';

        $expectedMessage =
            'identifier'.PHP_EOL.
            'Expected: "expected"'.PHP_EOL.
            '     but: was "actual"';

        try {
            \Hamcrest\MatcherAssert::assertThat('identifier', $actual, equalTo($expected));
            self::fail('expected assertion failure');
        } catch (\Hamcrest\AssertionError $ex) {
            self::assertEquals($expectedMessage, $ex->getMessage());
            self::assertEquals(1, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
        }
    }

    public function test_assert_that_with_no_args_throws_error_and_doesnt_increment_count()
    {
        try {
            \Hamcrest\MatcherAssert::assertThat();
            self::fail('expected invalid argument exception');
        } catch (\InvalidArgumentException $ex) {
            self::assertEquals(0, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
        }
    }

    public function test_assert_that_with_four_args_throws_error_and_doesnt_increment_count()
    {
        try {
            \Hamcrest\MatcherAssert::assertThat(1, 2, 3, 4);
            self::fail('expected invalid argument exception');
        } catch (\InvalidArgumentException $ex) {
            self::assertEquals(0, \Hamcrest\MatcherAssert::getCount(), 'assertion count');
        }
    }
}
