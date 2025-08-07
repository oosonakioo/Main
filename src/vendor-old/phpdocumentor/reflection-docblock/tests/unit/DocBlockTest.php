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

namespace phpDocumentor\Reflection;

use Mockery as m;
use phpDocumentor\Reflection\Types\Context;

/**
 * @coversDefaultClass phpDocumentor\Reflection\DocBlock
 *
 * @covers ::<private>
 *
 * @uses \Webmozart\Assert\Assert
 */
class DocBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getSummary
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     */
    public function test_doc_block_can_have_a_summary()
    {
        $summary = 'This is a summary';

        $fixture = new DocBlock($summary);

        $this->assertSame($summary, $fixture->getSummary());
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_exception_is_thrown_if_summary_is_not_a_string()
    {
        new DocBlock([]);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_exception_is_thrown_if_template_start_is_not_a_boolean()
    {
        new DocBlock('', null, [], null, null, ['is not boolean']);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_exception_is_thrown_if_template_end_is_not_a_boolean()
    {
        new DocBlock('', null, [], null, null, false, ['is not boolean']);
    }

    /**
     * @covers ::__construct
     * @covers ::getDescription
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     */
    public function test_doc_block_can_have_a_description()
    {
        $description = new DocBlock\Description('');

        $fixture = new DocBlock('', $description);

        $this->assertSame($description, $fixture->getDescription());
    }

    /**
     * @covers ::__construct
     * @covers ::getTags
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\DocBlock\Tag
     */
    public function test_doc_block_can_have_tags()
    {
        $tags = [
            m::mock(DocBlock\Tag::class),
        ];

        $fixture = new DocBlock('', null, $tags);

        $this->assertSame($tags, $fixture->getTags());
    }

    /**
     * @covers ::__construct
     * @covers ::getTags
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\DocBlock\Tag
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_doc_block_allows_only_tags()
    {
        $tags = [
            null,
        ];

        $fixture = new DocBlock('', null, $tags);
    }

    /**
     * @covers ::__construct
     * @covers ::getTagsByName
     *
     * @uses \phpDocumentor\Reflection\DocBlock::getTags
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\DocBlock\Tag
     */
    public function test_find_tags_in_doc_block_by_name()
    {
        $tag1 = m::mock(DocBlock\Tag::class);
        $tag2 = m::mock(DocBlock\Tag::class);
        $tag3 = m::mock(DocBlock\Tag::class);
        $tags = [$tag1, $tag2, $tag3];

        $tag1->shouldReceive('getName')->andReturn('abc');
        $tag2->shouldReceive('getName')->andReturn('abcd');
        $tag3->shouldReceive('getName')->andReturn('ab');

        $fixture = new DocBlock('', null, $tags);

        $this->assertSame([$tag2], $fixture->getTagsByName('abcd'));
        $this->assertSame([], $fixture->getTagsByName('Ebcd'));
    }

    /**
     * @covers ::__construct
     * @covers ::getTagsByName
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_exception_is_thrown_if_name_for_tags_is_not_string()
    {
        $fixture = new DocBlock;
        $fixture->getTagsByName([]);
    }

    /**
     * @covers ::__construct
     * @covers ::hasTag
     *
     * @uses \phpDocumentor\Reflection\DocBlock::getTags
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\DocBlock\Tag
     */
    public function test_check_if_there_are_tags_with_a_given_name()
    {
        $tag1 = m::mock(DocBlock\Tag::class);
        $tag2 = m::mock(DocBlock\Tag::class);
        $tag3 = m::mock(DocBlock\Tag::class);
        $tags = [$tag1, $tag2, $tag3];

        $tag1->shouldReceive('getName')->twice()->andReturn('abc');
        $tag2->shouldReceive('getName')->twice()->andReturn('abcd');
        $tag3->shouldReceive('getName')->once();

        $fixture = new DocBlock('', null, $tags);

        $this->assertTrue($fixture->hasTag('abcd'));
        $this->assertFalse($fixture->hasTag('Ebcd'));
    }

    /**
     * @covers ::__construct
     * @covers ::hasTag
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_exception_is_thrown_if_name_for_checking_tags_is_not_string()
    {
        $fixture = new DocBlock;
        $fixture->hasTag([]);
    }

    /**
     * @covers ::__construct
     * @covers ::getContext
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function test_doc_block_knows_in_which_namespace_it_is_and_which_aliases_there_are()
    {
        $context = new Context('');

        $fixture = new DocBlock('', null, [], $context);

        $this->assertSame($context, $fixture->getContext());
    }

    /**
     * @covers ::__construct
     * @covers ::getLocation
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\Location
     */
    public function test_doc_block_knows_at_which_line_it_is()
    {
        $location = new Location(10);

        $fixture = new DocBlock('', null, [], null, $location);

        $this->assertSame($location, $fixture->getLocation());
    }

    /**
     * @covers ::__construct
     * @covers ::isTemplateStart
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     */
    public function test_doc_block_knows_if_it_is_the_start_of_a_doc_block_template()
    {
        $fixture = new DocBlock('', null, [], null, null, true);

        $this->assertTrue($fixture->isTemplateStart());
    }

    /**
     * @covers ::__construct
     * @covers ::isTemplateEnd
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     */
    public function test_doc_block_knows_if_it_is_the_end_of_a_doc_block_template()
    {
        $fixture = new DocBlock('', null, [], null, null, false, true);

        $this->assertTrue($fixture->isTemplateEnd());
    }
}
