<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Generator;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function test_absolute_url_with_port80()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertEquals('http://localhost/app.php/testing', $url);
    }

    public function test_absolute_secure_url_with_port443()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes, ['scheme' => 'https'])->generate('test', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertEquals('https://localhost/app.php/testing', $url);
    }

    public function test_absolute_url_with_non_standard_port()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes, ['httpPort' => 8080])->generate('test', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertEquals('http://localhost:8080/app.php/testing', $url);
    }

    public function test_absolute_secure_url_with_non_standard_port()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes, ['httpsPort' => 8080, 'scheme' => 'https'])->generate('test', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertEquals('https://localhost:8080/app.php/testing', $url);
    }

    public function test_relative_url_without_parameters()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', [], UrlGeneratorInterface::ABSOLUTE_PATH);

        $this->assertEquals('/app.php/testing', $url);
    }

    public function test_relative_url_with_parameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}'));
        $url = $this->getGenerator($routes)->generate('test', ['foo' => 'bar'], UrlGeneratorInterface::ABSOLUTE_PATH);

        $this->assertEquals('/app.php/testing/bar', $url);
    }

    public function test_relative_url_with_null_parameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing.{format}', ['format' => null]));
        $url = $this->getGenerator($routes)->generate('test', [], UrlGeneratorInterface::ABSOLUTE_PATH);

        $this->assertEquals('/app.php/testing', $url);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_relative_url_with_null_parameter_but_not_optional()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}/bar', ['foo' => null]));
        // This must raise an exception because the default requirement for "foo" is "[^/]+" which is not met with these params.
        // Generating path "/testing//bar" would be wrong as matching this route would fail.
        $this->getGenerator($routes)->generate('test', [], UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function test_relative_url_with_optional_zero_parameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{page}'));
        $url = $this->getGenerator($routes)->generate('test', ['page' => 0], UrlGeneratorInterface::ABSOLUTE_PATH);

        $this->assertEquals('/app.php/testing/0', $url);
    }

    public function test_not_passed_optional_parameter_in_between()
    {
        $routes = $this->getRoutes('test', new Route('/{slug}/{page}', ['slug' => 'index', 'page' => 0]));
        $this->assertSame('/app.php/index/1', $this->getGenerator($routes)->generate('test', ['page' => 1]));
        $this->assertSame('/app.php/', $this->getGenerator($routes)->generate('test'));
    }

    public function test_relative_url_with_extra_parameters()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', ['foo' => 'bar'], UrlGeneratorInterface::ABSOLUTE_PATH);

        $this->assertEquals('/app.php/testing?foo=bar', $url);
    }

    public function test_absolute_url_with_extra_parameters()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', ['foo' => 'bar'], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertEquals('http://localhost/app.php/testing?foo=bar', $url);
    }

    public function test_url_with_null_extra_parameters()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', ['foo' => null], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertEquals('http://localhost/app.php/testing', $url);
    }

    public function test_url_with_extra_parameters_from_globals()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $generator = $this->getGenerator($routes);
        $context = new RequestContext('/app.php');
        $context->setParameter('bar', 'bar');
        $generator->setContext($context);
        $url = $generator->generate('test', ['foo' => 'bar']);

        $this->assertEquals('/app.php/testing?foo=bar', $url);
    }

    public function test_url_with_global_parameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}'));
        $generator = $this->getGenerator($routes);
        $context = new RequestContext('/app.php');
        $context->setParameter('foo', 'bar');
        $generator->setContext($context);
        $url = $generator->generate('test', []);

        $this->assertEquals('/app.php/testing/bar', $url);
    }

    public function test_global_parameter_has_higher_priority_than_default()
    {
        $routes = $this->getRoutes('test', new Route('/{_locale}', ['_locale' => 'en']));
        $generator = $this->getGenerator($routes);
        $context = new RequestContext('/app.php');
        $context->setParameter('_locale', 'de');
        $generator->setContext($context);
        $url = $generator->generate('test', []);

        $this->assertSame('/app.php/de', $url);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function test_generate_without_routes()
    {
        $routes = $this->getRoutes('foo', new Route('/testing/{foo}'));
        $this->getGenerator($routes)->generate('test', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     */
    public function test_generate_for_route_without_mandatory_parameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}'));
        $this->getGenerator($routes)->generate('test', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_generate_for_route_with_invalid_optional_parameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', ['foo' => '1'], ['foo' => 'd+']));
        $this->getGenerator($routes)->generate('test', ['foo' => 'bar'], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_generate_for_route_with_invalid_parameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', [], ['foo' => '1|2']));
        $this->getGenerator($routes)->generate('test', ['foo' => '0'], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function test_generate_for_route_with_invalid_optional_parameter_non_strict()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', ['foo' => '1'], ['foo' => 'd+']));
        $generator = $this->getGenerator($routes);
        $generator->setStrictRequirements(false);
        $this->assertNull($generator->generate('test', ['foo' => 'bar'], UrlGeneratorInterface::ABSOLUTE_URL));
    }

    public function test_generate_for_route_with_invalid_optional_parameter_non_strict_with_logger()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', ['foo' => '1'], ['foo' => 'd+']));
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())
            ->method('error');
        $generator = $this->getGenerator($routes, [], $logger);
        $generator->setStrictRequirements(false);
        $this->assertNull($generator->generate('test', ['foo' => 'bar'], UrlGeneratorInterface::ABSOLUTE_URL));
    }

    public function test_generate_for_route_with_invalid_parameter_but_disabled_requirements_check()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', ['foo' => '1'], ['foo' => 'd+']));
        $generator = $this->getGenerator($routes);
        $generator->setStrictRequirements(null);
        $this->assertSame('/app.php/testing/bar', $generator->generate('test', ['foo' => 'bar']));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_generate_for_route_with_invalid_mandatory_parameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', [], ['foo' => 'd+']));
        $this->getGenerator($routes)->generate('test', ['foo' => 'bar'], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_required_param_and_empty_passed()
    {
        $routes = $this->getRoutes('test', new Route('/{slug}', [], ['slug' => '.+']));
        $this->getGenerator($routes)->generate('test', ['slug' => '']);
    }

    public function test_scheme_requirement_does_nothing_if_same_current_scheme()
    {
        $routes = $this->getRoutes('test', new Route('/', [], [], [], '', ['http']));
        $this->assertEquals('/app.php/', $this->getGenerator($routes)->generate('test'));

        $routes = $this->getRoutes('test', new Route('/', [], [], [], '', ['https']));
        $this->assertEquals('/app.php/', $this->getGenerator($routes, ['scheme' => 'https'])->generate('test'));
    }

    public function test_scheme_requirement_forces_absolute_url()
    {
        $routes = $this->getRoutes('test', new Route('/', [], [], [], '', ['https']));
        $this->assertEquals('https://localhost/app.php/', $this->getGenerator($routes)->generate('test'));

        $routes = $this->getRoutes('test', new Route('/', [], [], [], '', ['http']));
        $this->assertEquals('http://localhost/app.php/', $this->getGenerator($routes, ['scheme' => 'https'])->generate('test'));
    }

    public function test_scheme_requirement_creates_url_for_first_required_scheme()
    {
        $routes = $this->getRoutes('test', new Route('/', [], [], [], '', ['Ftp', 'https']));
        $this->assertEquals('ftp://localhost/app.php/', $this->getGenerator($routes)->generate('test'));
    }

    public function test_path_with_two_starting_slashes()
    {
        $routes = $this->getRoutes('test', new Route('//path-and-not-domain'));

        // this must not generate '//path-and-not-domain' because that would be a network path
        $this->assertSame('/path-and-not-domain', $this->getGenerator($routes, ['BaseUrl' => ''])->generate('test'));
    }

    public function test_no_trailing_slash_for_multiple_optional_parameters()
    {
        $routes = $this->getRoutes('test', new Route('/category/{slug1}/{slug2}/{slug3}', ['slug2' => null, 'slug3' => null]));

        $this->assertEquals('/app.php/category/foo', $this->getGenerator($routes)->generate('test', ['slug1' => 'foo']));
    }

    public function test_with_an_integer_as_a_default_value()
    {
        $routes = $this->getRoutes('test', new Route('/{default}', ['default' => 0]));

        $this->assertEquals('/app.php/foo', $this->getGenerator($routes)->generate('test', ['default' => 'foo']));
    }

    public function test_null_for_optional_parameter_is_ignored()
    {
        $routes = $this->getRoutes('test', new Route('/test/{default}', ['default' => 0]));

        $this->assertEquals('/app.php/test', $this->getGenerator($routes)->generate('test', ['default' => null]));
    }

    public function test_query_param_same_as_default()
    {
        $routes = $this->getRoutes('test', new Route('/test', ['page' => 1]));

        $this->assertSame('/app.php/test?page=2', $this->getGenerator($routes)->generate('test', ['page' => 2]));
        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test', ['page' => 1]));
        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test', ['page' => '1']));
        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test'));
    }

    public function test_array_query_param_same_as_default()
    {
        $routes = $this->getRoutes('test', new Route('/test', ['array' => ['foo', 'bar']]));

        $this->assertSame('/app.php/test?array%5B0%5D=bar&array%5B1%5D=foo', $this->getGenerator($routes)->generate('test', ['array' => ['bar', 'foo']]));
        $this->assertSame('/app.php/test?array%5Ba%5D=foo&array%5Bb%5D=bar', $this->getGenerator($routes)->generate('test', ['array' => ['a' => 'foo', 'b' => 'bar']]));
        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test', ['array' => ['foo', 'bar']]));
        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test', ['array' => [1 => 'bar', 0 => 'foo']]));
        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test'));
    }

    public function test_generate_with_special_route_name()
    {
        $routes = $this->getRoutes('$péß^a|', new Route('/bar'));

        $this->assertSame('/app.php/bar', $this->getGenerator($routes)->generate('$péß^a|'));
    }

    public function test_url_encoding()
    {
        // This tests the encoding of reserved characters that are used for delimiting of URI components (defined in RFC 3986)
        // and other special ASCII chars. These chars are tested as static text path, variable path and query param.
        $chars = '@:[]/()*\'" +,;-._~&$<>|{}%\\^`!?foo=bar#id';
        $routes = $this->getRoutes('test', new Route("/$chars/{varpath}", [], ['varpath' => '.+']));
        $this->assertSame('/app.php/@:%5B%5D/%28%29*%27%22%20+,;-._~%26%24%3C%3E|%7B%7D%25%5C%5E%60!%3Ffoo=bar%23id'
           .'/@:%5B%5D/%28%29*%27%22%20+,;-._~%26%24%3C%3E|%7B%7D%25%5C%5E%60!%3Ffoo=bar%23id'
           .'?query=%40%3A%5B%5D/%28%29%2A%27%22+%2B%2C%3B-._%7E%26%24%3C%3E%7C%7B%7D%25%5C%5E%60%21%3Ffoo%3Dbar%23id',
            $this->getGenerator($routes)->generate('test', [
                'varpath' => $chars,
                'query' => $chars,
            ])
        );
    }

    public function test_encoding_of_relative_path_segments()
    {
        $routes = $this->getRoutes('test', new Route('/dir/../dir/..'));
        $this->assertSame('/app.php/dir/%2E%2E/dir/%2E%2E', $this->getGenerator($routes)->generate('test'));
        $routes = $this->getRoutes('test', new Route('/dir/./dir/.'));
        $this->assertSame('/app.php/dir/%2E/dir/%2E', $this->getGenerator($routes)->generate('test'));
        $routes = $this->getRoutes('test', new Route('/a./.a/a../..a/...'));
        $this->assertSame('/app.php/a./.a/a../..a/...', $this->getGenerator($routes)->generate('test'));
    }

    public function test_adjacent_variables()
    {
        $routes = $this->getRoutes('test', new Route('/{x}{y}{z}.{_format}', ['z' => 'default-z', '_format' => 'html'], ['y' => '\d+']));
        $generator = $this->getGenerator($routes);
        $this->assertSame('/app.php/foo123', $generator->generate('test', ['x' => 'foo', 'y' => '123']));
        $this->assertSame('/app.php/foo123bar.xml', $generator->generate('test', ['x' => 'foo', 'y' => '123', 'z' => 'bar', '_format' => 'xml']));

        // The default requirement for 'x' should not allow the separator '.' in this case because it would otherwise match everything
        // and following optional variables like _format could never match.
        $this->setExpectedException('Symfony\Component\Routing\Exception\InvalidParameterException');
        $generator->generate('test', ['x' => 'do.t', 'y' => '123', 'z' => 'bar', '_format' => 'xml']);
    }

    public function test_optional_variable_with_no_real_separator()
    {
        $routes = $this->getRoutes('test', new Route('/get{what}', ['what' => 'All']));
        $generator = $this->getGenerator($routes);

        $this->assertSame('/app.php/get', $generator->generate('test'));
        $this->assertSame('/app.php/getSites', $generator->generate('test', ['what' => 'Sites']));
    }

    public function test_required_variable_with_no_real_separator()
    {
        $routes = $this->getRoutes('test', new Route('/get{what}Suffix'));
        $generator = $this->getGenerator($routes);

        $this->assertSame('/app.php/getSitesSuffix', $generator->generate('test', ['what' => 'Sites']));
    }

    public function test_default_requirement_of_variable()
    {
        $routes = $this->getRoutes('test', new Route('/{page}.{_format}'));
        $generator = $this->getGenerator($routes);

        $this->assertSame('/app.php/index.mobile.html', $generator->generate('test', ['page' => 'index', '_format' => 'mobile.html']));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_default_requirement_of_variable_disallows_slash()
    {
        $routes = $this->getRoutes('test', new Route('/{page}.{_format}'));
        $this->getGenerator($routes)->generate('test', ['page' => 'index', '_format' => 'sl/ash']);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_default_requirement_of_variable_disallows_next_separator()
    {
        $routes = $this->getRoutes('test', new Route('/{page}.{_format}'));
        $this->getGenerator($routes)->generate('test', ['page' => 'do.t', '_format' => 'html']);
    }

    public function test_with_host_different_from_context()
    {
        $routes = $this->getRoutes('test', new Route('/{name}', [], [], [], '{locale}.example.com'));

        $this->assertEquals('//fr.example.com/app.php/Fabien', $this->getGenerator($routes)->generate('test', ['name' => 'Fabien', 'locale' => 'fr']));
    }

    public function test_with_host_same_as_context()
    {
        $routes = $this->getRoutes('test', new Route('/{name}', [], [], [], '{locale}.example.com'));

        $this->assertEquals('/app.php/Fabien', $this->getGenerator($routes, ['host' => 'fr.example.com'])->generate('test', ['name' => 'Fabien', 'locale' => 'fr']));
    }

    public function test_with_host_same_as_context_and_absolute()
    {
        $routes = $this->getRoutes('test', new Route('/{name}', [], [], [], '{locale}.example.com'));

        $this->assertEquals('http://fr.example.com/app.php/Fabien', $this->getGenerator($routes, ['host' => 'fr.example.com'])->generate('test', ['name' => 'Fabien', 'locale' => 'fr'], UrlGeneratorInterface::ABSOLUTE_URL));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_url_with_invalid_parameter_in_host()
    {
        $routes = $this->getRoutes('test', new Route('/', [], ['foo' => 'bar'], [], '{foo}.example.com'));
        $this->getGenerator($routes)->generate('test', ['foo' => 'baz'], UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_url_with_invalid_parameter_in_host_when_param_has_a_default_value()
    {
        $routes = $this->getRoutes('test', new Route('/', ['foo' => 'bar'], ['foo' => 'bar'], [], '{foo}.example.com'));
        $this->getGenerator($routes)->generate('test', ['foo' => 'baz'], UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function test_url_with_invalid_parameter_equals_default_value_in_host()
    {
        $routes = $this->getRoutes('test', new Route('/', ['foo' => 'baz'], ['foo' => 'bar'], [], '{foo}.example.com'));
        $this->getGenerator($routes)->generate('test', ['foo' => 'baz'], UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function test_url_with_invalid_parameter_in_host_in_non_strict_mode()
    {
        $routes = $this->getRoutes('test', new Route('/', [], ['foo' => 'bar'], [], '{foo}.example.com'));
        $generator = $this->getGenerator($routes);
        $generator->setStrictRequirements(false);
        $this->assertNull($generator->generate('test', ['foo' => 'baz'], UrlGeneratorInterface::ABSOLUTE_PATH));
    }

    public function test_host_is_case_insensitive()
    {
        $routes = $this->getRoutes('test', new Route('/', [], ['locale' => 'en|de|fr'], [], '{locale}.FooBar.com'));
        $generator = $this->getGenerator($routes);
        $this->assertSame('//EN.FooBar.com/app.php/', $generator->generate('test', ['locale' => 'EN'], UrlGeneratorInterface::NETWORK_PATH));
    }

    public function test_generate_network_path()
    {
        $routes = $this->getRoutes('test', new Route('/{name}', [], [], [], '{locale}.example.com', ['http']));

        $this->assertSame('//fr.example.com/app.php/Fabien', $this->getGenerator($routes)->generate('test',
            ['name' => 'Fabien', 'locale' => 'fr'], UrlGeneratorInterface::NETWORK_PATH), 'network path with different host'
        );
        $this->assertSame('//fr.example.com/app.php/Fabien?query=string', $this->getGenerator($routes, ['host' => 'fr.example.com'])->generate('test',
            ['name' => 'Fabien', 'locale' => 'fr', 'query' => 'string'], UrlGeneratorInterface::NETWORK_PATH), 'network path although host same as context'
        );
        $this->assertSame('http://fr.example.com/app.php/Fabien', $this->getGenerator($routes, ['scheme' => 'https'])->generate('test',
            ['name' => 'Fabien', 'locale' => 'fr'], UrlGeneratorInterface::NETWORK_PATH), 'absolute URL because scheme requirement does not match context'
        );
        $this->assertSame('http://fr.example.com/app.php/Fabien', $this->getGenerator($routes)->generate('test',
            ['name' => 'Fabien', 'locale' => 'fr'], UrlGeneratorInterface::ABSOLUTE_URL), 'absolute URL with same scheme because it is requested'
        );
    }

    public function test_generate_relative_path()
    {
        $routes = new RouteCollection;
        $routes->add('article', new Route('/{author}/{article}/'));
        $routes->add('comments', new Route('/{author}/{article}/comments'));
        $routes->add('host', new Route('/{article}', [], [], [], '{author}.example.com'));
        $routes->add('scheme', new Route('/{author}/blog', [], [], [], '', ['https']));
        $routes->add('unrelated', new Route('/about'));

        $generator = $this->getGenerator($routes, ['host' => 'example.com', 'pathInfo' => '/fabien/symfony-is-great/']);

        $this->assertSame('comments', $generator->generate('comments',
            ['author' => 'fabien', 'article' => 'symfony-is-great'], UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('comments?page=2', $generator->generate('comments',
            ['author' => 'fabien', 'article' => 'symfony-is-great', 'page' => 2], UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('../twig-is-great/', $generator->generate('article',
            ['author' => 'fabien', 'article' => 'twig-is-great'], UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('../../bernhard/forms-are-great/', $generator->generate('article',
            ['author' => 'bernhard', 'article' => 'forms-are-great'], UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('//bernhard.example.com/app.php/forms-are-great', $generator->generate('host',
            ['author' => 'bernhard', 'article' => 'forms-are-great'], UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('https://example.com/app.php/bernhard/blog', $generator->generate('scheme',
            ['author' => 'bernhard'], UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('../../about', $generator->generate('unrelated',
            [], UrlGeneratorInterface::RELATIVE_PATH)
        );
    }

    /**
     * @dataProvider provideRelativePaths
     */
    public function test_get_relative_path($sourcePath, $targetPath, $expectedPath)
    {
        $this->assertSame($expectedPath, UrlGenerator::getRelativePath($sourcePath, $targetPath));
    }

    public function provideRelativePaths()
    {
        return [
            [
                '/same/dir/',
                '/same/dir/',
                '',
            ],
            [
                '/same/file',
                '/same/file',
                '',
            ],
            [
                '/',
                '/file',
                'file',
            ],
            [
                '/',
                '/dir/file',
                'dir/file',
            ],
            [
                '/dir/file.html',
                '/dir/different-file.html',
                'different-file.html',
            ],
            [
                '/same/dir/extra-file',
                '/same/dir/',
                './',
            ],
            [
                '/parent/dir/',
                '/parent/',
                '../',
            ],
            [
                '/parent/dir/extra-file',
                '/parent/',
                '../',
            ],
            [
                '/a/b/',
                '/x/y/z/',
                '../../x/y/z/',
            ],
            [
                '/a/b/c/d/e',
                '/a/c/d',
                '../../../c/d',
            ],
            [
                '/a/b/c//',
                '/a/b/c/',
                '../',
            ],
            [
                '/a/b/c/',
                '/a/b/c//',
                './/',
            ],
            [
                '/root/a/b/c/',
                '/root/x/b/c/',
                '../../../x/b/c/',
            ],
            [
                '/a/b/c/d/',
                '/a',
                '../../../../a',
            ],
            [
                '/special-chars/sp%20ce/1€/mäh/e=mc²',
                '/special-chars/sp%20ce/1€/<µ>/e=mc²',
                '../<µ>/e=mc²',
            ],
            [
                'not-rooted',
                'dir/file',
                'dir/file',
            ],
            [
                '//dir/',
                '',
                '../../',
            ],
            [
                '/dir/',
                '/dir/file:with-colon',
                './file:with-colon',
            ],
            [
                '/dir/',
                '/dir/subdir/file:with-colon',
                'subdir/file:with-colon',
            ],
            [
                '/dir/',
                '/dir/:subdir/',
                './:subdir/',
            ],
        ];
    }

    protected function getGenerator(RouteCollection $routes, array $parameters = [], $logger = null)
    {
        $context = new RequestContext('/app.php');
        foreach ($parameters as $key => $value) {
            $method = 'set'.$key;
            $context->$method($value);
        }

        return new UrlGenerator($routes, $context, $logger);
    }

    protected function getRoutes($name, Route $route)
    {
        $routes = new RouteCollection;
        $routes->add($name, $route);

        return $routes;
    }
}
