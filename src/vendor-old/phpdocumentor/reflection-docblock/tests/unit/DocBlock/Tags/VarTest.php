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
 * @coversDefaultClass \phpDocumentor\Reflection\DocBlock\Tags\Var_
 *
 * @covers ::<private>
 */
class VarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Var_::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_correct_tag_name_is_returned()
    {
        $fixture = new Var_('myVariable', null, new Description('Description'));

        $this->assertSame('var', $fixture->getName());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Var_::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Var_::__toString
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses   \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_tag_can_be_rendered_using_default_formatter()
    {
        $fixture = new Var_('myVariable', new String_, new Description('Description'));
        $this->assertSame('@var string $myVariable Description', $fixture->render());

        $fixture = new Var_('myVariable', null, new Description('Description'));
        $this->assertSame('@var $myVariable Description', $fixture->render());

        $fixture = new Var_('myVariable');
        $this->assertSame('@var $myVariable', $fixture->render());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Var_::__construct
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     */
    public function test_if_tag_can_be_rendered_using_specific_formatter()
    {
        $fixture = new Var_('myVariable');

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
        $expected = 'myVariable';

        $fixture = new Var_($expected);

        $this->assertSame($expected, $fixture->getVariableName());
    }

    /**
     * @covers ::__construct
     * @covers ::getType
     */
    public function test_has_type()
    {
        $expected = new String_;

        $fixture = new Var_('myVariable', $expected);

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

        $fixture = new Var_('1.0', null, $expected);

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
        $fixture = new Var_('myVariable', new String_, new Description('Description'));

        $this->assertSame('string $myVariable Description', (string) $fixture);
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Var_::<public>
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

        $fixture = Var_::create('string $myVariable My Description', $typeResolver, $descriptionFactory, $context);

        $this->assertSame('string $myVariable My Description', (string) $fixture);
        $this->assertSame('myVariable', $fixture->getVariableName());
        $this->assertInstanceOf(String_::class, $fixture->getType());
        $this->assertSame($description, $fixture->getDescription());
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Var_::<public>
     * @uses \phpDocumentor\Reflection\TypeResolver
     * @uses \phpDocumentor\Reflection\DocBlock\DescriptionFactory
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_empty_body_is_given()
    {
        $descriptionFactory = m::mock(DescriptionFactory::class);
        Var_::create('', new TypeResolver, $descriptionFactory);
    }

    /**
     * @covers ::create
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_body_is_not_string()
    {
        Var_::create([]);
    }

    /**
     * @covers ::create
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_method_fails_if_resolver_is_null()
    {
        Var_::create('body');
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
        Var_::create('body', new TypeResolver);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_exception_is_thrown_if_variable_name_is_not_string()
    {
        new Var_([]);
    }
}
