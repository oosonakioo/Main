<?php

namespace Hamcrest\Text;

class IsEqualIgnoringWhiteSpaceTest extends \Hamcrest\AbstractMatcherTest
{
    private $_matcher;

    protected function setUp()
    {
        $this->_matcher = \Hamcrest\Text\IsEqualIgnoringWhiteSpace::equalToIgnoringWhiteSpace(
            "Hello World   how\n are we? "
        );
    }

    protected function createMatcher()
    {
        return $this->_matcher;
    }

    public function test_passes_if_words_are_same_but_whitespace_differs()
    {
        assertThat('Hello World how are we?', $this->_matcher);
        assertThat("   Hello \rWorld \t  how are\nwe?", $this->_matcher);
    }

    public function test_fails_if_text_other_than_whitespace_differs()
    {
        assertThat('Hello PLANET how are we?', not($this->_matcher));
        assertThat('Hello World how are we', not($this->_matcher));
    }

    public function test_fails_if_whitespace_is_added_or_removed_in_mid_word()
    {
        assertThat('HelloWorld how are we?', not($this->_matcher));
        assertThat('Hello Wo rld how are we?', not($this->_matcher));
    }

    public function test_fails_if_matching_against_null()
    {
        assertThat(null, not($this->_matcher));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription(
            'equalToIgnoringWhiteSpace("Hello World   how\\n are we? ")',
            $this->_matcher
        );
    }
}
