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
 * @coversDefaultClass \phpDocumentor\Reflection\DocBlock\Tags\Version
 *
 * @covers ::<private>
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Version::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_correct_tag_name_is_returned()
    {
        $fixture = new Version('1.0', new Description('Description'));

        $this->assertSame('version', $fixture->getName());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Version::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Version::__toString
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_tag_can_be_rendered_using_default_formatter()
    {
        $fixture = new Version('1.0', new Description('Description'));

        $this->assertSame('@version 1.0 Description', $fixture->render());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Version::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     */
    public function test_if_tag_can_be_rendered_using_specific_formatter()
    {
        $fixture = new Version('1.0', new Description('Description'));

        $formatter = m::mock(Formatter::class);
        $formatter->shouldReceive('format')->with($fixture)->andReturn('Rendered output');

        $this->assertSame('Rendered output', $fixture->render($formatter));
    }

    /**
     * @covers ::__construct
     * @covers ::getVersion
     */
    public function test_has_version_number()
    {
        $expected = '1.0';

        $fixture = new Version($expected);

        $this->assertSame($expected, $fixture->getVersion());
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

        $fixture = new Version('1.0', $expected);

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
        $fixture = new Version('1.0', new Description('Description'));

        $this->assertSame('1.0 Description', (string) $fixture);
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Version::<public>
     * @uses \phpDocumentor\Reflection\DocBlock\DescriptionFactory
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function test_factory_method()
    {
        $descriptionFactory = m::mock(DescriptionFactory::class);
        $context = new Context('');

        $version = '1.0';
        $description = new Description('My Description');

        $descriptionFactory->shouldReceive('create')->with('My Description', $context)->andReturn($description);

        $fixture = Version::create('1.0 My Description', $descriptionFactory, $context);

        $this->assertSame('1.0 My Description', (string) $fixture);
        $this->assertSame($version, $fixture->getVersion());
        $this->assertSame($description, $fixture->getDescription());
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Version::<public>
     * @uses \phpDocumentor\Reflection\DocBlock\DescriptionFactory
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function test_factory_method_creates_empty_version_tag()
    {
        $descriptionFactory = m::mock(DescriptionFactory::class);
        $descriptionFactory->shouldReceive('create')->never();

        $fixture = Version::create('', $descriptionFactory, new Context(''));

        $this->assertSame('', (string) $fixture);
        $this->assertSame(null, $fixture->getVersion());
        $this->assertSame(null, $fixture->getDescription());
    }

    /**
     * @covers ::create
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_version_is_not_string()
    {
        $this->assertNull(Version::create([]));
    }

    /**
     * @covers ::create
     */
    public function test_factory_method_returns_null_if_body_does_not_match_regex()
    {
        $this->assertNull(Version::create('dkhf<'));
    }
}
