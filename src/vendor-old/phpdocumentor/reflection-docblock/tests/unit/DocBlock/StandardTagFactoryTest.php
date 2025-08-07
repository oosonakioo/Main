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
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;

/**
 * @coversDefaultClass phpDocumentor\Reflection\DocBlock\StandardTagFactory
 *
 * @covers ::<private>
 */
class StandardTagFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Generic
     * @uses phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses phpDocumentor\Reflection\DocBlock\Description
     */
    public function test_creating_a_generic_tag()
    {
        $expectedTagName = 'unknown-tag';
        $expectedDescriptionText = 'This is a description';
        $expectedDescription = new Description($expectedDescriptionText);
        $context = new Context('');

        $descriptionFactory = m::mock(DescriptionFactory::class);
        $descriptionFactory
            ->shouldReceive('create')
            ->once()
            ->with($expectedDescriptionText, $context)
            ->andReturn($expectedDescription);

        $tagFactory = new StandardTagFactory(m::mock(FqsenResolver::class));
        $tagFactory->addService($descriptionFactory, DescriptionFactory::class);

        /** @var Generic $tag */
        $tag = $tagFactory->create('@'.$expectedTagName.' This is a description', $context);

        $this->assertInstanceOf(Generic::class, $tag);
        $this->assertSame($expectedTagName, $tag->getName());
        $this->assertSame($expectedDescription, $tag->getDescription());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Author
     * @uses phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     */
    public function test_creating_a_specific_tag()
    {
        $context = new Context('');
        $tagFactory = new StandardTagFactory(m::mock(FqsenResolver::class));

        /** @var Author $tag */
        $tag = $tagFactory->create('@author Mike van Riel <me@mikevanriel.com>', $context);

        $this->assertInstanceOf(Author::class, $tag);
        $this->assertSame('author', $tag->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     * @uses phpDocumentor\Reflection\DocBlock\Tags\See
     * @uses phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     */
    public function test_an_empty_context_is_created_if_none_is_provided()
    {
        $fqsen = '\Tag';
        $resolver = m::mock(FqsenResolver::class)
            ->shouldReceive('resolve')
            ->with('Tag', m::type(Context::class))
            ->andReturn(new Fqsen($fqsen))
            ->getMock();
        $descriptionFactory = m::mock(DescriptionFactory::class);
        $descriptionFactory->shouldIgnoreMissing();

        $tagFactory = new StandardTagFactory($resolver);
        $tagFactory->addService($descriptionFactory, DescriptionFactory::class);

        /** @var See $tag */
        $tag = $tagFactory->create('@see Tag');

        $this->assertInstanceOf(See::class, $tag);
        $this->assertSame($fqsen, (string) $tag->getReference());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Author
     * @uses phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     */
    public function test_passing_your_own_set_of_tag_handlers()
    {
        $context = new Context('');
        $tagFactory = new StandardTagFactory(m::mock(FqsenResolver::class), ['user' => Author::class]);

        /** @var Author $tag */
        $tag = $tagFactory->create('@user Mike van Riel <me@mikevanriel.com>', $context);

        $this->assertInstanceOf(Author::class, $tag);
        $this->assertSame('author', $tag->getName());
    }

    /**
     * @covers ::create
     *
     * @uses                     phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses                     phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     *
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The tag "@user/myuser" does not seem to be wellformed, please check it for errors
     */
    public function test_exception_is_thrown_if_provided_tag_is_not_wellformed()
    {
        $this->markTestIncomplete(
            'For some reason this test fails; once I have access to a RegEx analyzer I will have to test the regex'
        );
        $tagFactory = new StandardTagFactory(m::mock(FqsenResolver::class));
        $tagFactory->create('@user[myuser');
    }

    /**
     * @covers ::__construct
     * @covers ::addParameter
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     */
    public function test_add_parameter_to_service_locator()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);
        $tagFactory->addParameter('myParam', 'myValue');

        $this->assertAttributeSame(
            [FqsenResolver::class => $resolver, 'myParam' => 'myValue'],
            'serviceLocator',
            $tagFactory
        );
    }

    /**
     * @covers ::addService
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     */
    public function test_add_service_to_service_locator()
    {
        $service = new PassthroughFormatter;

        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);
        $tagFactory->addService($service);

        $this->assertAttributeSame(
            [FqsenResolver::class => $resolver, PassthroughFormatter::class => $service],
            'serviceLocator',
            $tagFactory
        );
    }

    /**
     * @covers ::addService
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     */
    public function test_inject_concrete_service_for_interface_to_service_locator()
    {
        $interfaceName = Formatter::class;
        $service = new PassthroughFormatter;

        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);
        $tagFactory->addService($service, $interfaceName);

        $this->assertAttributeSame(
            [FqsenResolver::class => $resolver, $interfaceName => $service],
            'serviceLocator',
            $tagFactory
        );
    }

    /**
     * @covers ::registerTagHandler
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::create
     * @uses phpDocumentor\Reflection\DocBlock\Tags\Author
     */
    public function test_registering_a_handler_for_a_new_tag()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);

        $tagFactory->registerTagHandler('my-tag', Author::class);

        // Assert by trying to create one
        $tag = $tagFactory->create('@my-tag Mike van Riel <me@mikevanriel.com>');
        $this->assertInstanceOf(Author::class, $tag);
    }

    /**
     * @covers ::registerTagHandler
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_handler_registration_fails_if_provided_tag_name_is_not_a_string()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);

        $tagFactory->registerTagHandler([], Author::class);
    }

    /**
     * @covers ::registerTagHandler
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_handler_registration_fails_if_provided_tag_name_is_empty()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);

        $tagFactory->registerTagHandler('', Author::class);
    }

    /**
     * @covers ::registerTagHandler
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_handler_registration_fails_if_provided_tag_name_is_namespace_but_not_fully_qualified()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);

        $tagFactory->registerTagHandler('Name\Spaced\Tag', Author::class);
    }

    /**
     * @covers ::registerTagHandler
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_handler_registration_fails_if_provided_handler_is_not_a_string()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);

        $tagFactory->registerTagHandler('my-tag', []);
    }

    /**
     * @covers ::registerTagHandler
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_handler_registration_fails_if_provided_handler_is_empty()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);

        $tagFactory->registerTagHandler('my-tag', '');
    }

    /**
     * @covers ::registerTagHandler
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_handler_registration_fails_if_provided_handler_is_not_an_existing_class_name()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);

        $tagFactory->registerTagHandler('my-tag', 'IDoNotExist');
    }

    /**
     * @covers ::registerTagHandler
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_handler_registration_fails_if_provided_handler_does_not_implement_the_tag_interface()
    {
        $resolver = m::mock(FqsenResolver::class);
        $tagFactory = new StandardTagFactory($resolver);

        $tagFactory->registerTagHandler('my-tag', 'stdClass');
    }

    /**
     * @covers ::create
     *
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct
     * @uses phpDocumentor\Reflection\DocBlock\StandardTagFactory::addService
     * @uses phpDocumentor\Reflection\Docblock\Description
     * @uses phpDocumentor\Reflection\Docblock\Tags\Return_
     * @uses phpDocumentor\Reflection\Docblock\Tags\BaseTag
     */
    public function test_returntag_is_mapped_correctly()
    {
        $context = new Context('');

        $descriptionFactory = m::mock(DescriptionFactory::class);
        $descriptionFactory
            ->shouldReceive('create')
            ->once()
            ->with('', $context)
            ->andReturn(new Description(''));

        $typeResolver = new TypeResolver;

        $tagFactory = new StandardTagFactory(m::mock(FqsenResolver::class));
        $tagFactory->addService($descriptionFactory, DescriptionFactory::class);
        $tagFactory->addService($typeResolver, TypeResolver::class);

        /** @var Return_ $tag */
        $tag = $tagFactory->create('@return mixed', $context);

        $this->assertInstanceOf(Return_::class, $tag);
        $this->assertSame('return', $tag->getName());
    }
}
