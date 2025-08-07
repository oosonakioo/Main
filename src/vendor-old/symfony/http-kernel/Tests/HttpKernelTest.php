<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class HttpKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function test_handle_when_controller_throws_an_exception_and_catch_is_true()
    {
        $kernel = new HttpKernel(new EventDispatcher, $this->getResolver(function () {
            throw new \RuntimeException;
        }));

        $kernel->handle(new Request, HttpKernelInterface::MASTER_REQUEST, true);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_handle_when_controller_throws_an_exception_and_catch_is_false_and_no_listener_is_registered()
    {
        $kernel = new HttpKernel(new EventDispatcher, $this->getResolver(function () {
            throw new \RuntimeException;
        }));

        $kernel->handle(new Request, HttpKernelInterface::MASTER_REQUEST, false);
    }

    public function test_handle_when_controller_throws_an_exception_and_catch_is_true_with_a_handling_listener()
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::EXCEPTION, function ($event) {
            $event->setResponse(new Response($event->getException()->getMessage()));
        });

        $kernel = new HttpKernel($dispatcher, $this->getResolver(function () {
            throw new \RuntimeException('foo');
        }));
        $response = $kernel->handle(new Request, HttpKernelInterface::MASTER_REQUEST, true);

        $this->assertEquals('500', $response->getStatusCode());
        $this->assertEquals('foo', $response->getContent());
    }

    public function test_handle_when_controller_throws_an_exception_and_catch_is_true_with_a_non_handling_listener()
    {
        $exception = new \RuntimeException;

        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::EXCEPTION, function ($event) {
            // should set a response, but does not
        });

        $kernel = new HttpKernel($dispatcher, $this->getResolver(function () use ($exception) {
            throw $exception;
        }));

        try {
            $kernel->handle(new Request, HttpKernelInterface::MASTER_REQUEST, true);
            $this->fail('LogicException expected');
        } catch (\RuntimeException $e) {
            $this->assertSame($exception, $e);
        }
    }

    public function test_handle_exception_with_a_redirection_response()
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::EXCEPTION, function ($event) {
            $event->setResponse(new RedirectResponse('/login', 301));
        });

        $kernel = new HttpKernel($dispatcher, $this->getResolver(function () {
            throw new AccessDeniedHttpException;
        }));
        $response = $kernel->handle(new Request);

        $this->assertEquals('301', $response->getStatusCode());
        $this->assertEquals('/login', $response->headers->get('Location'));
    }

    public function test_handle_http_exception()
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::EXCEPTION, function ($event) {
            $event->setResponse(new Response($event->getException()->getMessage()));
        });

        $kernel = new HttpKernel($dispatcher, $this->getResolver(function () {
            throw new MethodNotAllowedHttpException(['POST']);
        }));
        $response = $kernel->handle(new Request);

        $this->assertEquals('405', $response->getStatusCode());
        $this->assertEquals('POST', $response->headers->get('Allow'));
    }

    /**
     * @dataProvider getStatusCodes
     */
    public function test_handle_when_an_exception_is_handled_with_a_specific_status_code($responseStatusCode, $expectedStatusCode)
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::EXCEPTION, function ($event) use ($responseStatusCode, $expectedStatusCode) {
            $event->setResponse(new Response('', $responseStatusCode, ['X-Status-Code' => $expectedStatusCode]));
        });

        $kernel = new HttpKernel($dispatcher, $this->getResolver(function () {
            throw new \RuntimeException;
        }));
        $response = $kernel->handle(new Request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertFalse($response->headers->has('X-Status-Code'));
    }

    public function getStatusCodes()
    {
        return [
            [200, 404],
            [404, 200],
            [301, 200],
            [500, 200],
        ];
    }

    public function test_handle_when_a_listener_returns_a_response()
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::REQUEST, function ($event) {
            $event->setResponse(new Response('hello'));
        });

        $kernel = new HttpKernel($dispatcher, $this->getResolver());

        $this->assertEquals('hello', $kernel->handle(new Request)->getContent());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function test_handle_when_no_controller_is_found()
    {
        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver(false));

        $kernel->handle(new Request);
    }

    public function test_handle_when_the_controller_is_a_closure()
    {
        $response = new Response('foo');
        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver(function () use ($response) {
            return $response;
        }));

        $this->assertSame($response, $kernel->handle(new Request));
    }

    public function test_handle_when_the_controller_is_an_object_with_invoke()
    {
        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver(new Controller));

        $this->assertResponseEquals(new Response('foo'), $kernel->handle(new Request));
    }

    public function test_handle_when_the_controller_is_a_function()
    {
        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver('Symfony\Component\HttpKernel\Tests\controller_func'));

        $this->assertResponseEquals(new Response('foo'), $kernel->handle(new Request));
    }

    public function test_handle_when_the_controller_is_an_array()
    {
        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver([new Controller, 'controller']));

        $this->assertResponseEquals(new Response('foo'), $kernel->handle(new Request));
    }

    public function test_handle_when_the_controller_is_a_static_array()
    {
        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver(['Symfony\Component\HttpKernel\Tests\Controller', 'staticcontroller']));

        $this->assertResponseEquals(new Response('foo'), $kernel->handle(new Request));
    }

    /**
     * @expectedException \LogicException
     */
    public function test_handle_when_the_controller_does_not_return_a_response()
    {
        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver(function () {
            return 'foo';
        }));

        $kernel->handle(new Request);
    }

    public function test_handle_when_the_controller_does_not_return_a_response_but_a_view_is_registered()
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::VIEW, function ($event) {
            $event->setResponse(new Response($event->getControllerResult()));
        });
        $kernel = new HttpKernel($dispatcher, $this->getResolver(function () {
            return 'foo';
        }));

        $this->assertEquals('foo', $kernel->handle(new Request)->getContent());
    }

    public function test_handle_with_a_response_listener()
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::RESPONSE, function ($event) {
            $event->setResponse(new Response('foo'));
        });
        $kernel = new HttpKernel($dispatcher, $this->getResolver());

        $this->assertEquals('foo', $kernel->handle(new Request)->getContent());
    }

    public function test_terminate()
    {
        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver());
        $dispatcher->addListener(KernelEvents::TERMINATE, function ($event) use (&$called, &$capturedKernel, &$capturedRequest, &$capturedResponse) {
            $called = true;
            $capturedKernel = $event->getKernel();
            $capturedRequest = $event->getRequest();
            $capturedResponse = $event->getResponse();
        });

        $kernel->terminate($request = Request::create('/'), $response = new Response);
        $this->assertTrue($called);
        $this->assertEquals($kernel, $capturedKernel);
        $this->assertEquals($request, $capturedRequest);
        $this->assertEquals($response, $capturedResponse);
    }

    public function test_verify_request_stack_push_pop_during_handle()
    {
        $request = new Request;

        $stack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack', ['push', 'pop']);
        $stack->expects($this->at(0))->method('push')->with($this->equalTo($request));
        $stack->expects($this->at(1))->method('pop');

        $dispatcher = new EventDispatcher;
        $kernel = new HttpKernel($dispatcher, $this->getResolver(), $stack);

        $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function test_inconsistent_client_ips_on_master_requests()
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener(KernelEvents::REQUEST, function ($event) {
            $event->getRequest()->getClientIp();
        });

        $kernel = new HttpKernel($dispatcher, $this->getResolver());

        $request = new Request;
        $request->setTrustedProxies(['1.1.1.1']);
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $request->headers->set('FORWARDED', '2.2.2.2');
        $request->headers->set('X_FORWARDED_FOR', '3.3.3.3');

        $kernel->handle($request, $kernel::MASTER_REQUEST, false);
    }

    protected function getResolver($controller = null)
    {
        if ($controller === null) {
            $controller = function () {
                return new Response('Hello');
            };
        }

        $resolver = $this->getMock('Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface');
        $resolver->expects($this->any())
            ->method('getController')
            ->will($this->returnValue($controller));
        $resolver->expects($this->any())
            ->method('getArguments')
            ->will($this->returnValue([]));

        return $resolver;
    }

    protected function assertResponseEquals(Response $expected, Response $actual)
    {
        $expected->setDate($actual->getDate());
        $this->assertEquals($expected, $actual);
    }
}

class Controller
{
    public function __invoke()
    {
        return new Response('foo');
    }

    public function controller()
    {
        return new Response('foo');
    }

    public static function staticController()
    {
        return new Response('foo');
    }
}

function controller_func()
{
    return new Response('foo');
}
