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
use Symfony\Component\HttpKernel\EventListener\LocaleListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LocaleListenerTest extends \PHPUnit_Framework_TestCase
{
    private $requestStack;

    protected function setUp()
    {
        $this->requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack', [], [], '', false);
    }

    public function test_default_locale_without_session()
    {
        $listener = new LocaleListener($this->requestStack, 'fr');
        $event = $this->getEvent($request = Request::create('/'));

        $listener->onKernelRequest($event);
        $this->assertEquals('fr', $request->getLocale());
    }

    public function test_locale_from_request_attribute()
    {
        $request = Request::create('/');
        session_name('foo');
        $request->cookies->set('foo', 'value');

        $request->attributes->set('_locale', 'es');
        $listener = new LocaleListener($this->requestStack, 'fr');
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('es', $request->getLocale());
    }

    public function test_locale_set_for_routing_context()
    {
        // the request context is updated
        $context = $this->getMock('Symfony\Component\Routing\RequestContext');
        $context->expects($this->once())->method('setParameter')->with('_locale', 'es');

        $router = $this->getMock('Symfony\Component\Routing\Router', ['getContext'], [], '', false);
        $router->expects($this->once())->method('getContext')->will($this->returnValue($context));

        $request = Request::create('/');

        $request->attributes->set('_locale', 'es');
        $listener = new LocaleListener($this->requestStack, 'fr', $router);
        $listener->onKernelRequest($this->getEvent($request));
    }

    public function test_router_reset_with_parent_request_on_kernel_finish_request()
    {
        // the request context is updated
        $context = $this->getMock('Symfony\Component\Routing\RequestContext');
        $context->expects($this->once())->method('setParameter')->with('_locale', 'es');

        $router = $this->getMock('Symfony\Component\Routing\Router', ['getContext'], [], '', false);
        $router->expects($this->once())->method('getContext')->will($this->returnValue($context));

        $parentRequest = Request::create('/');
        $parentRequest->setLocale('es');

        $this->requestStack->expects($this->once())->method('getParentRequest')->will($this->returnValue($parentRequest));

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\FinishRequestEvent', [], [], '', false);

        $listener = new LocaleListener($this->requestStack, 'fr', $router);
        $listener->onKernelFinishRequest($event);
    }

    public function test_request_locale_is_not_overridden()
    {
        $request = Request::create('/');
        $request->setLocale('de');
        $listener = new LocaleListener($this->requestStack, 'fr');
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('de', $request->getLocale());
    }

    private function getEvent(Request $request)
    {
        return new GetResponseEvent($this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'), $request, HttpKernelInterface::MASTER_REQUEST);
    }
}
