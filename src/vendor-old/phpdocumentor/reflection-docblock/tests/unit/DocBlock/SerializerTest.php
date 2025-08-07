<?php

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\DocBlock;

use phpDocumentor\Reflection\DocBlock;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\DocBlock\Serializer
 *
 * @covers ::<private>
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getDocComment
     *
     * @uses phpDocumentor\Reflection\DocBlock\Description
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses phpDocumentor\Reflection\DocBlock
     * @uses phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Generic
     */
    public function test_reconstructs_a_doc_comment_from_a_doc_block()
    {
        $expected = <<<'DOCCOMMENT'
/**
 * This is a summary
 *
 * This is a description
 *
 * @unknown-tag Test description for the unknown tag
 */
DOCCOMMENT;

        $fixture = new Serializer;

        $docBlock = new DocBlock(
            'This is a summary',
            new Description('This is a description'),
            [
                new DocBlock\Tags\Generic('unknown-tag', new Description('Test description for the unknown tag')),
            ]
        );

        $this->assertSame($expected, $fixture->getDocComment($docBlock));
    }

    /**
     * @covers ::__construct
     * @covers ::getDocComment
     *
     * @uses phpDocumentor\Reflection\DocBlock\Description
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses phpDocumentor\Reflection\DocBlock
     * @uses phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Generic
     */
    public function test_add_prefix_to_doc_block()
    {
        $expected = <<<'DOCCOMMENT'
aa/**
aa * This is a summary
aa *
aa * This is a description
aa *
aa * @unknown-tag Test description for the unknown tag
aa */
DOCCOMMENT;

        $fixture = new Serializer(2, 'a');

        $docBlock = new DocBlock(
            'This is a summary',
            new Description('This is a description'),
            [
                new DocBlock\Tags\Generic('unknown-tag', new Description('Test description for the unknown tag')),
            ]
        );

        $this->assertSame($expected, $fixture->getDocComment($docBlock));
    }

    /**
     * @covers ::__construct
     * @covers ::getDocComment
     *
     * @uses phpDocumentor\Reflection\DocBlock\Description
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses phpDocumentor\Reflection\DocBlock
     * @uses phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Generic
     */
    public function test_add_prefix_to_doc_block_except_first_line()
    {
        $expected = <<<'DOCCOMMENT'
/**
aa * This is a summary
aa *
aa * This is a description
aa *
aa * @unknown-tag Test description for the unknown tag
aa */
DOCCOMMENT;

        $fixture = new Serializer(2, 'a', false);

        $docBlock = new DocBlock(
            'This is a summary',
            new Description('This is a description'),
            [
                new DocBlock\Tags\Generic('unknown-tag', new Description('Test description for the unknown tag')),
            ]
        );

        $this->assertSame($expected, $fixture->getDocComment($docBlock));
    }

    /**
     * @covers ::__construct
     * @covers ::getDocComment
     *
     * @uses phpDocumentor\Reflection\DocBlock\Description
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses phpDocumentor\Reflection\DocBlock
     * @uses phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Generic
     */
    public function test_wordwraps_around_the_given_amount_of_characters()
    {
        $expected = <<<'DOCCOMMENT'
/**
 * This is a
 * summary
 *
 * This is a
 * description
 *
 * @unknown-tag
 * Test
 * description
 * for the
 * unknown tag
 */
DOCCOMMENT;

        $fixture = new Serializer(0, '', true, 15);

        $docBlock = new DocBlock(
            'This is a summary',
            new Description('This is a description'),
            [
                new DocBlock\Tags\Generic('unknown-tag', new Description('Test description for the unknown tag')),
            ]
        );

        $this->assertSame($expected, $fixture->getDocComment($docBlock));
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_initialization_fails_if_indent_is_not_an_integer()
    {
        new Serializer([]);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_initialization_fails_if_indent_string_is_not_a_string()
    {
        new Serializer(0, []);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_initialization_fails_if_indent_first_line_is_not_a_boolean()
    {
        new Serializer(0, '', []);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_initialization_fails_if_line_length_is_not_null_nor_an_integer()
    {
        new Serializer(0, '', false, []);
    }
}
