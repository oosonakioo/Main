<?php

namespace Faker\Test\Provider;

use Faker\Provider\Lorem;

class LoremTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_text_throws_exception_when_asked_text_size_less_than5()
    {
        Lorem::text(4);
    }

    public function test_text_returns_words_when_asked_size_less_than25()
    {
        $this->assertEquals('Word word word word.', TestableLorem::text(24));
    }

    public function test_text_returns_sentences_when_asked_size_less_than100()
    {
        $this->assertEquals('This is a test sentence. This is a test sentence. This is a test sentence.', TestableLorem::text(99));
    }

    public function test_text_returns_paragraphs_when_asked_size_greater_or_equal_than_than100()
    {
        $this->assertEquals('This is a test paragraph. It has three sentences. Exactly three.', TestableLorem::text(100));
    }

    public function test_sentence_with_zero_nb_words_returns_empty_string()
    {
        $this->assertEquals('', Lorem::sentence(0));
    }

    public function test_sentence_with_negative_nb_words_returns_empty_string()
    {
        $this->assertEquals('', Lorem::sentence(-1));
    }

    public function test_paragraph_with_zero_nb_sentences_returns_empty_string()
    {
        $this->assertEquals('', Lorem::paragraph(0));
    }

    public function test_paragraph_with_negative_nb_sentences_returns_empty_string()
    {
        $this->assertEquals('', Lorem::paragraph(-1));
    }

    public function test_sentence_with_positive_nb_words_returns_at_least_one_word()
    {
        $sentence = Lorem::sentence(1);

        $this->assertGreaterThan(1, strlen($sentence));
        $this->assertGreaterThanOrEqual(1, count(explode(' ', $sentence)));
    }

    public function test_paragraph_with_positive_nb_sentences_returns_at_least_one_word()
    {
        $paragraph = Lorem::paragraph(1);

        $this->assertGreaterThan(1, strlen($paragraph));
        $this->assertGreaterThanOrEqual(1, count(explode(' ', $paragraph)));
    }

    public function test_wordss_as_text()
    {
        $words = TestableLorem::words(2, true);

        $this->assertEquals('word word', $words);
    }

    public function test_sentences_as_text()
    {
        $sentences = TestableLorem::sentences(2, true);

        $this->assertEquals('This is a test sentence. This is a test sentence.', $sentences);
    }

    public function test_paragraphs_as_text()
    {
        $paragraphs = TestableLorem::paragraphs(2, true);

        $expected = "This is a test paragraph. It has three sentences. Exactly three.\n\nThis is a test paragraph. It has three sentences. Exactly three.";
        $this->assertEquals($expected, $paragraphs);
    }
}

class TestableLorem extends Lorem
{
    public static function word()
    {
        return 'word';
    }

    public static function sentence($nbWords = 5, $variableNbWords = true)
    {
        return 'This is a test sentence.';
    }

    public static function paragraph($nbSentences = 3, $variableNbSentences = true)
    {
        return 'This is a test paragraph. It has three sentences. Exactly three.';
    }
}
