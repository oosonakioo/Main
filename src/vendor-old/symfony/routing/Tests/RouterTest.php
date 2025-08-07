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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    private $router = null;

    private $loader = null;

    protected function setUp()
    {
        $this->loader = $this->getMock('Symfony\Component\Config\Loader\LoaderInterface');
        $this->router = new Router($this->loader, 'routing.yml');
    }

    public function test_set_options_with_supported_options()
    {
        $this->router->setOptions([
            'cache_dir' => './cache',
            'debug' => true,
            'resource_type' => 'ResourceType',
        ]);

        $this->assertSame('./cache', $this->router->getOption('cache_dir'));
        $this->assertTrue($this->router->getOption('debug'));
        $this->assertSame('ResourceType', $this->router->getOption('resource_type'));
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The Router does not support the following options: "option_foo", "option_bar"
     */
    public function test_set_options_with_unsupported_options()
    {
        $this->router->setOptions([
            'cache_dir' => './cache',
            'option_foo' => true,
            'option_bar' => 'baz',
            'resource_type' => 'ResourceType',
        ]);
    }

    public function test_set_option_with_supported_option()
    {
        $this->router->setOption('cache_dir', './cache');

        $this->assertSame('./cache', $this->router->getOption('cache_dir'));
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The Router does not support the "option_foo" option
     */
    public function test_set_option_with_unsupported_option()
    {
        $this->router->setOption('option_foo', true);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The Router does not support the "option_foo" option
     */
    public function test_get_option_with_unsupported_option()
    {
        $this->router->getOption('option_foo', true);
    }

    public function test_that_route_collection_is_loaded()
    {
        $this->router->setOption('resource_type', 'ResourceType');

        $routeCollection = $this->getMock('Symfony\Component\Routing\RouteCollection');

        $this->loader->expects($this->once())
            ->method('load')->with('routing.yml', 'ResourceType')
            ->will($this->returnValue($routeCollection));

        $this->assertSame($routeCollection, $this->router->getRouteCollection());
    }

    /**
     * @dataProvider provideMatcherOptionsPreventingCaching
     */
    public function test_matcher_is_created_if_cache_is_not_configured($option)
    {
        $this->router->setOption($option, null);

        $this->loader->expects($this->once())
            ->method('load')->with('routing.yml', null)
            ->will($this->returnValue($this->getMock('Symfony\Component\Routing\RouteCollection')));

        $this->assertInstanceOf('Symfony\\Component\\Routing\\Matcher\\UrlMatcher', $this->router->getMatcher());
    }

    public function provideMatcherOptionsPreventingCaching()
    {
        return [
            ['cache_dir'],
            ['matcher_cache_class'],
        ];
    }

    /**
     * @dataProvider provideGeneratorOptionsPreventingCaching
     */
    public function test_generator_is_created_if_cache_is_not_configured($option)
    {
        $this->router->setOption($option, null);

        $this->loader->expects($this->once())
            ->method('load')->with('routing.yml', null)
            ->will($this->returnValue($this->getMock('Symfony\Component\Routing\RouteCollection')));

        $this->assertInstanceOf('Symfony\\Component\\Routing\\Generator\\UrlGenerator', $this->router->getGenerator());
    }

    public function provideGeneratorOptionsPreventingCaching()
    {
        return [
            ['cache_dir'],
            ['generator_cache_class'],
        ];
    }

    public function test_match_request_with_url_matcher_interface()
    {
        $matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
        $matcher->expects($this->once())->method('match');

        $p = new \ReflectionProperty($this->router, 'matcher');
        $p->setAccessible(true);
        $p->setValue($this->router, $matcher);

        $this->router->matchRequest(Request::create('/'));
    }

    public function test_match_request_with_request_matcher_interface()
    {
        $matcher = $this->getMock('Symfony\Component\Routing\Matcher\RequestMatcherInterface');
        $matcher->expects($this->once())->method('matchRequest');

        $p = new \ReflectionProperty($this->router, 'matcher');
        $p->setAccessible(true);
        $p->setValue($this->router, $matcher);

        $this->router->matchRequest(Request::create('/'));
    }
}
