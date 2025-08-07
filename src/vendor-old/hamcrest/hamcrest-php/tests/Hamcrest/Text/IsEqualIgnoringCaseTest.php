<?php

namespace Hamcrest\Text;

class IsEqualIgnoringCaseTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Text\IsEqualIgnoringCase::equalToIgnoringCase('irrelevant');
    }

    public function test_ignores_case_of_chars_in_string()
    {
        assertThat('HELLO', equalToIgnoringCase('heLLo'));
        assertThat('hello', equalToIgnoringCase('heLLo'));
        assertThat('HelLo', equalToIgnoringCase('heLLo'));

        assertThat('bye', not(equalToIgnoringCase('heLLo')));
    }

    public function test_fails_if_additional_whitespace_is_present()
    {
        assertThat('heLLo ', not(equalToIgnoringCase('heLLo')));
        assertThat(' heLLo', not(equalToIgnoringCase('heLLo')));
        assertThat('hello', not(equalToIgnoringCase(' heLLo')));
    }

    public function test_fails_if_matching_against_null()
    {
        assertThat(null, not(equalToIgnoringCase('heLLo')));
    }

    public function test_describes_itself_as_case_insensitive()
    {
        $this->assertDescription(
            'equalToIgnoringCase("heLLo")',
            equalToIgnoringCase('heLLo')
        );
    }
}
