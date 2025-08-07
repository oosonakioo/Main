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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function test_initialize()
    {
        $request = new Request;

        $request->initialize(['foo' => 'bar']);
        $this->assertEquals('bar', $request->query->get('foo'), '->initialize() takes an array of query parameters as its first argument');

        $request->initialize([], ['foo' => 'bar']);
        $this->assertEquals('bar', $request->request->get('foo'), '->initialize() takes an array of request parameters as its second argument');

        $request->initialize([], [], ['foo' => 'bar']);
        $this->assertEquals('bar', $request->attributes->get('foo'), '->initialize() takes an array of attributes as its third argument');

        $request->initialize([], [], [], [], [], ['HTTP_FOO' => 'bar']);
        $this->assertEquals('bar', $request->headers->get('FOO'), '->initialize() takes an array of HTTP headers as its sixth argument');
    }

    public function test_get_locale()
    {
        $request = new Request;
        $request->setLocale('pl');
        $locale = $request->getLocale();
        $this->assertEquals('pl', $locale);
    }

    public function test_get_user()
    {
        $request = Request::create('http://user_test:password_test@test.com/');
        $user = $request->getUser();

        $this->assertEquals('user_test', $user);
    }

    public function test_get_password()
    {
        $request = Request::create('http://user_test:password_test@test.com/');
        $password = $request->getPassword();

        $this->assertEquals('password_test', $password);
    }

    public function test_is_no_cache()
    {
        $request = new Request;
        $isNoCache = $request->isNoCache();

        $this->assertFalse($isNoCache);
    }

    public function test_get_content_type()
    {
        $request = new Request;
        $contentType = $request->getContentType();

        $this->assertNull($contentType);
    }

    public function test_set_default_locale()
    {
        $request = new Request;
        $request->setDefaultLocale('pl');
        $locale = $request->getLocale();

        $this->assertEquals('pl', $locale);
    }

    public function test_create()
    {
        $request = Request::create('http://test.com/foo?bar=baz');
        $this->assertEquals('http://test.com/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('bar=baz', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com/foo', 'GET', ['bar' => 'baz']);
        $this->assertEquals('http://test.com/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('bar=baz', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com/foo?bar=foo', 'GET', ['bar' => 'baz']);
        $this->assertEquals('http://test.com/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('bar=baz', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('https://test.com/foo?bar=baz');
        $this->assertEquals('https://test.com/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('bar=baz', $request->getQueryString());
        $this->assertEquals(443, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertTrue($request->isSecure());

        $request = Request::create('test.com:90/foo');
        $this->assertEquals('http://test.com:90/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('test.com', $request->getHost());
        $this->assertEquals('test.com:90', $request->getHttpHost());
        $this->assertEquals(90, $request->getPort());
        $this->assertFalse($request->isSecure());

        $request = Request::create('https://test.com:90/foo');
        $this->assertEquals('https://test.com:90/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('test.com', $request->getHost());
        $this->assertEquals('test.com:90', $request->getHttpHost());
        $this->assertEquals(90, $request->getPort());
        $this->assertTrue($request->isSecure());

        $request = Request::create('https://127.0.0.1:90/foo');
        $this->assertEquals('https://127.0.0.1:90/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('127.0.0.1', $request->getHost());
        $this->assertEquals('127.0.0.1:90', $request->getHttpHost());
        $this->assertEquals(90, $request->getPort());
        $this->assertTrue($request->isSecure());

        $request = Request::create('https://[::1]:90/foo');
        $this->assertEquals('https://[::1]:90/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('[::1]', $request->getHost());
        $this->assertEquals('[::1]:90', $request->getHttpHost());
        $this->assertEquals(90, $request->getPort());
        $this->assertTrue($request->isSecure());

        $request = Request::create('https://[::1]/foo');
        $this->assertEquals('https://[::1]/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('[::1]', $request->getHost());
        $this->assertEquals('[::1]', $request->getHttpHost());
        $this->assertEquals(443, $request->getPort());
        $this->assertTrue($request->isSecure());

        $json = '{"jsonrpc":"2.0","method":"echo","id":7,"params":["Hello World"]}';
        $request = Request::create('http://example.com/jsonrpc', 'POST', [], [], [], [], $json);
        $this->assertEquals($json, $request->getContent());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com');
        $this->assertEquals('http://test.com/', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com?test=1');
        $this->assertEquals('http://test.com/?test=1', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('test=1', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com:90/?test=1');
        $this->assertEquals('http://test.com:90/?test=1', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('test=1', $request->getQueryString());
        $this->assertEquals(90, $request->getPort());
        $this->assertEquals('test.com:90', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://username:password@test.com');
        $this->assertEquals('http://test.com/', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertEquals('username', $request->getUser());
        $this->assertEquals('password', $request->getPassword());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://username@test.com');
        $this->assertEquals('http://test.com/', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertEquals('username', $request->getUser());
        $this->assertSame('', $request->getPassword());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com/?foo');
        $this->assertEquals('/?foo', $request->getRequestUri());
        $this->assertEquals(['foo' => ''], $request->query->all());

        // assume rewrite rule: (.*) --> app/app.php; app/ is a symlink to a symfony web/ directory
        $request = Request::create('http://test.com/apparthotel-1234', 'GET', [], [], [],
            [
                'DOCUMENT_ROOT' => '/var/www/www.test.com',
                'SCRIPT_FILENAME' => '/var/www/www.test.com/app/app.php',
                'SCRIPT_NAME' => '/app/app.php',
                'PHP_SELF' => '/app/app.php/apparthotel-1234',
            ]);
        $this->assertEquals('http://test.com/apparthotel-1234', $request->getUri());
        $this->assertEquals('/apparthotel-1234', $request->getPathInfo());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());
    }

    public function test_create_check_precedence()
    {
        // server is used by default
        $request = Request::create('/', 'DELETE', [], [], [], [
            'HTTP_HOST' => 'example.com',
            'HTTPS' => 'on',
            'SERVER_PORT' => 443,
            'PHP_AUTH_USER' => 'fabien',
            'PHP_AUTH_PW' => 'pa$$',
            'QUERY_STRING' => 'foo=bar',
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(443, $request->getPort());
        $this->assertTrue($request->isSecure());
        $this->assertEquals('fabien', $request->getUser());
        $this->assertEquals('pa$$', $request->getPassword());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals('application/json', $request->headers->get('CONTENT_TYPE'));

        // URI has precedence over server
        $request = Request::create('http://thomas:pokemon@example.net:8080/?foo=bar', 'GET', [], [], [], [
            'HTTP_HOST' => 'example.com',
            'HTTPS' => 'on',
            'SERVER_PORT' => 443,
        ]);
        $this->assertEquals('example.net', $request->getHost());
        $this->assertEquals(8080, $request->getPort());
        $this->assertFalse($request->isSecure());
        $this->assertEquals('thomas', $request->getUser());
        $this->assertEquals('pokemon', $request->getPassword());
        $this->assertEquals('foo=bar', $request->getQueryString());
    }

    public function test_duplicate()
    {
        $request = new Request(['foo' => 'bar'], ['foo' => 'bar'], ['foo' => 'bar'], [], [], ['HTTP_FOO' => 'bar']);
        $dup = $request->duplicate();

        $this->assertEquals($request->query->all(), $dup->query->all(), '->duplicate() duplicates a request an copy the current query parameters');
        $this->assertEquals($request->request->all(), $dup->request->all(), '->duplicate() duplicates a request an copy the current request parameters');
        $this->assertEquals($request->attributes->all(), $dup->attributes->all(), '->duplicate() duplicates a request an copy the current attributes');
        $this->assertEquals($request->headers->all(), $dup->headers->all(), '->duplicate() duplicates a request an copy the current HTTP headers');

        $dup = $request->duplicate(['foo' => 'foobar'], ['foo' => 'foobar'], ['foo' => 'foobar'], [], [], ['HTTP_FOO' => 'foobar']);

        $this->assertEquals(['foo' => 'foobar'], $dup->query->all(), '->duplicate() overrides the query parameters if provided');
        $this->assertEquals(['foo' => 'foobar'], $dup->request->all(), '->duplicate() overrides the request parameters if provided');
        $this->assertEquals(['foo' => 'foobar'], $dup->attributes->all(), '->duplicate() overrides the attributes if provided');
        $this->assertEquals(['foo' => ['foobar']], $dup->headers->all(), '->duplicate() overrides the HTTP header if provided');
    }

    public function test_duplicate_with_format()
    {
        $request = new Request([], [], ['_format' => 'json']);
        $dup = $request->duplicate();

        $this->assertEquals('json', $dup->getRequestFormat());
        $this->assertEquals('json', $dup->attributes->get('_format'));

        $request = new Request;
        $request->setRequestFormat('xml');
        $dup = $request->duplicate();

        $this->assertEquals('xml', $dup->getRequestFormat());
    }

    /**
     * @dataProvider getFormatToMimeTypeMapProvider
     */
    public function test_get_format_from_mime_type($format, $mimeTypes)
    {
        $request = new Request;
        foreach ($mimeTypes as $mime) {
            $this->assertEquals($format, $request->getFormat($mime));
        }
        $request->setFormat($format, $mimeTypes);
        foreach ($mimeTypes as $mime) {
            $this->assertEquals($format, $request->getFormat($mime));
        }
    }

    public function test_get_format_from_mime_type_with_parameters()
    {
        $request = new Request;
        $this->assertEquals('json', $request->getFormat('application/json; charset=utf-8'));
    }

    /**
     * @dataProvider getFormatToMimeTypeMapProvider
     */
    public function test_get_mime_type_from_format($format, $mimeTypes)
    {
        if ($format !== null) {
            $request = new Request;
            $this->assertEquals($mimeTypes[0], $request->getMimeType($format));
        }
    }

    public function test_get_format_with_custom_mime_type()
    {
        $request = new Request;
        $request->setFormat('custom', 'application/vnd.foo.api;myversion=2.3');
        $this->assertEquals('custom', $request->getFormat('application/vnd.foo.api;myversion=2.3'));
    }

    public function getFormatToMimeTypeMapProvider()
    {
        return [
            [null, [null, 'unexistent-mime-type']],
            ['txt', ['text/plain']],
            ['js', ['application/javascript', 'application/x-javascript', 'text/javascript']],
            ['css', ['text/css']],
            ['json', ['application/json', 'application/x-json']],
            ['xml', ['text/xml', 'application/xml', 'application/x-xml']],
            ['rdf', ['application/rdf+xml']],
            ['atom', ['application/atom+xml']],
        ];
    }

    public function test_get_uri()
    {
        $server = [];

        // Standard Request on non default PORT
        // http://host:8080/index.php/path/info?query=string

        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/index.php/path/info?query=string';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PATH_INFO'] = '/path/info';
        $server['PATH_TRANSLATED'] = 'redirect:/index.php/path/info';
        $server['PHP_SELF'] = '/index_dev.php/path/info';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request = new Request;

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://host:8080/index.php/path/info?query=string', $request->getUri(), '->getUri() with non default port');

        // Use std port number
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://host/index.php/path/info?query=string', $request->getUri(), '->getUri() with default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://servername/index.php/path/info?query=string', $request->getUri(), '->getUri() with default port without HOST_HEADER');

        // Request with URL REWRITING (hide index.php)
        //   RewriteCond %{REQUEST_FILENAME} !-f
        //   RewriteRule ^(.*)$ index.php [QSA,L]
        // http://host:8080/path/info?query=string
        $server = [];
        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['REDIRECT_QUERY_STRING'] = 'query=string';
        $server['REDIRECT_URL'] = '/path/info';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/path/info?toto=test&1=1';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PHP_SELF'] = '/index.php';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://host:8080/path/info?query=string', $request->getUri(), '->getUri() with rewrite');

        // Use std port number
        //  http://host/path/info?query=string
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://host/path/info?query=string', $request->getUri(), '->getUri() with rewrite and default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://servername/path/info?query=string', $request->getUri(), '->getUri() with rewrite, default port without HOST_HEADER');

        // With encoded characters

        $server = [
            'HTTP_HOST' => 'host:8080',
            'SERVER_NAME' => 'servername',
            'SERVER_PORT' => '8080',
            'QUERY_STRING' => 'query=string',
            'REQUEST_URI' => '/ba%20se/index_dev.php/foo%20bar/in+fo?query=string',
            'SCRIPT_NAME' => '/ba se/index_dev.php',
            'PATH_TRANSLATED' => 'redirect:/index.php/foo bar/in+fo',
            'PHP_SELF' => '/ba se/index_dev.php/path/info',
            'SCRIPT_FILENAME' => '/some/where/ba se/index_dev.php',
        ];

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals(
            'http://host:8080/ba%20se/index_dev.php/foo%20bar/in+fo?query=string',
            $request->getUri()
        );

        // with user info

        $server['PHP_AUTH_USER'] = 'fabien';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://host:8080/ba%20se/index_dev.php/foo%20bar/in+fo?query=string', $request->getUri());

        $server['PHP_AUTH_PW'] = 'symfony';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://host:8080/ba%20se/index_dev.php/foo%20bar/in+fo?query=string', $request->getUri());
    }

    public function test_get_uri_for_path()
    {
        $request = Request::create('http://test.com/foo?bar=baz');
        $this->assertEquals('http://test.com/some/path', $request->getUriForPath('/some/path'));

        $request = Request::create('http://test.com:90/foo?bar=baz');
        $this->assertEquals('http://test.com:90/some/path', $request->getUriForPath('/some/path'));

        $request = Request::create('https://test.com/foo?bar=baz');
        $this->assertEquals('https://test.com/some/path', $request->getUriForPath('/some/path'));

        $request = Request::create('https://test.com:90/foo?bar=baz');
        $this->assertEquals('https://test.com:90/some/path', $request->getUriForPath('/some/path'));

        $server = [];

        // Standard Request on non default PORT
        // http://host:8080/index.php/path/info?query=string

        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/index.php/path/info?query=string';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PATH_INFO'] = '/path/info';
        $server['PATH_TRANSLATED'] = 'redirect:/index.php/path/info';
        $server['PHP_SELF'] = '/index_dev.php/path/info';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request = new Request;

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://host:8080/index.php/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with non default port');

        // Use std port number
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://host/index.php/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://servername/index.php/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with default port without HOST_HEADER');

        // Request with URL REWRITING (hide index.php)
        //   RewriteCond %{REQUEST_FILENAME} !-f
        //   RewriteRule ^(.*)$ index.php [QSA,L]
        // http://host:8080/path/info?query=string
        $server = [];
        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['REDIRECT_QUERY_STRING'] = 'query=string';
        $server['REDIRECT_URL'] = '/path/info';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/path/info?toto=test&1=1';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PHP_SELF'] = '/index.php';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://host:8080/some/path', $request->getUriForPath('/some/path'), '->getUri() with rewrite');

        // Use std port number
        //  http://host/path/info?query=string
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://host/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with rewrite and default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('http://servername/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with rewrite, default port without HOST_HEADER');
        $this->assertEquals('servername', $request->getHttpHost());

        // with user info

        $server['PHP_AUTH_USER'] = 'fabien';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://servername/some/path', $request->getUriForPath('/some/path'));

        $server['PHP_AUTH_PW'] = 'symfony';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://servername/some/path', $request->getUriForPath('/some/path'));
    }

    /**
     * @dataProvider getRelativeUriForPathData()
     */
    public function test_get_relative_uri_for_path($expected, $pathinfo, $path)
    {
        $this->assertEquals($expected, Request::create($pathinfo)->getRelativeUriForPath($path));
    }

    public function getRelativeUriForPathData()
    {
        return [
            ['me.png', '/foo', '/me.png'],
            ['../me.png', '/foo/bar', '/me.png'],
            ['me.png', '/foo/bar', '/foo/me.png'],
            ['../baz/me.png', '/foo/bar/b', '/foo/baz/me.png'],
            ['../../fooz/baz/me.png', '/foo/bar/b', '/fooz/baz/me.png'],
            ['baz/me.png', '/foo/bar/b', 'baz/me.png'],
        ];
    }

    public function test_get_user_info()
    {
        $request = new Request;

        $server = ['PHP_AUTH_USER' => 'fabien'];
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('fabien', $request->getUserInfo());

        $server['PHP_AUTH_USER'] = '0';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('0', $request->getUserInfo());

        $server['PHP_AUTH_PW'] = '0';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('0:0', $request->getUserInfo());
    }

    public function test_get_scheme_and_http_host()
    {
        $request = new Request;

        $server = [];
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '90';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://servername:90', $request->getSchemeAndHttpHost());

        $server['PHP_AUTH_USER'] = 'fabien';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://servername:90', $request->getSchemeAndHttpHost());

        $server['PHP_AUTH_USER'] = '0';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://servername:90', $request->getSchemeAndHttpHost());

        $server['PHP_AUTH_PW'] = '0';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('http://servername:90', $request->getSchemeAndHttpHost());
    }

    /**
     * @dataProvider getQueryStringNormalizationData
     */
    public function test_get_query_string($query, $expectedQuery, $msg)
    {
        $request = new Request;

        $request->server->set('QUERY_STRING', $query);
        $this->assertSame($expectedQuery, $request->getQueryString(), $msg);
    }

    public function getQueryStringNormalizationData()
    {
        return [
            ['foo', 'foo', 'works with valueless parameters'],
            ['foo=', 'foo=', 'includes a dangling equal sign'],
            ['bar=&foo=bar', 'bar=&foo=bar', '->works with empty parameters'],
            ['foo=bar&bar=', 'bar=&foo=bar', 'sorts keys alphabetically'],

            // GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
            // PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str.
            ['him=John%20Doe&her=Jane+Doe', 'her=Jane%20Doe&him=John%20Doe', 'normalizes spaces in both encodings "%20" and "+"'],

            ['foo[]=1&foo[]=2', 'foo%5B%5D=1&foo%5B%5D=2', 'allows array notation'],
            ['foo=1&foo=2', 'foo=1&foo=2', 'allows repeated parameters'],
            ['pa%3Dram=foo%26bar%3Dbaz&test=test', 'pa%3Dram=foo%26bar%3Dbaz&test=test', 'works with encoded delimiters'],
            ['0', '0', 'allows "0"'],
            ['Jane Doe&John%20Doe', 'Jane%20Doe&John%20Doe', 'normalizes encoding in keys'],
            ['her=Jane Doe&him=John%20Doe', 'her=Jane%20Doe&him=John%20Doe', 'normalizes encoding in values'],
            ['foo=bar&&&test&&', 'foo=bar&test', 'removes unneeded delimiters'],
            ['formula=e=m*c^2', 'formula=e%3Dm%2Ac%5E2', 'correctly treats only the first "=" as delimiter and the next as value'],

            // Ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
            // PHP also does not include them when building _GET.
            ['foo=bar&=a=b&=x=y', 'foo=bar', 'removes params with empty key'],
        ];
    }

    public function test_get_query_string_returns_null()
    {
        $request = new Request;

        $this->assertNull($request->getQueryString(), '->getQueryString() returns null for non-existent query string');

        $request->server->set('QUERY_STRING', '');
        $this->assertNull($request->getQueryString(), '->getQueryString() returns null for empty query string');
    }

    public function test_get_host()
    {
        $request = new Request;

        $request->initialize(['foo' => 'bar']);
        $this->assertEquals('', $request->getHost(), '->getHost() return empty string if not initialized');

        $request->initialize([], [], [], [], [], ['HTTP_HOST' => 'www.example.com']);
        $this->assertEquals('www.example.com', $request->getHost(), '->getHost() from Host Header');

        // Host header with port number
        $request->initialize([], [], [], [], [], ['HTTP_HOST' => 'www.example.com:8080']);
        $this->assertEquals('www.example.com', $request->getHost(), '->getHost() from Host Header with port number');

        // Server values
        $request->initialize([], [], [], [], [], ['SERVER_NAME' => 'www.example.com']);
        $this->assertEquals('www.example.com', $request->getHost(), '->getHost() from server name');

        $request->initialize([], [], [], [], [], ['SERVER_NAME' => 'www.example.com', 'HTTP_HOST' => 'www.host.com']);
        $this->assertEquals('www.host.com', $request->getHost(), '->getHost() value from Host header has priority over SERVER_NAME ');
    }

    public function test_get_port()
    {
        $request = Request::create('http://example.com', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ]);
        $port = $request->getPort();

        $this->assertEquals(80, $port, 'Without trusted proxies FORWARDED_PROTO and FORWARDED_PORT are ignored.');

        Request::setTrustedProxies(['1.1.1.1']);
        $request = Request::create('http://example.com', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '8443',
        ]);
        $this->assertEquals(80, $request->getPort(), 'With PROTO and PORT on untrusted connection server value takes precedence.');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $this->assertEquals(8443, $request->getPort(), 'With PROTO and PORT set PORT takes precedence.');

        $request = Request::create('http://example.com', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);
        $this->assertEquals(80, $request->getPort(), 'With only PROTO set getPort() ignores trusted headers on untrusted connection.');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $this->assertEquals(443, $request->getPort(), 'With only PROTO set getPort() defaults to 443.');

        $request = Request::create('http://example.com', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'http',
        ]);
        $this->assertEquals(80, $request->getPort(), 'If X_FORWARDED_PROTO is set to HTTP getPort() ignores trusted headers on untrusted connection.');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $this->assertEquals(80, $request->getPort(), 'If X_FORWARDED_PROTO is set to HTTP getPort() returns port of the original request.');

        $request = Request::create('http://example.com', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'On',
        ]);
        $this->assertEquals(80, $request->getPort(), 'With only PROTO set and value is On, getPort() ignores trusted headers on untrusted connection.');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $this->assertEquals(443, $request->getPort(), 'With only PROTO set and value is On, getPort() defaults to 443.');

        $request = Request::create('http://example.com', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => '1',
        ]);
        $this->assertEquals(80, $request->getPort(), 'With only PROTO set and value is 1, getPort() ignores trusted headers on untrusted connection.');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $this->assertEquals(443, $request->getPort(), 'With only PROTO set and value is 1, getPort() defaults to 443.');

        $request = Request::create('http://example.com', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'something-else',
        ]);
        $port = $request->getPort();
        $this->assertEquals(80, $port, 'With only PROTO set and value is not recognized, getPort() defaults to 80.');

        Request::setTrustedProxies([]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_get_host_with_fake_http_host_value()
    {
        $request = new Request;
        $request->initialize([], [], [], [], [], ['HTTP_HOST' => 'www.host.com?query=string']);
        $request->getHost();
    }

    public function test_get_set_method()
    {
        $request = new Request;

        $this->assertEquals('GET', $request->getMethod(), '->getMethod() returns GET if no method is defined');

        $request->setMethod('get');
        $this->assertEquals('GET', $request->getMethod(), '->getMethod() returns an uppercased string');

        $request->setMethod('PURGE');
        $this->assertEquals('PURGE', $request->getMethod(), '->getMethod() returns the method even if it is not a standard one');

        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod(), '->getMethod() returns the method POST if no _method is defined');

        $request->setMethod('POST');
        $request->request->set('_method', 'purge');
        $this->assertEquals('POST', $request->getMethod(), '->getMethod() does not return the method from _method if defined and POST but support not enabled');

        $request = new Request;
        $request->setMethod('POST');
        $request->request->set('_method', 'purge');

        $this->assertFalse(Request::getHttpMethodParameterOverride(), 'httpMethodParameterOverride should be disabled by default');

        Request::enableHttpMethodParameterOverride();

        $this->assertTrue(Request::getHttpMethodParameterOverride(), 'httpMethodParameterOverride should be enabled now but it is not');

        $this->assertEquals('PURGE', $request->getMethod(), '->getMethod() returns the method from _method if defined and POST');
        $this->disableHttpMethodParameterOverride();

        $request = new Request;
        $request->setMethod('POST');
        $request->query->set('_method', 'purge');
        $this->assertEquals('POST', $request->getMethod(), '->getMethod() does not return the method from _method if defined and POST but support not enabled');

        $request = new Request;
        $request->setMethod('POST');
        $request->query->set('_method', 'purge');
        Request::enableHttpMethodParameterOverride();
        $this->assertEquals('PURGE', $request->getMethod(), '->getMethod() returns the method from _method if defined and POST');
        $this->disableHttpMethodParameterOverride();

        $request = new Request;
        $request->setMethod('POST');
        $request->headers->set('X-HTTP-METHOD-OVERRIDE', 'delete');
        $this->assertEquals('DELETE', $request->getMethod(), '->getMethod() returns the method from X-HTTP-Method-Override even though _method is set if defined and POST');

        $request = new Request;
        $request->setMethod('POST');
        $request->headers->set('X-HTTP-METHOD-OVERRIDE', 'delete');
        $this->assertEquals('DELETE', $request->getMethod(), '->getMethod() returns the method from X-HTTP-Method-Override if defined and POST');
    }

    /**
     * @dataProvider testGetClientIpsProvider
     */
    public function test_get_client_ip($expected, $remoteAddr, $httpForwardedFor, $trustedProxies)
    {
        $request = $this->getRequestInstanceForClientIpTests($remoteAddr, $httpForwardedFor, $trustedProxies);

        $this->assertEquals($expected[0], $request->getClientIp());

        Request::setTrustedProxies([]);
    }

    /**
     * @dataProvider testGetClientIpsProvider
     */
    public function test_get_client_ips($expected, $remoteAddr, $httpForwardedFor, $trustedProxies)
    {
        $request = $this->getRequestInstanceForClientIpTests($remoteAddr, $httpForwardedFor, $trustedProxies);

        $this->assertEquals($expected, $request->getClientIps());

        Request::setTrustedProxies([]);
    }

    /**
     * @dataProvider testGetClientIpsForwardedProvider
     */
    public function test_get_client_ips_forwarded($expected, $remoteAddr, $httpForwarded, $trustedProxies)
    {
        $request = $this->getRequestInstanceForClientIpsForwardedTests($remoteAddr, $httpForwarded, $trustedProxies);

        $this->assertEquals($expected, $request->getClientIps());

        Request::setTrustedProxies([]);
    }

    public function test_get_client_ips_forwarded_provider()
    {
        //              $expected                                  $remoteAddr  $httpForwarded                                       $trustedProxies
        return [
            [['127.0.0.1'],                              '127.0.0.1', 'for="_gazonk"',                                      null],
            [['127.0.0.1'],                              '127.0.0.1', 'for="_gazonk"',                                      ['127.0.0.1']],
            [['88.88.88.88'],                            '127.0.0.1', 'for="88.88.88.88:80"',                               ['127.0.0.1']],
            [['192.0.2.60'],                             '::1',       'for=192.0.2.60;proto=http;by=203.0.113.43',          ['::1']],
            [['2620:0:1cfe:face:b00c::3', '192.0.2.43'], '::1',       'for=192.0.2.43, for=2620:0:1cfe:face:b00c::3',       ['::1']],
            [['2001:db8:cafe::17'],                      '::1',       'for="[2001:db8:cafe::17]:4711',                      ['::1']],
        ];
    }

    public function test_get_client_ips_provider()
    {
        //        $expected                   $remoteAddr                $httpForwardedFor            $trustedProxies
        return [
            // simple IPv4
            [['88.88.88.88'],              '88.88.88.88',              null,                        null],
            // trust the IPv4 remote addr
            [['88.88.88.88'],              '88.88.88.88',              null,                        ['88.88.88.88']],

            // simple IPv6
            [['::1'],                      '::1',                      null,                        null],
            // trust the IPv6 remote addr
            [['::1'],                      '::1',                      null,                        ['::1']],

            // forwarded for with remote IPv4 addr not trusted
            [['127.0.0.1'],                '127.0.0.1',                '88.88.88.88',               null],
            // forwarded for with remote IPv4 addr trusted
            [['88.88.88.88'],              '127.0.0.1',                '88.88.88.88',               ['127.0.0.1']],
            // forwarded for with remote IPv4 and all FF addrs trusted
            [['88.88.88.88'],              '127.0.0.1',                '88.88.88.88',               ['127.0.0.1', '88.88.88.88']],
            // forwarded for with remote IPv4 range trusted
            [['88.88.88.88'],              '123.45.67.89',             '88.88.88.88',               ['123.45.67.0/24']],

            // forwarded for with remote IPv6 addr not trusted
            [['1620:0:1cfe:face:b00c::3'], '1620:0:1cfe:face:b00c::3', '2620:0:1cfe:face:b00c::3',  null],
            // forwarded for with remote IPv6 addr trusted
            [['2620:0:1cfe:face:b00c::3'], '1620:0:1cfe:face:b00c::3', '2620:0:1cfe:face:b00c::3',  ['1620:0:1cfe:face:b00c::3']],
            // forwarded for with remote IPv6 range trusted
            [['88.88.88.88'],              '2a01:198:603:0:396e:4789:8e99:890f', '88.88.88.88',     ['2a01:198:603:0::/65']],

            // multiple forwarded for with remote IPv4 addr trusted
            [['88.88.88.88', '87.65.43.21', '127.0.0.1'], '123.45.67.89', '127.0.0.1, 87.65.43.21, 88.88.88.88', ['123.45.67.89']],
            // multiple forwarded for with remote IPv4 addr and some reverse proxies trusted
            [['87.65.43.21', '127.0.0.1'], '123.45.67.89',             '127.0.0.1, 87.65.43.21, 88.88.88.88', ['123.45.67.89', '88.88.88.88']],
            // multiple forwarded for with remote IPv4 addr and some reverse proxies trusted but in the middle
            [['88.88.88.88', '127.0.0.1'], '123.45.67.89',             '127.0.0.1, 87.65.43.21, 88.88.88.88', ['123.45.67.89', '87.65.43.21']],
            // multiple forwarded for with remote IPv4 addr and all reverse proxies trusted
            [['127.0.0.1'],                '123.45.67.89',             '127.0.0.1, 87.65.43.21, 88.88.88.88', ['123.45.67.89', '87.65.43.21', '88.88.88.88', '127.0.0.1']],

            // multiple forwarded for with remote IPv6 addr trusted
            [['2620:0:1cfe:face:b00c::3', '3620:0:1cfe:face:b00c::3'], '1620:0:1cfe:face:b00c::3', '3620:0:1cfe:face:b00c::3,2620:0:1cfe:face:b00c::3', ['1620:0:1cfe:face:b00c::3']],
            // multiple forwarded for with remote IPv6 addr and some reverse proxies trusted
            [['3620:0:1cfe:face:b00c::3'], '1620:0:1cfe:face:b00c::3', '3620:0:1cfe:face:b00c::3,2620:0:1cfe:face:b00c::3', ['1620:0:1cfe:face:b00c::3', '2620:0:1cfe:face:b00c::3']],
            // multiple forwarded for with remote IPv4 addr and some reverse proxies trusted but in the middle
            [['2620:0:1cfe:face:b00c::3', '4620:0:1cfe:face:b00c::3'], '1620:0:1cfe:face:b00c::3', '4620:0:1cfe:face:b00c::3,3620:0:1cfe:face:b00c::3,2620:0:1cfe:face:b00c::3', ['1620:0:1cfe:face:b00c::3', '3620:0:1cfe:face:b00c::3']],

            // client IP with port
            [['88.88.88.88'], '127.0.0.1', '88.88.88.88:12345, 127.0.0.1', ['127.0.0.1']],

            // invalid forwarded IP is ignored
            [['88.88.88.88'], '127.0.0.1', 'unknown,88.88.88.88', ['127.0.0.1']],
            [['88.88.88.88'], '127.0.0.1', '}__test|O:21:&quot;JDatabaseDriverMysqli&quot;:3:{s:2,88.88.88.88', ['127.0.0.1']],
        ];
    }

    /**
     * @expectedException \Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException
     *
     * @dataProvider testGetClientIpsWithConflictingHeadersProvider
     */
    public function test_get_client_ips_with_conflicting_headers($httpForwarded, $httpXForwardedFor)
    {
        $request = new Request;

        $server = [
            'REMOTE_ADDR' => '88.88.88.88',
            'HTTP_FORWARDED' => $httpForwarded,
            'HTTP_X_FORWARDED_FOR' => $httpXForwardedFor,
        ];

        Request::setTrustedProxies(['88.88.88.88']);

        $request->initialize([], [], [], [], [], $server);

        $request->getClientIps();
    }

    public function test_get_client_ips_with_conflicting_headers_provider()
    {
        //        $httpForwarded                   $httpXForwardedFor
        return [
            ['for=87.65.43.21',                 '192.0.2.60'],
            ['for=87.65.43.21, for=192.0.2.60', '192.0.2.60'],
            ['for=192.0.2.60',                  '192.0.2.60,87.65.43.21'],
            ['for="::face", for=192.0.2.60',    '192.0.2.60,192.0.2.43'],
            ['for=87.65.43.21, for=192.0.2.60', '192.0.2.60,87.65.43.21'],
        ];
    }

    /**
     * @dataProvider testGetClientIpsWithAgreeingHeadersProvider
     */
    public function test_get_client_ips_with_agreeing_headers($httpForwarded, $httpXForwardedFor)
    {
        $request = new Request;

        $server = [
            'REMOTE_ADDR' => '88.88.88.88',
            'HTTP_FORWARDED' => $httpForwarded,
            'HTTP_X_FORWARDED_FOR' => $httpXForwardedFor,
        ];

        Request::setTrustedProxies(['88.88.88.88']);

        $request->initialize([], [], [], [], [], $server);

        $request->getClientIps();

        Request::setTrustedProxies([]);
    }

    public function test_get_client_ips_with_agreeing_headers_provider()
    {
        //        $httpForwarded                               $httpXForwardedFor
        return [
            ['for="192.0.2.60"',                          '192.0.2.60'],
            ['for=192.0.2.60, for=87.65.43.21',           '192.0.2.60,87.65.43.21'],
            ['for="[::face]", for=192.0.2.60',            '::face,192.0.2.60'],
            ['for="192.0.2.60:80"',                       '192.0.2.60'],
            ['for=192.0.2.60;proto=http;by=203.0.113.43', '192.0.2.60'],
            ['for="[2001:db8:cafe::17]:4711"',            '2001:db8:cafe::17'],
        ];
    }

    public function test_get_content_works_twice_in_default_mode()
    {
        $req = new Request;
        $this->assertEquals('', $req->getContent());
        $this->assertEquals('', $req->getContent());
    }

    public function test_get_content_returns_resource()
    {
        $req = new Request;
        $retval = $req->getContent(true);
        $this->assertInternalType('resource', $retval);
        $this->assertEquals('', fread($retval, 1));
        $this->assertTrue(feof($retval));
    }

    public function test_get_content_returns_resource_when_content_set_in_constructor()
    {
        $req = new Request([], [], [], [], [], [], 'MyContent');
        $resource = $req->getContent(true);

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('MyContent', stream_get_contents($resource));
    }

    public function test_content_as_resource()
    {
        $resource = fopen('php://memory', 'r+');
        fwrite($resource, 'My other content');
        rewind($resource);

        $req = new Request([], [], [], [], [], [], $resource);
        $this->assertEquals('My other content', stream_get_contents($req->getContent(true)));
        $this->assertEquals('My other content', $req->getContent());
    }

    /**
     * @expectedException \LogicException
     *
     * @dataProvider getContentCantBeCalledTwiceWithResourcesProvider
     */
    public function test_get_content_cant_be_called_twice_with_resources($first, $second)
    {
        if (PHP_VERSION_ID >= 50600) {
            $this->markTestSkipped('PHP >= 5.6 allows to open php://input several times.');
        }

        $req = new Request;
        $req->getContent($first);
        $req->getContent($second);
    }

    /**
     * @dataProvider getContentCantBeCalledTwiceWithResourcesProvider
     *
     * @requires PHP 5.6
     */
    public function test_get_content_can_be_called_twice_with_resources($first, $second)
    {
        $req = new Request;
        $a = $req->getContent($first);
        $b = $req->getContent($second);

        if ($first) {
            $a = stream_get_contents($a);
        }

        if ($second) {
            $b = stream_get_contents($b);
        }

        $this->assertEquals($a, $b);
    }

    public function getContentCantBeCalledTwiceWithResourcesProvider()
    {
        return [
            'Resource then fetch' => [true, false],
            'Resource then resource' => [true, true],
        ];
    }

    public function provideOverloadedMethods()
    {
        return [
            ['PUT'],
            ['DELETE'],
            ['PATCH'],
            ['put'],
            ['delete'],
            ['patch'],

        ];
    }

    /**
     * @dataProvider provideOverloadedMethods
     */
    public function test_create_from_globals($method)
    {
        $normalizedMethod = strtoupper($method);

        $_GET['foo1'] = 'bar1';
        $_POST['foo2'] = 'bar2';
        $_COOKIE['foo3'] = 'bar3';
        $_FILES['foo4'] = ['bar4'];
        $_SERVER['foo5'] = 'bar5';

        $request = Request::createFromGlobals();
        $this->assertEquals('bar1', $request->query->get('foo1'), '::fromGlobals() uses values from $_GET');
        $this->assertEquals('bar2', $request->request->get('foo2'), '::fromGlobals() uses values from $_POST');
        $this->assertEquals('bar3', $request->cookies->get('foo3'), '::fromGlobals() uses values from $_COOKIE');
        $this->assertEquals(['bar4'], $request->files->get('foo4'), '::fromGlobals() uses values from $_FILES');
        $this->assertEquals('bar5', $request->server->get('foo5'), '::fromGlobals() uses values from $_SERVER');

        unset($_GET['foo1'], $_POST['foo2'], $_COOKIE['foo3'], $_FILES['foo4'], $_SERVER['foo5']);

        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $request = RequestContentProxy::createFromGlobals();
        $this->assertEquals($normalizedMethod, $request->getMethod());
        $this->assertEquals('mycontent', $request->request->get('content'));

        unset($_SERVER['REQUEST_METHOD'], $_SERVER['CONTENT_TYPE']);

        Request::createFromGlobals();
        Request::enableHttpMethodParameterOverride();
        $_POST['_method'] = $method;
        $_POST['foo6'] = 'bar6';
        $_SERVER['REQUEST_METHOD'] = 'PoSt';
        $request = Request::createFromGlobals();
        $this->assertEquals($normalizedMethod, $request->getMethod());
        $this->assertEquals('POST', $request->getRealMethod());
        $this->assertEquals('bar6', $request->request->get('foo6'));

        unset($_POST['_method'], $_POST['foo6'], $_SERVER['REQUEST_METHOD']);
        $this->disableHttpMethodParameterOverride();
    }

    public function test_override_globals()
    {
        $request = new Request;
        $request->initialize(['foo' => 'bar']);

        // as the Request::overrideGlobals really work, it erase $_SERVER, so we must backup it
        $server = $_SERVER;

        $request->overrideGlobals();

        $this->assertEquals(['foo' => 'bar'], $_GET);

        $request->initialize([], ['foo' => 'bar']);
        $request->overrideGlobals();

        $this->assertEquals(['foo' => 'bar'], $_POST);

        $this->assertArrayNotHasKey('HTTP_X_FORWARDED_PROTO', $_SERVER);

        $request->headers->set('X_FORWARDED_PROTO', 'https');

        Request::setTrustedProxies(['1.1.1.1']);
        $this->assertFalse($request->isSecure());
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $this->assertTrue($request->isSecure());
        Request::setTrustedProxies([]);

        $request->overrideGlobals();

        $this->assertArrayHasKey('HTTP_X_FORWARDED_PROTO', $_SERVER);

        $request->headers->set('CONTENT_TYPE', 'multipart/form-data');
        $request->headers->set('CONTENT_LENGTH', 12345);

        $request->overrideGlobals();

        $this->assertArrayHasKey('CONTENT_TYPE', $_SERVER);
        $this->assertArrayHasKey('CONTENT_LENGTH', $_SERVER);

        $request->initialize(['foo' => 'bar', 'baz' => 'foo']);
        $request->query->remove('baz');

        $request->overrideGlobals();

        $this->assertEquals(['foo' => 'bar'], $_GET);
        $this->assertEquals('foo=bar', $_SERVER['QUERY_STRING']);
        $this->assertEquals('foo=bar', $request->server->get('QUERY_STRING'));

        // restore initial $_SERVER array
        $_SERVER = $server;
    }

    public function test_get_script_name()
    {
        $request = new Request;
        $this->assertEquals('', $request->getScriptName());

        $server = [];
        $server['SCRIPT_NAME'] = '/index.php';

        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('/index.php', $request->getScriptName());

        $server = [];
        $server['ORIG_SCRIPT_NAME'] = '/frontend.php';
        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('/frontend.php', $request->getScriptName());

        $server = [];
        $server['SCRIPT_NAME'] = '/index.php';
        $server['ORIG_SCRIPT_NAME'] = '/frontend.php';
        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('/index.php', $request->getScriptName());
    }

    public function test_get_base_path()
    {
        $request = new Request;
        $this->assertEquals('', $request->getBasePath());

        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $request->initialize([], [], [], [], [], $server);
        $this->assertEquals('', $request->getBasePath());

        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['SCRIPT_NAME'] = '/index.php';
        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('', $request->getBasePath());

        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['PHP_SELF'] = '/index.php';
        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('', $request->getBasePath());

        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['ORIG_SCRIPT_NAME'] = '/index.php';
        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('', $request->getBasePath());
    }

    public function test_get_path_info()
    {
        $request = new Request;
        $this->assertEquals('/', $request->getPathInfo());

        $server = [];
        $server['REQUEST_URI'] = '/path/info';
        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('/path/info', $request->getPathInfo());

        $server = [];
        $server['REQUEST_URI'] = '/path%20test/info';
        $request->initialize([], [], [], [], [], $server);

        $this->assertEquals('/path%20test/info', $request->getPathInfo());
    }

    public function test_get_parameter_precedence()
    {
        $request = new Request;
        $request->attributes->set('foo', 'attr');
        $request->query->set('foo', 'query');
        $request->request->set('foo', 'body');

        $this->assertSame('attr', $request->get('foo'));

        $request->attributes->remove('foo');
        $this->assertSame('query', $request->get('foo'));

        $request->query->remove('foo');
        $this->assertSame('body', $request->get('foo'));

        $request->request->remove('foo');
        $this->assertNull($request->get('foo'));
    }

    public function test_get_preferred_language()
    {
        $request = new Request;
        $this->assertNull($request->getPreferredLanguage());
        $this->assertNull($request->getPreferredLanguage([]));
        $this->assertEquals('fr', $request->getPreferredLanguage(['fr']));
        $this->assertEquals('fr', $request->getPreferredLanguage(['fr', 'en']));
        $this->assertEquals('en', $request->getPreferredLanguage(['en', 'fr']));
        $this->assertEquals('fr-ch', $request->getPreferredLanguage(['fr-ch', 'fr-fr']));

        $request = new Request;
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, en; q=0.6');
        $this->assertEquals('en', $request->getPreferredLanguage(['en', 'en-us']));

        $request = new Request;
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, en; q=0.6');
        $this->assertEquals('en', $request->getPreferredLanguage(['fr', 'en']));

        $request = new Request;
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8');
        $this->assertEquals('en', $request->getPreferredLanguage(['fr', 'en']));

        $request = new Request;
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, fr-fr; q=0.6, fr; q=0.5');
        $this->assertEquals('en', $request->getPreferredLanguage(['fr', 'en']));
    }

    public function test_is_xml_http_request()
    {
        $request = new Request;
        $this->assertFalse($request->isXmlHttpRequest());

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->assertTrue($request->isXmlHttpRequest());

        $request->headers->remove('X-Requested-With');
        $this->assertFalse($request->isXmlHttpRequest());
    }

    /**
     * @requires extension intl
     */
    public function test_intl_locale()
    {
        $request = new Request;

        $request->setDefaultLocale('fr');
        $this->assertEquals('fr', $request->getLocale());
        $this->assertEquals('fr', \Locale::getDefault());

        $request->setLocale('en');
        $this->assertEquals('en', $request->getLocale());
        $this->assertEquals('en', \Locale::getDefault());

        $request->setDefaultLocale('de');
        $this->assertEquals('en', $request->getLocale());
        $this->assertEquals('en', \Locale::getDefault());
    }

    public function test_get_charsets()
    {
        $request = new Request;
        $this->assertEquals([], $request->getCharsets());
        $request->headers->set('Accept-Charset', 'ISO-8859-1, US-ASCII, UTF-8; q=0.8, ISO-10646-UCS-2; q=0.6');
        $this->assertEquals([], $request->getCharsets()); // testing caching

        $request = new Request;
        $request->headers->set('Accept-Charset', 'ISO-8859-1, US-ASCII, UTF-8; q=0.8, ISO-10646-UCS-2; q=0.6');
        $this->assertEquals(['ISO-8859-1', 'US-ASCII', 'UTF-8', 'ISO-10646-UCS-2'], $request->getCharsets());

        $request = new Request;
        $request->headers->set('Accept-Charset', 'ISO-8859-1,utf-8;q=0.7,*;q=0.7');
        $this->assertEquals(['ISO-8859-1', 'utf-8', '*'], $request->getCharsets());
    }

    public function test_get_encodings()
    {
        $request = new Request;
        $this->assertEquals([], $request->getEncodings());
        $request->headers->set('Accept-Encoding', 'gzip,deflate,sdch');
        $this->assertEquals([], $request->getEncodings()); // testing caching

        $request = new Request;
        $request->headers->set('Accept-Encoding', 'gzip,deflate,sdch');
        $this->assertEquals(['gzip', 'deflate', 'sdch'], $request->getEncodings());

        $request = new Request;
        $request->headers->set('Accept-Encoding', 'gzip;q=0.4,deflate;q=0.9,compress;q=0.7');
        $this->assertEquals(['deflate', 'compress', 'gzip'], $request->getEncodings());
    }

    public function test_get_acceptable_content_types()
    {
        $request = new Request;
        $this->assertEquals([], $request->getAcceptableContentTypes());
        $request->headers->set('Accept', 'application/vnd.wap.wmlscriptc, text/vnd.wap.wml, application/vnd.wap.xhtml+xml, application/xhtml+xml, text/html, multipart/mixed, */*');
        $this->assertEquals([], $request->getAcceptableContentTypes()); // testing caching

        $request = new Request;
        $request->headers->set('Accept', 'application/vnd.wap.wmlscriptc, text/vnd.wap.wml, application/vnd.wap.xhtml+xml, application/xhtml+xml, text/html, multipart/mixed, */*');
        $this->assertEquals(['application/vnd.wap.wmlscriptc', 'text/vnd.wap.wml', 'application/vnd.wap.xhtml+xml', 'application/xhtml+xml', 'text/html', 'multipart/mixed', '*/*'], $request->getAcceptableContentTypes());
    }

    public function test_get_languages()
    {
        $request = new Request;
        $this->assertEquals([], $request->getLanguages());

        $request = new Request;
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, en; q=0.6');
        $this->assertEquals(['zh', 'en_US', 'en'], $request->getLanguages());
        $this->assertEquals(['zh', 'en_US', 'en'], $request->getLanguages());

        $request = new Request;
        $request->headers->set('Accept-language', 'zh, en-us; q=0.6, en; q=0.8');
        $this->assertEquals(['zh', 'en', 'en_US'], $request->getLanguages()); // Test out of order qvalues

        $request = new Request;
        $request->headers->set('Accept-language', 'zh, en, en-us');
        $this->assertEquals(['zh', 'en', 'en_US'], $request->getLanguages()); // Test equal weighting without qvalues

        $request = new Request;
        $request->headers->set('Accept-language', 'zh; q=0.6, en, en-us; q=0.6');
        $this->assertEquals(['en', 'zh', 'en_US'], $request->getLanguages()); // Test equal weighting with qvalues

        $request = new Request;
        $request->headers->set('Accept-language', 'zh, i-cherokee; q=0.6');
        $this->assertEquals(['zh', 'cherokee'], $request->getLanguages());
    }

    public function test_get_request_format()
    {
        $request = new Request;
        $this->assertEquals('html', $request->getRequestFormat());

        $request = new Request;
        $this->assertNull($request->getRequestFormat(null));

        $request = new Request;
        $this->assertNull($request->setRequestFormat('foo'));
        $this->assertEquals('foo', $request->getRequestFormat(null));

        $request = new Request(['_format' => 'foo']);
        $this->assertEquals('html', $request->getRequestFormat());
    }

    public function test_has_session()
    {
        $request = new Request;

        $this->assertFalse($request->hasSession());
        $request->setSession(new Session(new MockArraySessionStorage));
        $this->assertTrue($request->hasSession());
    }

    public function test_get_session()
    {
        $request = new Request;

        $request->setSession(new Session(new MockArraySessionStorage));
        $this->assertTrue($request->hasSession());

        $session = $request->getSession();
        $this->assertObjectHasAttribute('storage', $session);
        $this->assertObjectHasAttribute('flashName', $session);
        $this->assertObjectHasAttribute('attributeName', $session);
    }

    public function test_has_previous_session()
    {
        $request = new Request;

        $this->assertFalse($request->hasPreviousSession());
        $request->cookies->set('MOCKSESSID', 'foo');
        $this->assertFalse($request->hasPreviousSession());
        $request->setSession(new Session(new MockArraySessionStorage));
        $this->assertTrue($request->hasPreviousSession());
    }

    public function test_to_string()
    {
        $request = new Request;

        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, en; q=0.6');

        $this->assertContains('Accept-Language: zh, en-us; q=0.8, en; q=0.6', $request->__toString());
    }

    public function test_is_method()
    {
        $request = new Request;
        $request->setMethod('POST');
        $this->assertTrue($request->isMethod('POST'));
        $this->assertTrue($request->isMethod('post'));
        $this->assertFalse($request->isMethod('GET'));
        $this->assertFalse($request->isMethod('get'));

        $request->setMethod('GET');
        $this->assertTrue($request->isMethod('GET'));
        $this->assertTrue($request->isMethod('get'));
        $this->assertFalse($request->isMethod('POST'));
        $this->assertFalse($request->isMethod('post'));
    }

    /**
     * @dataProvider getBaseUrlData
     */
    public function test_get_base_url($uri, $server, $expectedBaseUrl, $expectedPathInfo)
    {
        $request = Request::create($uri, 'GET', [], [], [], $server);

        $this->assertSame($expectedBaseUrl, $request->getBaseUrl(), 'baseUrl');
        $this->assertSame($expectedPathInfo, $request->getPathInfo(), 'pathInfo');
    }

    public function getBaseUrlData()
    {
        return [
            [
                '/fruit/strawberry/1234index.php/blah',
                [
                    'SCRIPT_FILENAME' => 'E:/Sites/cc-new/public_html/fruit/index.php',
                    'SCRIPT_NAME' => '/fruit/index.php',
                    'PHP_SELF' => '/fruit/index.php',
                ],
                '/fruit',
                '/strawberry/1234index.php/blah',
            ],
            [
                '/fruit/strawberry/1234index.php/blah',
                [
                    'SCRIPT_FILENAME' => 'E:/Sites/cc-new/public_html/index.php',
                    'SCRIPT_NAME' => '/index.php',
                    'PHP_SELF' => '/index.php',
                ],
                '',
                '/fruit/strawberry/1234index.php/blah',
            ],
            [
                '/foo%20bar/',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME' => '/foo bar/app.php',
                    'PHP_SELF' => '/foo bar/app.php',
                ],
                '/foo%20bar',
                '/',
            ],
            [
                '/foo%20bar/home',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME' => '/foo bar/app.php',
                    'PHP_SELF' => '/foo bar/app.php',
                ],
                '/foo%20bar',
                '/home',
            ],
            [
                '/foo%20bar/app.php/home',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME' => '/foo bar/app.php',
                    'PHP_SELF' => '/foo bar/app.php',
                ],
                '/foo%20bar/app.php',
                '/home',
            ],
            [
                '/foo%20bar/app.php/home%3Dbaz',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME' => '/foo bar/app.php',
                    'PHP_SELF' => '/foo bar/app.php',
                ],
                '/foo%20bar/app.php',
                '/home%3Dbaz',
            ],
            [
                '/foo/bar+baz',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo/app.php',
                    'SCRIPT_NAME' => '/foo/app.php',
                    'PHP_SELF' => '/foo/app.php',
                ],
                '/foo',
                '/bar+baz',
            ],
        ];
    }

    /**
     * @dataProvider urlencodedStringPrefixData
     */
    public function test_urlencoded_string_prefix($string, $prefix, $expect)
    {
        $request = new Request;

        $me = new \ReflectionMethod($request, 'getUrlencodedPrefix');
        $me->setAccessible(true);

        $this->assertSame($expect, $me->invoke($request, $string, $prefix));
    }

    public function urlencodedStringPrefixData()
    {
        return [
            ['foo', 'foo', 'foo'],
            ['fo%6f', 'foo', 'fo%6f'],
            ['foo/bar', 'foo', 'foo'],
            ['fo%6f/bar', 'foo', 'fo%6f'],
            ['f%6f%6f/bar', 'foo', 'f%6f%6f'],
            ['%66%6F%6F/bar', 'foo', '%66%6F%6F'],
            ['fo+o/bar', 'fo+o', 'fo+o'],
            ['fo%2Bo/bar', 'fo+o', 'fo%2Bo'],
        ];
    }

    private function disableHttpMethodParameterOverride()
    {
        $class = new \ReflectionClass('Symfony\\Component\\HttpFoundation\\Request');
        $property = $class->getProperty('httpMethodParameterOverride');
        $property->setAccessible(true);
        $property->setValue(false);
    }

    private function getRequestInstanceForClientIpTests($remoteAddr, $httpForwardedFor, $trustedProxies)
    {
        $request = new Request;

        $server = ['REMOTE_ADDR' => $remoteAddr];
        if ($httpForwardedFor !== null) {
            $server['HTTP_X_FORWARDED_FOR'] = $httpForwardedFor;
        }

        if ($trustedProxies) {
            Request::setTrustedProxies($trustedProxies);
        }

        $request->initialize([], [], [], [], [], $server);

        return $request;
    }

    private function getRequestInstanceForClientIpsForwardedTests($remoteAddr, $httpForwarded, $trustedProxies)
    {
        $request = new Request;

        $server = ['REMOTE_ADDR' => $remoteAddr];

        if ($httpForwarded !== null) {
            $server['HTTP_FORWARDED'] = $httpForwarded;
        }

        if ($trustedProxies) {
            Request::setTrustedProxies($trustedProxies);
        }

        $request->initialize([], [], [], [], [], $server);

        return $request;
    }

    public function test_trusted_proxies()
    {
        $request = Request::create('http://example.com/');
        $request->server->set('REMOTE_ADDR', '3.3.3.3');
        $request->headers->set('X_FORWARDED_FOR', '1.1.1.1, 2.2.2.2');
        $request->headers->set('X_FORWARDED_HOST', 'foo.example.com, real.example.com:8080');
        $request->headers->set('X_FORWARDED_PROTO', 'https');
        $request->headers->set('X_FORWARDED_PORT', 443);
        $request->headers->set('X_MY_FOR', '3.3.3.3, 4.4.4.4');
        $request->headers->set('X_MY_HOST', 'my.example.com');
        $request->headers->set('X_MY_PROTO', 'http');
        $request->headers->set('X_MY_PORT', 81);

        // no trusted proxies
        $this->assertEquals('3.3.3.3', $request->getClientIp());
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());
        $this->assertFalse($request->isSecure());

        // disabling proxy trusting
        Request::setTrustedProxies([]);
        $this->assertEquals('3.3.3.3', $request->getClientIp());
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());
        $this->assertFalse($request->isSecure());

        // request is forwarded by a non-trusted proxy
        Request::setTrustedProxies(['2.2.2.2']);
        $this->assertEquals('3.3.3.3', $request->getClientIp());
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());
        $this->assertFalse($request->isSecure());

        // trusted proxy via setTrustedProxies()
        Request::setTrustedProxies(['3.3.3.3', '2.2.2.2']);
        $this->assertEquals('1.1.1.1', $request->getClientIp());
        $this->assertEquals('real.example.com', $request->getHost());
        $this->assertEquals(443, $request->getPort());
        $this->assertTrue($request->isSecure());

        // trusted proxy via setTrustedProxies()
        Request::setTrustedProxies(['3.3.3.4', '2.2.2.2']);
        $this->assertEquals('3.3.3.3', $request->getClientIp());
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());
        $this->assertFalse($request->isSecure());

        // check various X_FORWARDED_PROTO header values
        Request::setTrustedProxies(['3.3.3.3', '2.2.2.2']);
        $request->headers->set('X_FORWARDED_PROTO', 'ssl');
        $this->assertTrue($request->isSecure());

        $request->headers->set('X_FORWARDED_PROTO', 'https, http');
        $this->assertTrue($request->isSecure());

        // custom header names
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_IP, 'X_MY_FOR');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_HOST, 'X_MY_HOST');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PORT, 'X_MY_PORT');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PROTO, 'X_MY_PROTO');
        $this->assertEquals('4.4.4.4', $request->getClientIp());
        $this->assertEquals('my.example.com', $request->getHost());
        $this->assertEquals(81, $request->getPort());
        $this->assertFalse($request->isSecure());

        // disabling via empty header names
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_IP, null);
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_HOST, null);
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PORT, null);
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PROTO, null);
        $this->assertEquals('3.3.3.3', $request->getClientIp());
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());
        $this->assertFalse($request->isSecure());

        // reset
        Request::setTrustedProxies([]);
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_IP, 'X_FORWARDED_FOR');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_HOST, 'X_FORWARDED_HOST');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PORT, 'X_FORWARDED_PORT');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PROTO, 'X_FORWARDED_PROTO');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_set_trusted_proxies_invalid_header_name()
    {
        Request::create('http://example.com/');
        Request::setTrustedHeaderName('bogus name', 'X_MY_FOR');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_get_trusted_proxies_invalid_header_name()
    {
        Request::create('http://example.com/');
        Request::getTrustedHeaderName('bogus name');
    }

    /**
     * @dataProvider iisRequestUriProvider
     */
    public function test_iis_request_uri($headers, $server, $expectedRequestUri)
    {
        $request = new Request;
        $request->headers->replace($headers);
        $request->server->replace($server);

        $this->assertEquals($expectedRequestUri, $request->getRequestUri(), '->getRequestUri() is correct');

        $subRequestUri = '/bar/foo';
        $subRequest = Request::create($subRequestUri, 'get', [], [], [], $request->server->all());
        $this->assertEquals($subRequestUri, $subRequest->getRequestUri(), '->getRequestUri() is correct in sub request');
    }

    public function iisRequestUriProvider()
    {
        return [
            [
                [
                    'X_ORIGINAL_URL' => '/foo/bar',
                ],
                [],
                '/foo/bar',
            ],
            [
                [
                    'X_REWRITE_URL' => '/foo/bar',
                ],
                [],
                '/foo/bar',
            ],
            [
                [],
                [
                    'IIS_WasUrlRewritten' => '1',
                    'UNENCODED_URL' => '/foo/bar',
                ],
                '/foo/bar',
            ],
            [
                [
                    'X_ORIGINAL_URL' => '/foo/bar',
                ],
                [
                    'HTTP_X_ORIGINAL_URL' => '/foo/bar',
                ],
                '/foo/bar',
            ],
            [
                [
                    'X_ORIGINAL_URL' => '/foo/bar',
                ],
                [
                    'IIS_WasUrlRewritten' => '1',
                    'UNENCODED_URL' => '/foo/bar',
                ],
                '/foo/bar',
            ],
            [
                [
                    'X_ORIGINAL_URL' => '/foo/bar',
                ],
                [
                    'HTTP_X_ORIGINAL_URL' => '/foo/bar',
                    'IIS_WasUrlRewritten' => '1',
                    'UNENCODED_URL' => '/foo/bar',
                ],
                '/foo/bar',
            ],
            [
                [],
                [
                    'ORIG_PATH_INFO' => '/foo/bar',
                ],
                '/foo/bar',
            ],
            [
                [],
                [
                    'ORIG_PATH_INFO' => '/foo/bar',
                    'QUERY_STRING' => 'foo=bar',
                ],
                '/foo/bar?foo=bar',
            ],
        ];
    }

    public function test_trusted_hosts()
    {
        // create a request
        $request = Request::create('/');

        // no trusted host set -> no host check
        $request->headers->set('host', 'evil.com');
        $this->assertEquals('evil.com', $request->getHost());

        // add a trusted domain and all its subdomains
        Request::setTrustedHosts(['^([a-z]{9}\.)?trusted\.com$']);

        // untrusted host
        $request->headers->set('host', 'evil.com');
        try {
            $request->getHost();
            $this->fail('Request::getHost() should throw an exception when host is not trusted.');
        } catch (\UnexpectedValueException $e) {
            $this->assertEquals('Untrusted Host "evil.com"', $e->getMessage());
        }

        // trusted hosts
        $request->headers->set('host', 'trusted.com');
        $this->assertEquals('trusted.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());

        $request->server->set('HTTPS', true);
        $request->headers->set('host', 'trusted.com');
        $this->assertEquals('trusted.com', $request->getHost());
        $this->assertEquals(443, $request->getPort());
        $request->server->set('HTTPS', false);

        $request->headers->set('host', 'trusted.com:8000');
        $this->assertEquals('trusted.com', $request->getHost());
        $this->assertEquals(8000, $request->getPort());

        $request->headers->set('host', 'subdomain.trusted.com');
        $this->assertEquals('subdomain.trusted.com', $request->getHost());

        // reset request for following tests
        Request::setTrustedHosts([]);
    }

    public function test_factory()
    {
        Request::setFactory(function (array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null) {
            return new NewRequest;
        });

        $this->assertEquals('foo', Request::create('/')->getFoo());

        Request::setFactory(null);
    }

    /**
     * @dataProvider getLongHostNames
     */
    public function test_very_long_hosts($host)
    {
        $start = microtime(true);

        $request = Request::create('/');
        $request->headers->set('host', $host);
        $this->assertEquals($host, $request->getHost());
        $this->assertLessThan(5, microtime(true) - $start);
    }

    /**
     * @dataProvider getHostValidities
     */
    public function test_host_validity($host, $isValid, $expectedHost = null, $expectedPort = null)
    {
        $request = Request::create('/');
        $request->headers->set('host', $host);

        if ($isValid) {
            $this->assertSame($expectedHost ?: $host, $request->getHost());
            if ($expectedPort) {
                $this->assertSame($expectedPort, $request->getPort());
            }
        } else {
            $this->setExpectedException('UnexpectedValueException', 'Invalid Host');
            $request->getHost();
        }
    }

    public function getHostValidities()
    {
        return [
            ['.a', false],
            ['a..', false],
            ['a.', true],
            ["\xE9", false],
            ['[::1]', true],
            ['[::1]:80', true, '[::1]', 80],
            [str_repeat('.', 101), false],
        ];
    }

    public function getLongHostNames()
    {
        return [
            ['a'.str_repeat('.a', 40000)],
            [str_repeat(':', 101)],
        ];
    }

    /**
     * @dataProvider methodSafeProvider
     */
    public function test_method_safe($method, $safe)
    {
        $request = new Request;
        $request->setMethod($method);
        $this->assertEquals($safe, $request->isMethodSafe());
    }

    public function methodSafeProvider()
    {
        return [
            ['HEAD', true],
            ['GET', true],
            ['POST', false],
            ['PUT', false],
            ['PATCH', false],
            ['DELETE', false],
            ['PURGE', false],
            ['OPTIONS', true],
            ['TRACE', true],
            ['CONNECT', false],
        ];
    }
}

class RequestContentProxy extends Request
{
    public function getContent($asResource = false)
    {
        return http_build_query(['_method' => 'PUT', 'content' => 'mycontent']);
    }
}

class NewRequest extends Request
{
    public function getFoo()
    {
        return 'foo';
    }
}
