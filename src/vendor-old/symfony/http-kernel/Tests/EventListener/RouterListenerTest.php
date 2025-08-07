<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RequestContext;

class RouterListenerTest extends \PHPUnit_Framework_TestCase
{
    private $requestStack;

    protected function setUp()
    {
        $this->requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack', [], [], '', false);
    }

    /**
     * @dataProvider getPortData
     */
    public function test_port($defaultHttpPort, $defaultHttpsPort, $uri, $expectedHttpPort, $expectedHttpsPort)
    {
        $urlMatcher = $this->getMockBuilder('Symfony\Component\Routing\Matcher\UrlMatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $context = new RequestContext;
        $context->setHttpPort($defaultHttpPort);
        $context->setHttpsPort($defaultHttpsPort);
        $urlMatcher->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($context));

        $listener = new RouterListener($urlMatcher, $this->requestStack);
        $event = $this->createGetResponseEventForUri($uri);
        $listener->onKernelRequest($event);

        $this->assertEquals($expectedHttpPort, $context->getHttpPort());
        $this->assertEquals($expectedHttpsPort, $context->getHttpsPort());
        $this->assertEquals(strpos($uri, 'https') === 0 ? 'https' : 'http', $context->getScheme());
    }

    public function getPortData()
    {
        return [
            [80, 443, 'http://localhost/', 80, 443],
            [80, 443, 'http://localhost:90/', 90, 443],
            [80, 443, 'https://localhost/', 80, 443],
            [80, 443, 'https://localhost:90/', 80, 90],
        ];
    }

    /**
     * @param  string  $uri
     * @return GetResponseEvent
     */
    private function createGetResponseEventForUri($uri)
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create($uri);
        $request->attributes->set('_controller', null); // Prevents going in to routing process

        return new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_invalid_matcher()
    {
        new RouterListener(new \stdClass, $this->requestStack);
    }

    public function test_request_matcher()
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('http://localhost/');
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $requestMatcher = $this->getMock('Symfony\Component\Routing\Matcher\RequestMatcherInterface');
        $requestMatcher->expects($this->once())
            ->method('matchRequest')
            ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
            ->will($this->returnValue([]));

        $listener = new RouterListener($requestMatcher, $this->requestStack, new RequestContext);
        $listener->onKernelRequest($event);
    }

    public function test_sub_request_with_different_method()
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('http://localhost/', 'post');
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $requestMatcher = $this->getMock('Symfony\Component\Routing\Matcher\RequestMatcherInterface');
        $requestMatcher->expects($this->any())
            ->method('matchRequest')
            ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
            ->will($this->returnValue([]));

        $context = new RequestContext;

        $listener = new RouterListener($requestMatcher, $this->requestStack, new RequestContext);
        $listener->onKernelRequest($event);

        // sub-request with another HTTP method
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('http://localhost/', 'get');
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertEquals('GET', $context->getMethod());
    }

    /**
     * @dataProvider getLoggingParameterData
     */
    public function test_logging_parameter($parameter, $log)
    {
        $requestMatcher = $this->getMock('Symfony\Component\Routing\Matcher\RequestMatcherInterface');
        $requestMatcher->expects($this->once())
            ->method('matchRequest')
            ->will($this->returnValue($parameter));

        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())
            ->method('info')
            ->with($this->equalTo($log));

        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('http://localhost/');

        $listener = new RouterListener($requestMatcher, $this->requestStack, new RequestContext, $logger);
        $listener->onKernelRequest(new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST));
    }

    public function getLoggingParameterData()
    {
        return [
            [['_route' => 'foo'], 'Matched route "foo".'],
            [[], 'Matched route "n/a".'],
        ];
    }
}
