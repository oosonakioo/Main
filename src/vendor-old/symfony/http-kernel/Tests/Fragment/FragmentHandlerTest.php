<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Fragment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

/**
 * @group time-sensitive
 */
class FragmentHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $requestStack;

    protected function setUp()
    {
        $this->requestStack = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\RequestStack')
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue(Request::create('/')));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_render_when_renderer_does_not_exist()
    {
        $handler = new FragmentHandler($this->requestStack);
        $handler->render('/', 'foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_render_with_unknown_renderer()
    {
        $handler = $this->getHandler($this->returnValue(new Response('foo')));

        $handler->render('/', 'bar');
    }

    /**
     * @expectedException \RuntimeException
     *
     * @expectedExceptionMessage Error when rendering "http://localhost/" (Status code is 404).
     */
    public function test_deliver_with_unsuccessful_response()
    {
        $handler = $this->getHandler($this->returnValue(new Response('foo', 404)));

        $handler->render('/', 'foo');
    }

    public function test_render()
    {
        $handler = $this->getHandler($this->returnValue(new Response('foo')), ['/', Request::create('/'), ['foo' => 'foo', 'ignore_errors' => true]]);

        $this->assertEquals('foo', $handler->render('/', 'foo', ['foo' => 'foo']));
    }

    protected function getHandler($returnValue, $arguments = [])
    {
        $renderer = $this->getMock('Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface');
        $renderer
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $e = $renderer
            ->expects($this->any())
            ->method('render')
            ->will($returnValue);

        if ($arguments) {
            call_user_func_array([$e, 'with'], $arguments);
        }

        $handler = new FragmentHandler($this->requestStack);
        $handler->addRenderer($renderer);

        return $handler;
    }
}
