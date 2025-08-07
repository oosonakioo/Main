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

namespace phpDocumentor\Reflection\DocBlock\Tags;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\Types\Context;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\DocBlock\Tags\Link
 *
 * @covers ::<private>
 */
class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Link::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_correct_tag_name_is_returned()
    {
        $fixture = new Link('http://this.is.my/link', new Description('Description'));

        $this->assertSame('link', $fixture->getName());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Link::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Link::__toString
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_tag_can_be_rendered_using_default_formatter()
    {
        $fixture = new Link('http://this.is.my/link', new Description('Description'));

        $this->assertSame('@link http://this.is.my/link Description', $fixture->render());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Link::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     */
    public function test_if_tag_can_be_rendered_using_specific_formatter()
    {
        $fixture = new Link('http://this.is.my/link', new Description('Description'));

        $formatter = m::mock(Formatter::class);
        $formatter->shouldReceive('format')->with($fixture)->andReturn('Rendered output');

        $this->assertSame('Rendered output', $fixture->render($formatter));
    }

    /**
     * @covers ::__construct
     * @covers ::getLink
     */
    public function test_has_link_url()
    {
        $expected = 'http://this.is.my/link';

        $fixture = new Link($expected);

        $this->assertSame($expected, $fixture->getLink());
    }

    /**
     * @covers ::__construct
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getDescription
     *
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     */
    public function test_has_description()
    {
        $expected = new Description('Description');

        $fixture = new Link('http://this.is.my/link', $expected);

        $this->assertSame($expected, $fixture->getDescription());
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     *
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     */
    public function test_string_representation_is_returned()
    {
        $fixture = new Link('http://this.is.my/link', new Description('Description'));

        $this->assertSame('http://this.is.my/link Description', (string) $fixture);
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Link::<public>
     * @uses \phpDocumentor\Reflection\DocBlock\DescriptionFactory
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function test_factory_method()
    {
        $descriptionFactory = m::mock(DescriptionFactory::class);
        $context = new Context('');

        $links = 'http://this.is.my/link';
        $description = new Description('My Description');

        $descriptionFactory->shouldReceive('create')->with('My Description', $context)->andReturn($description);

        $fixture = Link::create('http://this.is.my/link My Description', $descriptionFactory, $context);

        $this->assertSame('http://this.is.my/link My Description', (string) $fixture);
        $this->assertSame($links, $fixture->getLink());
        $this->assertSame($description, $fixture->getDescription());
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Link::<public>
     * @uses \phpDocumentor\Reflection\DocBlock\DescriptionFactory
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function test_factory_method_creates_empty_link_tag()
    {
        $descriptionFactory = m::mock(DescriptionFactory::class);
        $descriptionFactory->shouldReceive('create')->never();

        $fixture = Link::create('', $descriptionFactory, new Context(''));

        $this->assertSame('', (string) $fixture);
        $this->assertSame('', $fixture->getLink());
        $this->assertSame(null, $fixture->getDescription());
    }

    /**
     * @covers ::create
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_version_is_not_string()
    {
        $this->assertNull(Link::create([]));
    }
}
