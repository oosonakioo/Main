<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests;

use Symfony\Component\Routing\Route;

class RouteCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCompileData
     */
    public function test_compile($name, $arguments, $prefix, $regex, $variables, $tokens)
    {
        $r = new \ReflectionClass('Symfony\\Component\\Routing\\Route');
        $route = $r->newInstanceArgs($arguments);

        $compiled = $route->compile();
        $this->assertEquals($prefix, $compiled->getStaticPrefix(), $name.' (static prefix)');
        $this->assertEquals($regex, $compiled->getRegex(), $name.' (regex)');
        $this->assertEquals($variables, $compiled->getVariables(), $name.' (variables)');
        $this->assertEquals($tokens, $compiled->getTokens(), $name.' (tokens)');
    }

    public function provideCompileData()
    {
        return [
            [
                'Static route',
                ['/foo'],
                '/foo', '#^/foo$#s', [], [
                    ['text', '/foo'],
                ],
            ],

            [
                'Route with a variable',
                ['/foo/{bar}'],
                '/foo', '#^/foo/(?P<bar>[^/]++)$#s', ['bar'], [
                    ['variable', '/', '[^/]++', 'bar'],
                    ['text', '/foo'],
                ],
            ],

            [
                'Route with a variable that has a default value',
                ['/foo/{bar}', ['bar' => 'bar']],
                '/foo', '#^/foo(?:/(?P<bar>[^/]++))?$#s', ['bar'], [
                    ['variable', '/', '[^/]++', 'bar'],
                    ['text', '/foo'],
                ],
            ],

            [
                'Route with several variables',
                ['/foo/{bar}/{foobar}'],
                '/foo', '#^/foo/(?P<bar>[^/]++)/(?P<foobar>[^/]++)$#s', ['bar', 'foobar'], [
                    ['variable', '/', '[^/]++', 'foobar'],
                    ['variable', '/', '[^/]++', 'bar'],
                    ['text', '/foo'],
                ],
            ],

            [
                'Route with several variables that have default values',
                ['/foo/{bar}/{foobar}', ['bar' => 'bar', 'foobar' => '']],
                '/foo', '#^/foo(?:/(?P<bar>[^/]++)(?:/(?P<foobar>[^/]++))?)?$#s', ['bar', 'foobar'], [
                    ['variable', '/', '[^/]++', 'foobar'],
                    ['variable', '/', '[^/]++', 'bar'],
                    ['text', '/foo'],
                ],
            ],

            [
                'Route with several variables but some of them have no default values',
                ['/foo/{bar}/{foobar}', ['bar' => 'bar']],
                '/foo', '#^/foo/(?P<bar>[^/]++)/(?P<foobar>[^/]++)$#s', ['bar', 'foobar'], [
                    ['variable', '/', '[^/]++', 'foobar'],
                    ['variable', '/', '[^/]++', 'bar'],
                    ['text', '/foo'],
                ],
            ],

            [
                'Route with an optional variable as the first segment',
                ['/{bar}', ['bar' => 'bar']],
                '', '#^/(?P<bar>[^/]++)?$#s', ['bar'], [
                    ['variable', '/', '[^/]++', 'bar'],
                ],
            ],

            [
                'Route with a requirement of 0',
                ['/{bar}', ['bar' => null], ['bar' => '0']],
                '', '#^/(?P<bar>0)?$#s', ['bar'], [
                    ['variable', '/', '0', 'bar'],
                ],
            ],

            [
                'Route with an optional variable as the first segment with requirements',
                ['/{bar}', ['bar' => 'bar'], ['bar' => '(foo|bar)']],
                '', '#^/(?P<bar>(foo|bar))?$#s', ['bar'], [
                    ['variable', '/', '(foo|bar)', 'bar'],
                ],
            ],

            [
                'Route with only optional variables',
                ['/{foo}/{bar}', ['foo' => 'foo', 'bar' => 'bar']],
                '', '#^/(?P<foo>[^/]++)?(?:/(?P<bar>[^/]++))?$#s', ['foo', 'bar'], [
                    ['variable', '/', '[^/]++', 'bar'],
                    ['variable', '/', '[^/]++', 'foo'],
                ],
            ],

            [
                'Route with a variable in last position',
                ['/foo-{bar}'],
                '/foo', '#^/foo\-(?P<bar>[^/]++)$#s', ['bar'], [
                    ['variable', '-', '[^/]++', 'bar'],
                    ['text', '/foo'],
                ],
            ],

            [
                'Route with nested placeholders',
                ['/{static{var}static}'],
                '/{static', '#^/\{static(?P<var>[^/]+)static\}$#s', ['var'], [
                    ['text', 'static}'],
                    ['variable', '', '[^/]+', 'var'],
                    ['text', '/{static'],
                ],
            ],

            [
                'Route without separator between variables',
                ['/{w}{x}{y}{z}.{_format}', ['z' => 'default-z', '_format' => 'html'], ['y' => '(y|Y)']],
                '', '#^/(?P<w>[^/\.]+)(?P<x>[^/\.]+)(?P<y>(y|Y))(?:(?P<z>[^/\.]++)(?:\.(?P<_format>[^/]++))?)?$#s', ['w', 'x', 'y', 'z', '_format'], [
                    ['variable', '.', '[^/]++', '_format'],
                    ['variable', '', '[^/\.]++', 'z'],
                    ['variable', '', '(y|Y)', 'y'],
                    ['variable', '', '[^/\.]+', 'x'],
                    ['variable', '/', '[^/\.]+', 'w'],
                ],
            ],

            [
                'Route with a format',
                ['/foo/{bar}.{_format}'],
                '/foo', '#^/foo/(?P<bar>[^/\.]++)\.(?P<_format>[^/]++)$#s', ['bar', '_format'], [
                    ['variable', '.', '[^/]++', '_format'],
                    ['variable', '/', '[^/\.]++', 'bar'],
                    ['text', '/foo'],
                ],
            ],
        ];
    }

    /**
     * @expectedException \LogicException
     */
    public function test_route_with_same_variable_twice()
    {
        $route = new Route('/{name}/{name}');

        $compiled = $route->compile();
    }

    /**
     * @dataProvider getNumericVariableNames
     *
     * @expectedException \DomainException
     */
    public function test_route_with_numeric_variable_name($name)
    {
        $route = new Route('/{'.$name.'}');
        $route->compile();
    }

    public function getNumericVariableNames()
    {
        return [
            ['09'],
            ['123'],
            ['1e2'],
        ];
    }

    /**
     * @dataProvider provideCompileWithHostData
     */
    public function test_compile_with_host($name, $arguments, $prefix, $regex, $variables, $pathVariables, $tokens, $hostRegex, $hostVariables, $hostTokens)
    {
        $r = new \ReflectionClass('Symfony\\Component\\Routing\\Route');
        $route = $r->newInstanceArgs($arguments);

        $compiled = $route->compile();
        $this->assertEquals($prefix, $compiled->getStaticPrefix(), $name.' (static prefix)');
        $this->assertEquals($regex, str_replace(["\n", ' '], '', $compiled->getRegex()), $name.' (regex)');
        $this->assertEquals($variables, $compiled->getVariables(), $name.' (variables)');
        $this->assertEquals($pathVariables, $compiled->getPathVariables(), $name.' (path variables)');
        $this->assertEquals($tokens, $compiled->getTokens(), $name.' (tokens)');
        $this->assertEquals($hostRegex, str_replace(["\n", ' '], '', $compiled->getHostRegex()), $name.' (host regex)');
        $this->assertEquals($hostVariables, $compiled->getHostVariables(), $name.' (host variables)');
        $this->assertEquals($hostTokens, $compiled->getHostTokens(), $name.' (host tokens)');
    }

    public function provideCompileWithHostData()
    {
        return [
            [
                'Route with host pattern',
                ['/hello', [], [], [], 'www.example.com'],
                '/hello', '#^/hello$#s', [], [], [
                    ['text', '/hello'],
                ],
                '#^www\.example\.com$#si', [], [
                    ['text', 'www.example.com'],
                ],
            ],
            [
                'Route with host pattern and some variables',
                ['/hello/{name}', [], [], [], 'www.example.{tld}'],
                '/hello', '#^/hello/(?P<name>[^/]++)$#s', ['tld', 'name'], ['name'], [
                    ['variable', '/', '[^/]++', 'name'],
                    ['text', '/hello'],
                ],
                '#^www\.example\.(?P<tld>[^\.]++)$#si', ['tld'], [
                    ['variable', '.', '[^\.]++', 'tld'],
                    ['text', 'www.example'],
                ],
            ],
            [
                'Route with variable at beginning of host',
                ['/hello', [], [], [], '{locale}.example.{tld}'],
                '/hello', '#^/hello$#s', ['locale', 'tld'], [], [
                    ['text', '/hello'],
                ],
                '#^(?P<locale>[^\.]++)\.example\.(?P<tld>[^\.]++)$#si', ['locale', 'tld'], [
                    ['variable', '.', '[^\.]++', 'tld'],
                    ['text', '.example'],
                    ['variable', '', '[^\.]++', 'locale'],
                ],
            ],
            [
                'Route with host variables that has a default value',
                ['/hello', ['locale' => 'a', 'tld' => 'b'], [], [], '{locale}.example.{tld}'],
                '/hello', '#^/hello$#s', ['locale', 'tld'], [], [
                    ['text', '/hello'],
                ],
                '#^(?P<locale>[^\.]++)\.example\.(?P<tld>[^\.]++)$#si', ['locale', 'tld'], [
                    ['variable', '.', '[^\.]++', 'tld'],
                    ['text', '.example'],
                    ['variable', '', '[^\.]++', 'locale'],
                ],
            ],
        ];
    }
}
