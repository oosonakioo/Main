<?php

namespace PhpParser;

use PhpParser\Parser\Tokens;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    /* To allow overwriting in parent class */
    protected function getLexer(array $options = [])
    {
        return new Lexer($options);
    }

    /**
     * @dataProvider provideTestError
     */
    public function test_error($code, $message)
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM does not throw warnings from token_get_all()');
        }

        $lexer = $this->getLexer();
        try {
            $lexer->startLexing($code);
        } catch (Error $e) {
            $this->assertSame($message, $e->getMessage());

            return;
        }

        $this->fail('Expected PhpParser\Error');
    }

    public function provideTestError()
    {
        return [
            ['<?php /*', 'Unterminated comment on line 1'],
            ['<?php '."\1", 'Unexpected character "'."\1".'" (ASCII 1) on unknown line'],
            ['<?php '."\0", 'Unexpected null byte on unknown line'],
        ];
    }

    /**
     * @dataProvider provideTestLex
     */
    public function test_lex($code, $options, $tokens)
    {
        $lexer = $this->getLexer($options);
        $lexer->startLexing($code);
        while ($id = $lexer->getNextToken($value, $startAttributes, $endAttributes)) {
            $token = array_shift($tokens);

            $this->assertSame($token[0], $id);
            $this->assertSame($token[1], $value);
            $this->assertEquals($token[2], $startAttributes);
            $this->assertEquals($token[3], $endAttributes);
        }
    }

    public function provideTestLex()
    {
        return [
            // tests conversion of closing PHP tag and drop of whitespace and opening tags
            [
                '<?php tokens ?>plaintext',
                [],
                [
                    [
                        Tokens::T_STRING, 'tokens',
                        ['startLine' => 1], ['endLine' => 1],
                    ],
                    [
                        ord(';'), '?>',
                        ['startLine' => 1], ['endLine' => 1],
                    ],
                    [
                        Tokens::T_INLINE_HTML, 'plaintext',
                        ['startLine' => 1], ['endLine' => 1],
                    ],
                ],
            ],
            // tests line numbers
            [
                '<?php'."\n".'$ token /** doc'."\n".'comment */ $',
                [],
                [
                    [
                        ord('$'), '$',
                        ['startLine' => 2], ['endLine' => 2],
                    ],
                    [
                        Tokens::T_STRING, 'token',
                        ['startLine' => 2], ['endLine' => 2],
                    ],
                    [
                        ord('$'), '$',
                        [
                            'startLine' => 3,
                            'comments' => [
                                new Comment\Doc('/** doc'."\n".'comment */', 2, 14),
                            ],
                        ],
                        ['endLine' => 3],
                    ],
                ],
            ],
            // tests comment extraction
            [
                '<?php /* comment */ // comment'."\n".'/** docComment 1 *//** docComment 2 */ token',
                [],
                [
                    [
                        Tokens::T_STRING, 'token',
                        [
                            'startLine' => 2,
                            'comments' => [
                                new Comment('/* comment */', 1, 6),
                                new Comment('// comment'."\n", 1, 20),
                                new Comment\Doc('/** docComment 1 */', 2, 31),
                                new Comment\Doc('/** docComment 2 */', 2, 50),
                            ],
                        ],
                        ['endLine' => 2],
                    ],
                ],
            ],
            // tests differing start and end line
            [
                '<?php "foo'."\n".'bar"',
                [],
                [
                    [
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"foo'."\n".'bar"',
                        ['startLine' => 1], ['endLine' => 2],
                    ],
                ],
            ],
            // tests exact file offsets
            [
                '<?php "a";'."\n".'// foo'."\n".'"b";',
                ['usedAttributes' => ['startFilePos', 'endFilePos']],
                [
                    [
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"a"',
                        ['startFilePos' => 6], ['endFilePos' => 8],
                    ],
                    [
                        ord(';'), ';',
                        ['startFilePos' => 9], ['endFilePos' => 9],
                    ],
                    [
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"b"',
                        ['startFilePos' => 18], ['endFilePos' => 20],
                    ],
                    [
                        ord(';'), ';',
                        ['startFilePos' => 21], ['endFilePos' => 21],
                    ],
                ],
            ],
            // tests token offsets
            [
                '<?php "a";'."\n".'// foo'."\n".'"b";',
                ['usedAttributes' => ['startTokenPos', 'endTokenPos']],
                [
                    [
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"a"',
                        ['startTokenPos' => 1], ['endTokenPos' => 1],
                    ],
                    [
                        ord(';'), ';',
                        ['startTokenPos' => 2], ['endTokenPos' => 2],
                    ],
                    [
                        Tokens::T_CONSTANT_ENCAPSED_STRING, '"b"',
                        ['startTokenPos' => 5], ['endTokenPos' => 5],
                    ],
                    [
                        ord(';'), ';',
                        ['startTokenPos' => 6], ['endTokenPos' => 6],
                    ],
                ],
            ],
            // tests all attributes being disabled
            [
                '<?php /* foo */ $bar;',
                ['usedAttributes' => []],
                [
                    [
                        Tokens::T_VARIABLE, '$bar',
                        [], [],
                    ],
                    [
                        ord(';'), ';',
                        [], [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideTestHaltCompiler
     */
    public function test_handle_halt_compiler($code, $remaining)
    {
        $lexer = $this->getLexer();
        $lexer->startLexing($code);

        while ($lexer->getNextToken() !== Tokens::T_HALT_COMPILER);

        $this->assertSame($remaining, $lexer->handleHaltCompiler());
        $this->assertSame(0, $lexer->getNextToken());
    }

    public function provideTestHaltCompiler()
    {
        return [
            ['<?php ... __halt_compiler();Remaining Text', 'Remaining Text'],
            ['<?php ... __halt_compiler ( ) ;Remaining Text', 'Remaining Text'],
            ['<?php ... __halt_compiler() ?>Remaining Text', 'Remaining Text'],
            // array('<?php ... __halt_compiler();' . "\0", "\0"),
            // array('<?php ... __halt_compiler /* */ ( ) ;Remaining Text', 'Remaining Text'),
        ];
    }

    /**
     * @expectedException \PhpParser\Error
     *
     * @expectedExceptionMessage __HALT_COMPILER must be followed by "();"
     */
    public function test_handle_halt_compiler_error()
    {
        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ... __halt_compiler invalid ();');

        while ($lexer->getNextToken() !== Tokens::T_HALT_COMPILER);
        $lexer->handleHaltCompiler();
    }

    public function test_get_tokens()
    {
        $code = '<?php "a";'."\n".'// foo'."\n".'"b";';
        $expectedTokens = [
            [T_OPEN_TAG, '<?php ', 1],
            [T_CONSTANT_ENCAPSED_STRING, '"a"', 1],
            ';',
            [T_WHITESPACE, "\n", 1],
            [T_COMMENT, '// foo'."\n", 2],
            [T_CONSTANT_ENCAPSED_STRING, '"b"', 3],
            ';',
        ];

        $lexer = $this->getLexer();
        $lexer->startLexing($code);
        $this->assertSame($expectedTokens, $lexer->getTokens());
    }
}
