<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\DataCollector;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function test_collect()
    {
        $c = new RequestDataCollector;

        $c->collect($this->createRequest(), $this->createResponse());

        $attributes = $c->getRequestAttributes();

        $this->assertSame('request', $c->getName());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\HeaderBag', $c->getRequestHeaders());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestServer());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestCookies());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $attributes);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestRequest());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestQuery());
        $this->assertSame('html', $c->getFormat());
        $this->assertSame('foobar', $c->getRoute());
        $this->assertSame(['name' => 'foo'], $c->getRouteParams());
        $this->assertSame([], $c->getSessionAttributes());
        $this->assertSame('en', $c->getLocale());
        $this->assertRegExp('/Resource\(stream#\d+\)/', $attributes->get('resource'));
        $this->assertSame('Object(stdClass)', $attributes->get('object'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\HeaderBag', $c->getResponseHeaders());
        $this->assertSame('OK', $c->getStatusText());
        $this->assertSame(200, $c->getStatusCode());
        $this->assertSame('application/json', $c->getContentType());
    }

    /**
     * Test various types of controller callables.
     */
    public function test_controller_inspection()
    {
        // make sure we always match the line number
        $r1 = new \ReflectionMethod($this, 'testControllerInspection');
        $r2 = new \ReflectionMethod($this, 'staticControllerMethod');
        $r3 = new \ReflectionClass($this);
        // test name, callable, expected
        $controllerTests = [
            [
                '"Regular" callable',
                [$this, 'testControllerInspection'],
                [
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'testControllerInspection',
                    'file' => __FILE__,
                    'line' => $r1->getStartLine(),
                ],
            ],

            [
                'Closure',
                function () {
                    return 'foo';
                },
                [
                    'class' => __NAMESPACE__.'\{closure}',
                    'method' => null,
                    'file' => __FILE__,
                    'line' => __LINE__ - 5,
                ],
            ],

            [
                'Static callback as string',
                'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest::staticControllerMethod',
                'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest::staticControllerMethod',
            ],

            [
                'Static callable with instance',
                [$this, 'staticControllerMethod'],
                [
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'staticControllerMethod',
                    'file' => __FILE__,
                    'line' => $r2->getStartLine(),
                ],
            ],

            [
                'Static callable with class name',
                ['Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest', 'staticControllerMethod'],
                [
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'staticControllerMethod',
                    'file' => __FILE__,
                    'line' => $r2->getStartLine(),
                ],
            ],

            [
                'Callable with instance depending on __call()',
                [$this, 'magicMethod'],
                [
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'magicMethod',
                    'file' => 'n/a',
                    'line' => 'n/a',
                ],
            ],

            [
                'Callable with class name depending on __callStatic()',
                ['Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest', 'magicMethod'],
                [
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'magicMethod',
                    'file' => 'n/a',
                    'line' => 'n/a',
                ],
            ],

            [
                'Invokable controller',
                $this,
                [
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => null,
                    'file' => __FILE__,
                    'line' => $r3->getStartLine(),
                ],
            ],
        ];

        $c = new RequestDataCollector;
        $request = $this->createRequest();
        $response = $this->createResponse();
        foreach ($controllerTests as $controllerTest) {
            $this->injectController($c, $controllerTest[1], $request);
            $c->collect($request, $response);
            $this->assertSame($controllerTest[2], $c->getController(), sprintf('Testing: %s', $controllerTest[0]));
        }
    }

    protected function createRequest()
    {
        $request = Request::create('http://test.com/foo?bar=baz');
        $request->attributes->set('foo', 'bar');
        $request->attributes->set('_route', 'foobar');
        $request->attributes->set('_route_params', ['name' => 'foo']);
        $request->attributes->set('resource', fopen(__FILE__, 'r'));
        $request->attributes->set('object', new \stdClass);

        return $request;
    }

    protected function createResponse()
    {
        $response = new Response;
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->setCookie(new Cookie('foo', 'bar', 1, '/foo', 'localhost', true, true));
        $response->headers->setCookie(new Cookie('bar', 'foo', new \DateTime('@946684800')));
        $response->headers->setCookie(new Cookie('bazz', 'foo', '2000-12-12'));

        return $response;
    }

    /**
     * Inject the given controller callable into the data collector.
     */
    protected function injectController($collector, $controller, $request)
    {
        $resolver = $this->getMock('Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface');
        $httpKernel = new HttpKernel(new EventDispatcher, $resolver);
        $event = new FilterControllerEvent($httpKernel, $controller, $request, HttpKernelInterface::MASTER_REQUEST);
        $collector->onKernelController($event);
    }

    /**
     * Dummy method used as controller callable.
     */
    public static function staticControllerMethod()
    {
        throw new \LogicException('Unexpected method call');
    }

    /**
     * Magic method to allow non existing methods to be called and delegated.
     */
    public function __call($method, $args)
    {
        throw new \LogicException('Unexpected method call');
    }

    /**
     * Magic method to allow non existing methods to be called and delegated.
     */
    public static function __callStatic($method, $args)
    {
        throw new \LogicException('Unexpected method call');
    }

    public function __invoke()
    {
        throw new \LogicException('Unexpected method call');
    }
}
