<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Loader;

use Symfony\Component\Routing\Annotation\Route;

class AnnotationClassLoaderTest extends AbstractAnnotationLoaderTest
{
    protected $loader;

    private $reader;

    protected function setUp()
    {
        parent::setUp();

        $this->reader = $this->getReader();
        $this->loader = $this->getClassLoader($this->reader);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_load_missing_class()
    {
        $this->loader->load('MissingClass');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_load_abstract_class()
    {
        $this->loader->load('Symfony\Component\Routing\Tests\Fixtures\AnnotatedClasses\AbstractClass');
    }

    /**
     * @dataProvider provideTestSupportsChecksResource
     */
    public function test_supports_checks_resource($resource, $expectedSupports)
    {
        $this->assertSame($expectedSupports, $this->loader->supports($resource), '->supports() returns true if the resource is loadable');
    }

    public function provideTestSupportsChecksResource()
    {
        return [
            ['class', true],
            ['\fully\qualified\class\name', true],
            ['namespaced\class\without\leading\slash', true],
            ['ÿClassWithLegalSpecialCharacters', true],
            ['5', false],
            ['foo.foo', false],
            [null, false],
        ];
    }

    public function test_supports_checks_type_if_specified()
    {
        $this->assertTrue($this->loader->supports('class', 'annotation'), '->supports() checks the resource type if specified');
        $this->assertFalse($this->loader->supports('class', 'foo'), '->supports() checks the resource type if specified');
    }

    public function getLoadTests()
    {
        return [
            [
                'Symfony\Component\Routing\Tests\Fixtures\AnnotatedClasses\BarClass',
                ['name' => 'route1', 'path' => '/path'],
                ['arg2' => 'defaultValue2', 'arg3' => 'defaultValue3'],
            ],
            [
                'Symfony\Component\Routing\Tests\Fixtures\AnnotatedClasses\BarClass',
                ['defaults' => ['arg2' => 'foo'], 'requirements' => ['arg3' => '\w+']],
                ['arg2' => 'defaultValue2', 'arg3' => 'defaultValue3'],
            ],
            [
                'Symfony\Component\Routing\Tests\Fixtures\AnnotatedClasses\BarClass',
                ['options' => ['foo' => 'bar']],
                ['arg2' => 'defaultValue2', 'arg3' => 'defaultValue3'],
            ],
            [
                'Symfony\Component\Routing\Tests\Fixtures\AnnotatedClasses\BarClass',
                ['schemes' => ['https'], 'methods' => ['GET']],
                ['arg2' => 'defaultValue2', 'arg3' => 'defaultValue3'],
            ],
            [
                'Symfony\Component\Routing\Tests\Fixtures\AnnotatedClasses\BarClass',
                ['condition' => 'context.getMethod() == "GET"'],
                ['arg2' => 'defaultValue2', 'arg3' => 'defaultValue3'],
            ],
        ];
    }

    /**
     * @dataProvider getLoadTests
     */
    public function test_load($className, $routeData = [], $methodArgs = [])
    {
        $routeData = array_replace([
            'name' => 'route',
            'path' => '/',
            'requirements' => [],
            'options' => [],
            'defaults' => [],
            'schemes' => [],
            'methods' => [],
            'condition' => '',
        ], $routeData);

        $this->reader
            ->expects($this->once())
            ->method('getMethodAnnotations')
            ->will($this->returnValue([$this->getAnnotatedRoute($routeData)]));

        $routeCollection = $this->loader->load($className);
        $route = $routeCollection->get($routeData['name']);

        $this->assertSame($routeData['path'], $route->getPath(), '->load preserves path annotation');
        $this->assertCount(
            count($routeData['requirements']),
            array_intersect_assoc($routeData['requirements'], $route->getRequirements()),
            '->load preserves requirements annotation'
        );
        $this->assertCount(
            count($routeData['options']),
            array_intersect_assoc($routeData['options'], $route->getOptions()),
            '->load preserves options annotation'
        );
        $defaults = array_replace($methodArgs, $routeData['defaults']);
        $this->assertCount(
            count($defaults),
            array_intersect_assoc($defaults, $route->getDefaults()),
            '->load preserves defaults annotation and merges them with default arguments in method signature'
        );
        $this->assertEquals($routeData['schemes'], $route->getSchemes(), '->load preserves schemes annotation');
        $this->assertEquals($routeData['methods'], $route->getMethods(), '->load preserves methods annotation');
        $this->assertSame($routeData['condition'], $route->getCondition(), '->load preserves condition annotation');
    }

    public function test_class_route_load()
    {
        $classRouteData = [
            'path' => '/prefix',
            'schemes' => ['https'],
            'methods' => ['GET'],
        ];

        $methodRouteData = [
            'name' => 'route1',
            'path' => '/path',
            'schemes' => ['http'],
            'methods' => ['POST', 'PUT'],
        ];

        $this->reader
            ->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue($this->getAnnotatedRoute($classRouteData)));
        $this->reader
            ->expects($this->once())
            ->method('getMethodAnnotations')
            ->will($this->returnValue([$this->getAnnotatedRoute($methodRouteData)]));

        $routeCollection = $this->loader->load('Symfony\Component\Routing\Tests\Fixtures\AnnotatedClasses\BarClass');
        $route = $routeCollection->get($methodRouteData['name']);

        $this->assertSame($classRouteData['path'].$methodRouteData['path'], $route->getPath(), '->load concatenates class and method route path');
        $this->assertEquals(array_merge($classRouteData['schemes'], $methodRouteData['schemes']), $route->getSchemes(), '->load merges class and method route schemes');
        $this->assertEquals(array_merge($classRouteData['methods'], $methodRouteData['methods']), $route->getMethods(), '->load merges class and method route methods');
    }

    private function getAnnotatedRoute($data)
    {
        return new Route($data);
    }
}
