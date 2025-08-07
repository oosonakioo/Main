<?php

namespace Hamcrest\Core;

class PhpForm
{
    public function __toString()
    {
        return 'php';
    }
}

class JavaForm
{
    public function toString()
    {
        return 'java';
    }
}

class BothForms
{
    public function __toString()
    {
        return 'php';
    }

    public function toString()
    {
        return 'java';
    }
}

class HasToStringTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\HasToString::hasToString('foo');
    }

    public function test_matches_when_to_string_matches()
    {
        $this->assertMatches(
            hasToString(equalTo('php')),
            new \Hamcrest\Core\PhpForm,
            'correct __toString'
        );
        $this->assertMatches(
            hasToString(equalTo('java')),
            new \Hamcrest\Core\JavaForm,
            'correct toString'
        );
    }

    public function test_picks_java_over_php_to_string()
    {
        $this->assertMatches(
            hasToString(equalTo('java')),
            new \Hamcrest\Core\BothForms,
            'correct toString'
        );
    }

    public function test_does_not_match_when_to_string_does_not_match()
    {
        $this->assertDoesNotMatch(
            hasToString(equalTo('mismatch')),
            new \Hamcrest\Core\PhpForm,
            'incorrect __toString'
        );
        $this->assertDoesNotMatch(
            hasToString(equalTo('mismatch')),
            new \Hamcrest\Core\JavaForm,
            'incorrect toString'
        );
        $this->assertDoesNotMatch(
            hasToString(equalTo('mismatch')),
            new \Hamcrest\Core\BothForms,
            'incorrect __toString'
        );
    }

    public function test_does_not_match_null()
    {
        $this->assertDoesNotMatch(
            hasToString(equalTo('a')),
            null,
            'should not match null'
        );
    }

    public function test_provides_convenient_shortcut_for_traversable_with_size_equal_to()
    {
        $this->assertMatches(
            hasToString(equalTo('php')),
            new \Hamcrest\Core\PhpForm,
            'correct __toString'
        );
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription(
            'an object with toString() "php"',
            hasToString(equalTo('php'))
        );
    }
}
