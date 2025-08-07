<?php

namespace Hamcrest\Text;

class StringContainsIgnoringCaseTest extends \Hamcrest\AbstractMatcherTest
{
    const EXCERPT = 'ExcErPt';

    private $_stringContains;

    protected function setUp()
    {
        $this->_stringContains = \Hamcrest\Text\StringContainsIgnoringCase::containsStringIgnoringCase(
            strtolower(self::EXCERPT)
        );
    }

    protected function createMatcher()
    {
        return $this->_stringContains;
    }

    public function test_evaluates_to_true_if_argument_contains_specified_substring()
    {
        $this->assertTrue(
            $this->_stringContains->matches(self::EXCERPT.'END'),
            'should be true if excerpt at beginning'
        );
        $this->assertTrue(
            $this->_stringContains->matches('START'.self::EXCERPT),
            'should be true if excerpt at end'
        );
        $this->assertTrue(
            $this->_stringContains->matches('START'.self::EXCERPT.'END'),
            'should be true if excerpt in middle'
        );
        $this->assertTrue(
            $this->_stringContains->matches(self::EXCERPT.self::EXCERPT),
            'should be true if excerpt is repeated'
        );

        $this->assertFalse(
            $this->_stringContains->matches('Something else'),
            'should not be true if excerpt is not in string'
        );
        $this->assertFalse(
            $this->_stringContains->matches(substr(self::EXCERPT, 1)),
            'should not be true if part of excerpt is in string'
        );
    }

    public function test_evaluates_to_true_if_argument_is_equal_to_substring()
    {
        $this->assertTrue(
            $this->_stringContains->matches(self::EXCERPT),
            'should be true if excerpt is entire string'
        );
    }

    public function test_evaluates_to_true_if_argument_contains_exact_substring()
    {
        $this->assertTrue(
            $this->_stringContains->matches(strtolower(self::EXCERPT)),
            'should be false if excerpt is entire string ignoring case'
        );
        $this->assertTrue(
            $this->_stringContains->matches('START'.strtolower(self::EXCERPT).'END'),
            'should be false if excerpt is contained in string ignoring case'
        );
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription(
            'a string containing in any case "'
            .strtolower(self::EXCERPT).'"',
            $this->_stringContains
        );
    }
}
