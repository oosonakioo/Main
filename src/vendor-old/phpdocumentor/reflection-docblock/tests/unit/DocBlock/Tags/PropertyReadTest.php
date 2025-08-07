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
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\String_;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\DocBlock\Tags\PropertyRead
 *
 * @covers ::<private>
 */
class PropertyReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\PropertyRead::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_correct_tag_name_is_returned()
    {
        $fixture = new PropertyRead('myProperty', null, new Description('Description'));

        $this->assertSame('property-read', $fixture->getName());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\PropertyRead::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\PropertyRead::__toString
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_tag_can_be_rendered_using_default_formatter()
    {
        $fixture = new PropertyRead('myProperty', new String_, new Description('Description'));
        $this->assertSame('@property-read string $myProperty Description', $fixture->render());

        $fixture = new PropertyRead('myProperty', null, new Description('Description'));
        $this->assertSame('@property-read $myProperty Description', $fixture->render());

        $fixture = new PropertyRead('myProperty');
        $this->assertSame('@property-read $myProperty', $fixture->render());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\PropertyRead::__construct
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     */
    public function test_if_tag_can_be_rendered_using_specific_formatter()
    {
        $fixture = new PropertyRead('myProperty');

        $formatter = m::mock(Formatter::class);
        $formatter->shouldReceive('format')->with($fixture)->andReturn('Rendered output');

        $this->assertSame('Rendered output', $fixture->render($formatter));
    }

    /**
     * @covers ::__construct
     * @covers ::getVariableName
     */
    public function test_has_variable_name()
    {
        $expected = 'myProperty';

        $fixture = new PropertyRead($expected);

        $this->assertSame($expected, $fixture->getVariableName());
    }

    /**
     * @covers ::__construct
     * @covers ::getType
     */
    public function test_has_type()
    {
        $expected = new String_;

        $fixture = new PropertyRead('myProperty', $expected);

        $this->assertSame($expected, $fixture->getType());
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

        $fixture = new PropertyRead('1.0', null, $expected);

        $this->assertSame($expected, $fixture->getDescription());
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     *
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     * @uses   \phpDocumentor\Reflection\Types\String_
     */
    public function test_string_representation_is_returned()
    {
        $fixture = new PropertyRead('myProperty', new String_, new Description('Description'));

        $this->assertSame('string $myProperty Description', (string) $fixture);
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\PropertyRead::<public>
     * @uses \phpDocumentor\Reflection\DocBlock\DescriptionFactory
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function test_factory_method()
    {
        $typeResolver = new TypeResolver;
        $descriptionFactory = m::mock(DescriptionFactory::class);
        $context = new Context('');

        $description = new Description('My Description');
        $descriptionFactory->shouldReceive('create')->with('My Description', $context)->andReturn($description);

        $fixture = PropertyRead::create('string $myProperty My Description', $typeResolver, $descriptionFactory,
            $context);

        $this->assertSame('string $myProperty My Description', (string) $fixture);
        $this->assertSame('myProperty', $fixture->getVariableName());
        $this->assertInstanceOf(String_::class, $fixture->getType());
        $this->assertSame($description, $fixture->getDescription());
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\PropertyRead::<public>
     * @uses \phpDocumentor\Reflection\TypeResolver
     * @uses \phpDocumentor\Reflection\DocBlock\DescriptionFactory
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_empty_body_is_given()
    {
        $descriptionFactory = m::mock(DescriptionFactory::class);
        PropertyRead::create('', new TypeResolver, $descriptionFactory);
    }

    /**
     * @covers ::create
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_body_is_not_string()
    {
        PropertyRead::create([]);
    }

    /**
     * @covers ::create
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_resolver_is_null()
    {
        PropertyRead::create('body');
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\TypeResolver
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_description_factory_is_null()
    {
        PropertyRead::create('body', new TypeResolver);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_exception_is_thrown_if_variable_name_is_not_string()
    {
        new PropertyRead([]);
    }
}
