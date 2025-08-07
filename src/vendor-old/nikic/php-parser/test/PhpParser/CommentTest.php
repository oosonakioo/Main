<?php

namespace PhpParser;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_set()
    {
        $comment = new Comment('/* Some comment */', 1, 10);

        $this->assertSame('/* Some comment */', $comment->getText());
        $this->assertSame('/* Some comment */', (string) $comment);
        $this->assertSame(1, $comment->getLine());
        $this->assertSame(10, $comment->getFilePos());

        $comment->setText('/* Some other comment */');
        $comment->setLine(10);

        $this->assertSame('/* Some other comment */', $comment->getText());
        $this->assertSame('/* Some other comment */', (string) $comment);
        $this->assertSame(10, $comment->getLine());
    }

    /**
     * @dataProvider provideTestReformatting
     */
    public function test_reformatting($commentText, $reformattedText)
    {
        $comment = new Comment($commentText);
        $this->assertSame($reformattedText, $comment->getReformattedText());
    }

    public function provideTestReformatting()
    {
        return [
            ['// Some text'."\n", '// Some text'],
            ['/* Some text */', '/* Some text */'],
            [
                '/**
     * Some text.
     * Some more text.
     */',
                '/**
 * Some text.
 * Some more text.
 */',
            ],
            [
                '/*
        Some text.
        Some more text.
    */',
                '/*
    Some text.
    Some more text.
*/',
            ],
            [
                '/* Some text.
       More text.
       Even more text. */',
                '/* Some text.
   More text.
   Even more text. */',
            ],
            [
                '/* Some text.
       More text.
         Indented text. */',
                '/* Some text.
   More text.
     Indented text. */',
            ],
            // invalid comment -> no reformatting
            [
                'hallo
    world',
                'hallo
    world',
            ],
        ];
    }
}
