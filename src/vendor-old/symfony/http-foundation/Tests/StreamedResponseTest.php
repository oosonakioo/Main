<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamedResponseTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor()
    {
        $response = new StreamedResponse(function () {
            echo 'foo';
        }, 404, ['Content-Type' => 'text/plain']);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('text/plain', $response->headers->get('Content-Type'));
    }

    public function test_prepare_with11_protocol()
    {
        $response = new StreamedResponse(function () {
            echo 'foo';
        });
        $request = Request::create('/');
        $request->server->set('SERVER_PROTOCOL', 'HTTP/1.1');

        $response->prepare($request);

        $this->assertEquals('1.1', $response->getProtocolVersion());
        $this->assertNotEquals('chunked', $response->headers->get('Transfer-Encoding'), 'Apache assumes responses with a Transfer-Encoding header set to chunked to already be encoded.');
    }

    public function test_prepare_with10_protocol()
    {
        $response = new StreamedResponse(function () {
            echo 'foo';
        });
        $request = Request::create('/');
        $request->server->set('SERVER_PROTOCOL', 'HTTP/1.0');

        $response->prepare($request);

        $this->assertEquals('1.0', $response->getProtocolVersion());
        $this->assertNull($response->headers->get('Transfer-Encoding'));
    }

    public function test_prepare_with_head_request()
    {
        $response = new StreamedResponse(function () {
            echo 'foo';
        });
        $request = Request::create('/', 'HEAD');

        $response->prepare($request);
    }

    public function test_prepare_with_cache_headers()
    {
        $response = new StreamedResponse(function () {
            echo 'foo';
        }, 200, ['Cache-Control' => 'max-age=600, public']);
        $request = Request::create('/', 'GET');

        $response->prepare($request);
        $this->assertEquals('max-age=600, public', $response->headers->get('Cache-Control'));
    }

    public function test_send_content()
    {
        $called = 0;

        $response = new StreamedResponse(function () use (&$called) {
            $called++;
        });

        $response->sendContent();
        $this->assertEquals(1, $called);

        $response->sendContent();
        $this->assertEquals(1, $called);
    }

    /**
     * @expectedException \LogicException
     */
    public function test_send_content_with_non_callable()
    {
        $response = new StreamedResponse(null);
        $response->sendContent();
    }

    /**
     * @expectedException \LogicException
     */
    public function test_set_content()
    {
        $response = new StreamedResponse(function () {
            echo 'foo';
        });
        $response->setContent('foo');
    }

    public function test_get_content()
    {
        $response = new StreamedResponse(function () {
            echo 'foo';
        });
        $this->assertFalse($response->getContent());
    }

    public function test_create()
    {
        $response = StreamedResponse::create(function () {}, 204);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
