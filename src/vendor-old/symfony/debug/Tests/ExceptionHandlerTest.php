<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Tests;

use Symfony\Component\Debug\Exception\OutOfMemoryException;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__.'/HeaderMock.php';

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        testHeader();
    }

    protected function tearDown()
    {
        testHeader();
    }

    public function test_debug()
    {
        $handler = new ExceptionHandler(false);

        ob_start();
        $handler->sendPhpResponse(new \RuntimeException('Foo'));
        $response = ob_get_clean();

        $this->assertContains('<h1>Whoops, looks like something went wrong.</h1>', $response);
        $this->assertNotContains('<h2 class="block_exception clear_fix">', $response);

        $handler = new ExceptionHandler(true);

        ob_start();
        $handler->sendPhpResponse(new \RuntimeException('Foo'));
        $response = ob_get_clean();

        $this->assertContains('<h1>Whoops, looks like something went wrong.</h1>', $response);
        $this->assertContains('<h2 class="block_exception clear_fix">', $response);
    }

    public function test_status_code()
    {
        $handler = new ExceptionHandler(false, 'iso8859-1');

        ob_start();
        $handler->sendPhpResponse(new NotFoundHttpException('Foo'));
        $response = ob_get_clean();

        $this->assertContains('Sorry, the page you are looking for could not be found.', $response);

        $expectedHeaders = [
            ['HTTP/1.0 404', true, null],
            ['Content-Type: text/html; charset=iso8859-1', true, null],
        ];

        $this->assertSame($expectedHeaders, testHeader());
    }

    public function test_headers()
    {
        $handler = new ExceptionHandler(false, 'iso8859-1');

        ob_start();
        $handler->sendPhpResponse(new MethodNotAllowedHttpException(['POST']));
        $response = ob_get_clean();

        $expectedHeaders = [
            ['HTTP/1.0 405', true, null],
            ['Allow: POST', false, null],
            ['Content-Type: text/html; charset=iso8859-1', true, null],
        ];

        $this->assertSame($expectedHeaders, testHeader());
    }

    public function test_nested_exceptions()
    {
        $handler = new ExceptionHandler(true);
        ob_start();
        $handler->sendPhpResponse(new \RuntimeException('Foo', 0, new \RuntimeException('Bar')));
        $response = ob_get_clean();

        $this->assertStringMatchesFormat('%A<span class="exception_message">Foo</span>%A<span class="exception_message">Bar</span>%A', $response);
    }

    public function test_handle()
    {
        $exception = new \Exception('foo');

        $handler = $this->getMock('Symfony\Component\Debug\ExceptionHandler', ['sendPhpResponse']);
        $handler
            ->expects($this->exactly(2))
            ->method('sendPhpResponse');

        $handler->handle($exception);

        $handler->setHandler(function ($e) use ($exception) {
            $this->assertSame($exception, $e);
        });

        $handler->handle($exception);
    }

    public function test_handle_out_of_memory_exception()
    {
        $exception = new OutOfMemoryException('foo', 0, E_ERROR, __FILE__, __LINE__);

        $handler = $this->getMock('Symfony\Component\Debug\ExceptionHandler', ['sendPhpResponse']);
        $handler
            ->expects($this->once())
            ->method('sendPhpResponse');

        $handler->setHandler(function ($e) {
            $this->fail('OutOfMemoryException should bypass the handler');
        });

        $handler->handle($exception);
    }
}
