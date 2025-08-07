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

use Mockery as m;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use phpDocumentor\Reflection\Types\Context;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\DocBlock\DescriptionFactory
 *
 * @covers ::<private>
 */
class DescriptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses         phpDocumentor\Reflection\DocBlock\Description
     *
     * @dataProvider provideSimpleExampleDescriptions
     */
    public function test_description_can_parse_a_simple_string($contents)
    {
        $tagFactory = m::mock(TagFactory::class);
        $tagFactory->shouldReceive('create')->never();

        $factory = new DescriptionFactory($tagFactory);
        $description = $factory->create($contents, new Context(''));

        $this->assertSame($contents, $description->render());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses         phpDocumentor\Reflection\DocBlock\Description
     *
     * @dataProvider provideEscapeSequences
     */
    public function test_escape_sequences($contents, $expected)
    {
        $tagFactory = m::mock(TagFactory::class);
        $tagFactory->shouldReceive('create')->never();

        $factory = new DescriptionFactory($tagFactory);
        $description = $factory->create($contents, new Context(''));

        $this->assertSame($expected, $description->render());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses   phpDocumentor\Reflection\DocBlock\Description
     * @uses   phpDocumentor\Reflection\DocBlock\Tags\Link
     * @uses   phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses   phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses   phpDocumentor\Reflection\Types\Context
     */
    public function test_description_can_parse_a_string_with_inline_tag()
    {
        $contents = 'This is text for a {@link http://phpdoc.org/ description} that uses an inline tag.';
        $context = new Context('');
        $tagFactory = m::mock(TagFactory::class);
        $tagFactory->shouldReceive('create')
            ->once()
            ->with('@link http://phpdoc.org/ description', $context)
            ->andReturn(new Link('http://phpdoc.org/', new Description('description')));

        $factory = new DescriptionFactory($tagFactory);
        $description = $factory->create($contents, $context);

        $this->assertSame($contents, $description->render());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses   phpDocumentor\Reflection\DocBlock\Description
     * @uses   phpDocumentor\Reflection\DocBlock\Tags\Link
     * @uses   phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses   phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses   phpDocumentor\Reflection\Types\Context
     */
    public function test_description_can_parse_a_string_starting_with_inline_tag()
    {
        $contents = '{@link http://phpdoc.org/ This} is text for a description that starts with an inline tag.';
        $context = new Context('');
        $tagFactory = m::mock(TagFactory::class);
        $tagFactory->shouldReceive('create')
            ->once()
            ->with('@link http://phpdoc.org/ This', $context)
            ->andReturn(new Link('http://phpdoc.org/', new Description('This')));

        $factory = new DescriptionFactory($tagFactory);
        $description = $factory->create($contents, $context);

        $this->assertSame($contents, $description->render());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses   phpDocumentor\Reflection\DocBlock\Description
     */
    public function test_if_superfluous_starting_spaces_are_removed()
    {
        $factory = new DescriptionFactory(m::mock(TagFactory::class));
        $descriptionText = <<<'DESCRIPTION'
This is a multiline
  description that you commonly
  see with tags.

      It does have a multiline code sample
      that should align, no matter what

  All spaces superfluous spaces on the
  second and later lines should be
  removed but the code sample should
  still be indented.
DESCRIPTION;

        $expectedDescription = <<<'DESCRIPTION'
This is a multiline
description that you commonly
see with tags.

    It does have a multiline code sample
    that should align, no matter what

All spaces superfluous spaces on the
second and later lines should be
removed but the code sample should
still be indented.
DESCRIPTION;

        $description = $factory->create($descriptionText, new Context(''));

        $this->assertSame($expectedDescription, $description->render());
    }

    /**
     * Provides a series of example strings that the parser should correctly interpret and return.
     *
     * @return string[][]
     */
    public function provideSimpleExampleDescriptions()
    {
        return [
            ['This is text for a description.'],
            ['This is text for a description containing { that is literal.'],
            ['This is text for a description containing } that is literal.'],
            ['This is text for a description with {just a text} that is not a tag.'],
        ];
    }

    public function provideEscapeSequences()
    {
        return [
            ['This is text for a description with a {@}.', 'This is text for a description with a @.'],
            ['This is text for a description with a {}.', 'This is text for a description with a }.'],
        ];
    }
}
