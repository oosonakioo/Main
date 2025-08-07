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

use Symfony\Component\HttpFoundation\ServerBag;

/**
 * ServerBagTest.
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 */
class ServerBagTest extends \PHPUnit_Framework_TestCase
{
    public function test_should_extract_headers_from_server_array()
    {
        $server = [
            'SOME_SERVER_VARIABLE' => 'value',
            'SOME_SERVER_VARIABLE2' => 'value',
            'ROOT' => 'value',
            'HTTP_CONTENT_TYPE' => 'text/html',
            'HTTP_CONTENT_LENGTH' => '0',
            'HTTP_ETAG' => 'asdf',
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => 'bar',
        ];

        $bag = new ServerBag($server);

        $this->assertEquals([
            'CONTENT_TYPE' => 'text/html',
            'CONTENT_LENGTH' => '0',
            'ETAG' => 'asdf',
            'AUTHORIZATION' => 'Basic '.base64_encode('foo:bar'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => 'bar',
        ], $bag->getHeaders());
    }

    public function test_http_password_is_optional()
    {
        $bag = new ServerBag(['PHP_AUTH_USER' => 'foo']);

        $this->assertEquals([
            'AUTHORIZATION' => 'Basic '.base64_encode('foo:'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => '',
        ], $bag->getHeaders());
    }

    public function test_http_basic_auth_with_php_cgi()
    {
        $bag = new ServerBag(['HTTP_AUTHORIZATION' => 'Basic '.base64_encode('foo:bar')]);

        $this->assertEquals([
            'AUTHORIZATION' => 'Basic '.base64_encode('foo:bar'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => 'bar',
        ], $bag->getHeaders());
    }

    public function test_http_basic_auth_with_php_cgi_bogus()
    {
        $bag = new ServerBag(['HTTP_AUTHORIZATION' => 'Basic_'.base64_encode('foo:bar')]);

        // Username and passwords should not be set as the header is bogus
        $headers = $bag->getHeaders();
        $this->assertFalse(isset($headers['PHP_AUTH_USER']));
        $this->assertFalse(isset($headers['PHP_AUTH_PW']));
    }

    public function test_http_basic_auth_with_php_cgi_redirect()
    {
        $bag = new ServerBag(['REDIRECT_HTTP_AUTHORIZATION' => 'Basic '.base64_encode('username:pass:word')]);

        $this->assertEquals([
            'AUTHORIZATION' => 'Basic '.base64_encode('username:pass:word'),
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW' => 'pass:word',
        ], $bag->getHeaders());
    }

    public function test_http_basic_auth_with_php_cgi_empty_password()
    {
        $bag = new ServerBag(['HTTP_AUTHORIZATION' => 'Basic '.base64_encode('foo:')]);

        $this->assertEquals([
            'AUTHORIZATION' => 'Basic '.base64_encode('foo:'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => '',
        ], $bag->getHeaders());
    }

    public function test_http_digest_auth_with_php_cgi()
    {
        $digest = 'Digest username="foo", realm="acme", nonce="'.md5('secret').'", uri="/protected, qop="auth"';
        $bag = new ServerBag(['HTTP_AUTHORIZATION' => $digest]);

        $this->assertEquals([
            'AUTHORIZATION' => $digest,
            'PHP_AUTH_DIGEST' => $digest,
        ], $bag->getHeaders());
    }

    public function test_http_digest_auth_with_php_cgi_bogus()
    {
        $digest = 'Digest_username="foo", realm="acme", nonce="'.md5('secret').'", uri="/protected, qop="auth"';
        $bag = new ServerBag(['HTTP_AUTHORIZATION' => $digest]);

        // Username and passwords should not be set as the header is bogus
        $headers = $bag->getHeaders();
        $this->assertFalse(isset($headers['PHP_AUTH_USER']));
        $this->assertFalse(isset($headers['PHP_AUTH_PW']));
    }

    public function test_http_digest_auth_with_php_cgi_redirect()
    {
        $digest = 'Digest username="foo", realm="acme", nonce="'.md5('secret').'", uri="/protected, qop="auth"';
        $bag = new ServerBag(['REDIRECT_HTTP_AUTHORIZATION' => $digest]);

        $this->assertEquals([
            'AUTHORIZATION' => $digest,
            'PHP_AUTH_DIGEST' => $digest,
        ], $bag->getHeaders());
    }

    public function test_o_auth_bearer_auth()
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $bag = new ServerBag(['HTTP_AUTHORIZATION' => $headerContent]);

        $this->assertEquals([
            'AUTHORIZATION' => $headerContent,
        ], $bag->getHeaders());
    }

    public function test_o_auth_bearer_auth_with_redirect()
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $bag = new ServerBag(['REDIRECT_HTTP_AUTHORIZATION' => $headerContent]);

        $this->assertEquals([
            'AUTHORIZATION' => $headerContent,
        ], $bag->getHeaders());
    }

    /**
     * @see https://github.com/symfony/symfony/issues/17345
     */
    public function test_it_does_not_overwrite_the_authorization_header_if_it_is_already_set()
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $bag = new ServerBag(['PHP_AUTH_USER' => 'foo', 'HTTP_AUTHORIZATION' => $headerContent]);

        $this->assertEquals([
            'AUTHORIZATION' => $headerContent,
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => '',
        ], $bag->getHeaders());
    }
}
