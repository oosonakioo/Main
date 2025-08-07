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

/**
 * @coversDefaultClass \phpDocumentor\Reflection\DocBlock\Tags\Author
 *
 * @covers ::<private>
 */
class AuthorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Author::__construct
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_correct_tag_name_is_returned()
    {
        $fixture = new Author('Mike van Riel', 'mike@phpdoc.org');

        $this->assertSame('author', $fixture->getName());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Author::__construct
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Author::__toString
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::getName
     */
    public function test_if_tag_can_be_rendered_using_default_formatter()
    {
        $fixture = new Author('Mike van Riel', 'mike@phpdoc.org');

        $this->assertSame('@author Mike van Riel<mike@phpdoc.org>', $fixture->render());
    }

    /**
     * @uses   \phpDocumentor\Reflection\DocBlock\Tags\Author::__construct
     *
     * @covers \phpDocumentor\Reflection\DocBlock\Tags\BaseTag::render
     */
    public function test_if_tag_can_be_rendered_using_specific_formatter()
    {
        $fixture = new Author('Mike van Riel', 'mike@phpdoc.org');

        $formatter = m::mock(Formatter::class);
        $formatter->shouldReceive('format')->with($fixture)->andReturn('Rendered output');

        $this->assertSame('Rendered output', $fixture->render($formatter));
    }

    /**
     * @covers ::__construct
     * @covers ::getAuthorName
     */
    public function test_has_the_author_name()
    {
        $expected = 'Mike van Riel';

        $fixture = new Author($expected, 'mike@phpdoc.org');

        $this->assertSame($expected, $fixture->getAuthorName());
    }

    /**
     * @covers ::__construct
     * @covers ::getAuthorName
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_initialization_fails_if_author_name_is_not_a_string()
    {
        new Author([], 'mike@phpdoc.org');
    }

    /**
     * @covers ::__construct
     * @covers ::getEmail
     */
    public function test_has_the_author_mail_address()
    {
        $expected = 'mike@phpdoc.org';

        $fixture = new Author('Mike van Riel', $expected);

        $this->assertSame($expected, $fixture->getEmail());
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_initialization_fails_if_email_is_not_a_string()
    {
        new Author('Mike van Riel', []);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_initialization_fails_if_email_is_not_valid()
    {
        new Author('Mike van Riel', 'mike');
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function test_string_representation_is_returned()
    {
        $fixture = new Author('Mike van Riel', 'mike@phpdoc.org');

        $this->assertSame('Mike van Riel<mike@phpdoc.org>', (string) $fixture);
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Author::<public>
     */
    public function test_factory_method()
    {
        $fixture = Author::create('Mike van Riel <mike@phpdoc.org>');

        $this->assertSame('Mike van Riel<mike@phpdoc.org>', (string) $fixture);
        $this->assertSame('Mike van Riel', $fixture->getAuthorName());
        $this->assertSame('mike@phpdoc.org', $fixture->getEmail());
    }

    /**
     * @covers ::create
     *
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Author::<public>
     */
    public function test_factory_method_returns_null_if_it_could_not_read_body()
    {
        $this->assertNull(Author::create('dfgr<'));
    }
}
