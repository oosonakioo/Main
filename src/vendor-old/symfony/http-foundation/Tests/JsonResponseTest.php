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

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonResponseTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor_empty_creates_json_object()
    {
        $response = new JsonResponse;
        $this->assertSame('{}', $response->getContent());
    }

    public function test_constructor_with_array_creates_json_array()
    {
        $response = new JsonResponse([0, 1, 2, 3]);
        $this->assertSame('[0,1,2,3]', $response->getContent());
    }

    public function test_constructor_with_assoc_array_creates_json_object()
    {
        $response = new JsonResponse(['foo' => 'bar']);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function test_constructor_with_simple_types()
    {
        $response = new JsonResponse('foo');
        $this->assertSame('"foo"', $response->getContent());

        $response = new JsonResponse(0);
        $this->assertSame('0', $response->getContent());

        $response = new JsonResponse(0.1);
        $this->assertSame('0.1', $response->getContent());

        $response = new JsonResponse(true);
        $this->assertSame('true', $response->getContent());
    }

    public function test_constructor_with_custom_status()
    {
        $response = new JsonResponse([], 202);
        $this->assertSame(202, $response->getStatusCode());
    }

    public function test_constructor_adds_content_type_header()
    {
        $response = new JsonResponse;
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function test_constructor_with_custom_headers()
    {
        $response = new JsonResponse([], 200, ['ETag' => 'foo']);
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame('foo', $response->headers->get('ETag'));
    }

    public function test_constructor_with_custom_content_type()
    {
        $headers = ['Content-Type' => 'application/vnd.acme.blog-v1+json'];

        $response = new JsonResponse([], 200, $headers);
        $this->assertSame('application/vnd.acme.blog-v1+json', $response->headers->get('Content-Type'));
    }

    public function test_create()
    {
        $response = JsonResponse::create(['foo' => 'bar'], 204);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertEquals('{"foo":"bar"}', $response->getContent());
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function test_static_create_empty_json_object()
    {
        $response = JsonResponse::create();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertSame('{}', $response->getContent());
    }

    public function test_static_create_json_array()
    {
        $response = JsonResponse::create([0, 1, 2, 3]);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertSame('[0,1,2,3]', $response->getContent());
    }

    public function test_static_create_json_object()
    {
        $response = JsonResponse::create(['foo' => 'bar']);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function test_static_create_with_simple_types()
    {
        $response = JsonResponse::create('foo');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertSame('"foo"', $response->getContent());

        $response = JsonResponse::create(0);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertSame('0', $response->getContent());

        $response = JsonResponse::create(0.1);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertSame('0.1', $response->getContent());

        $response = JsonResponse::create(true);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertSame('true', $response->getContent());
    }

    public function test_static_create_with_custom_status()
    {
        $response = JsonResponse::create([], 202);
        $this->assertSame(202, $response->getStatusCode());
    }

    public function test_static_create_adds_content_type_header()
    {
        $response = JsonResponse::create();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function test_static_create_with_custom_headers()
    {
        $response = JsonResponse::create([], 200, ['ETag' => 'foo']);
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame('foo', $response->headers->get('ETag'));
    }

    public function test_static_create_with_custom_content_type()
    {
        $headers = ['Content-Type' => 'application/vnd.acme.blog-v1+json'];

        $response = JsonResponse::create([], 200, $headers);
        $this->assertSame('application/vnd.acme.blog-v1+json', $response->headers->get('Content-Type'));
    }

    public function test_set_callback()
    {
        $response = JsonResponse::create(['foo' => 'bar'])->setCallback('callback');

        $this->assertEquals('/**/callback({"foo":"bar"});', $response->getContent());
        $this->assertEquals('text/javascript', $response->headers->get('Content-Type'));
    }

    public function test_json_encode_flags()
    {
        $response = new JsonResponse('<>\'&"');

        $this->assertEquals('"\u003C\u003E\u0027\u0026\u0022"', $response->getContent());
    }

    public function test_get_encoding_options()
    {
        $response = new JsonResponse;

        $this->assertEquals(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT, $response->getEncodingOptions());
    }

    public function test_set_encoding_options()
    {
        $response = new JsonResponse;
        $response->setData([[1, 2, 3]]);

        $this->assertEquals('[[1,2,3]]', $response->getContent());

        $response->setEncodingOptions(JSON_FORCE_OBJECT);

        $this->assertEquals('{"0":{"0":1,"1":2,"2":3}}', $response->getContent());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_set_callback_invalid_identifier()
    {
        $response = new JsonResponse('foo');
        $response->setCallback('+invalid');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_set_content()
    {
        JsonResponse::create("\xB1\x31");
    }

    /**
     * @expectedException \Exception
     *
     * @expectedExceptionMessage This error is expected
     */
    public function test_set_content_json_serialize_error()
    {
        $serializable = new JsonSerializableObject;

        JsonResponse::create($serializable);
    }
}

if (interface_exists('JsonSerializable')) {
    class JsonSerializableObject implements \JsonSerializable
    {
        public function jsonSerialize()
        {
            throw new \Exception('This error is expected');
        }
    }
}
