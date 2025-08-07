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
use Symfony\Component\HttpFoundation\Response;

/**
 * @group time-sensitive
 */
class ResponseTest extends ResponseTestCase
{
    public function test_create()
    {
        $response = Response::create('foo', 301, ['Foo' => 'bar']);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('bar', $response->headers->get('foo'));
    }

    public function test_to_string()
    {
        $response = new Response;
        $response = explode("\r\n", $response);
        $this->assertEquals('HTTP/1.0 200 OK', $response[0]);
        $this->assertEquals('Cache-Control: no-cache', $response[1]);
    }

    public function test_clone()
    {
        $response = new Response;
        $responseClone = clone $response;
        $this->assertEquals($response, $responseClone);
    }

    public function test_send_headers()
    {
        $response = new Response;
        $headers = $response->sendHeaders();
        $this->assertObjectHasAttribute('headers', $headers);
        $this->assertObjectHasAttribute('content', $headers);
        $this->assertObjectHasAttribute('version', $headers);
        $this->assertObjectHasAttribute('statusCode', $headers);
        $this->assertObjectHasAttribute('statusText', $headers);
        $this->assertObjectHasAttribute('charset', $headers);
    }

    public function test_send()
    {
        $response = new Response;
        $responseSend = $response->send();
        $this->assertObjectHasAttribute('headers', $responseSend);
        $this->assertObjectHasAttribute('content', $responseSend);
        $this->assertObjectHasAttribute('version', $responseSend);
        $this->assertObjectHasAttribute('statusCode', $responseSend);
        $this->assertObjectHasAttribute('statusText', $responseSend);
        $this->assertObjectHasAttribute('charset', $responseSend);
    }

    public function test_get_charset()
    {
        $response = new Response;
        $charsetOrigin = 'UTF-8';
        $response->setCharset($charsetOrigin);
        $charset = $response->getCharset();
        $this->assertEquals($charsetOrigin, $charset);
    }

    public function test_is_cacheable()
    {
        $response = new Response;
        $this->assertFalse($response->isCacheable());
    }

    public function test_is_cacheable_with_error_code()
    {
        $response = new Response('', 500);
        $this->assertFalse($response->isCacheable());
    }

    public function test_is_cacheable_with_no_store_directive()
    {
        $response = new Response;
        $response->headers->set('cache-control', 'private');
        $this->assertFalse($response->isCacheable());
    }

    public function test_is_cacheable_with_set_ttl()
    {
        $response = new Response;
        $response->setTtl(10);
        $this->assertTrue($response->isCacheable());
    }

    public function test_must_revalidate()
    {
        $response = new Response;
        $this->assertFalse($response->mustRevalidate());
    }

    public function test_must_revalidate_with_must_revalidate_cache_control_header()
    {
        $response = new Response;
        $response->headers->set('cache-control', 'must-revalidate');

        $this->assertTrue($response->mustRevalidate());
    }

    public function test_must_revalidate_with_proxy_revalidate_cache_control_header()
    {
        $response = new Response;
        $response->headers->set('cache-control', 'proxy-revalidate');

        $this->assertTrue($response->mustRevalidate());
    }

    public function test_set_not_modified()
    {
        $response = new Response;
        $modified = $response->setNotModified();
        $this->assertObjectHasAttribute('headers', $modified);
        $this->assertObjectHasAttribute('content', $modified);
        $this->assertObjectHasAttribute('version', $modified);
        $this->assertObjectHasAttribute('statusCode', $modified);
        $this->assertObjectHasAttribute('statusText', $modified);
        $this->assertObjectHasAttribute('charset', $modified);
        $this->assertEquals(304, $modified->getStatusCode());
    }

    public function test_is_successful()
    {
        $response = new Response;
        $this->assertTrue($response->isSuccessful());
    }

    public function test_is_not_modified()
    {
        $response = new Response;
        $modified = $response->isNotModified(new Request);
        $this->assertFalse($modified);
    }

    public function test_is_not_modified_not_safe()
    {
        $request = Request::create('/homepage', 'POST');

        $response = new Response;
        $this->assertFalse($response->isNotModified($request));
    }

    public function test_is_not_modified_last_modified()
    {
        $before = 'Sun, 25 Aug 2013 18:32:31 GMT';
        $modified = 'Sun, 25 Aug 2013 18:33:31 GMT';
        $after = 'Sun, 25 Aug 2013 19:33:31 GMT';

        $request = new Request;
        $request->headers->set('If-Modified-Since', $modified);

        $response = new Response;

        $response->headers->set('Last-Modified', $modified);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('Last-Modified', $before);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('Last-Modified', $after);
        $this->assertFalse($response->isNotModified($request));

        $response->headers->set('Last-Modified', '');
        $this->assertFalse($response->isNotModified($request));
    }

    public function test_is_not_modified_etag()
    {
        $etagOne = 'randomly_generated_etag';
        $etagTwo = 'randomly_generated_etag_2';

        $request = new Request;
        $request->headers->set('if_none_match', sprintf('%s, %s, %s', $etagOne, $etagTwo, 'etagThree'));

        $response = new Response;

        $response->headers->set('ETag', $etagOne);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('ETag', $etagTwo);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('ETag', '');
        $this->assertFalse($response->isNotModified($request));
    }

    public function test_is_not_modified_last_modified_and_etag()
    {
        $before = 'Sun, 25 Aug 2013 18:32:31 GMT';
        $modified = 'Sun, 25 Aug 2013 18:33:31 GMT';
        $after = 'Sun, 25 Aug 2013 19:33:31 GMT';
        $etag = 'randomly_generated_etag';

        $request = new Request;
        $request->headers->set('if_none_match', sprintf('%s, %s', $etag, 'etagThree'));
        $request->headers->set('If-Modified-Since', $modified);

        $response = new Response;

        $response->headers->set('ETag', $etag);
        $response->headers->set('Last-Modified', $after);
        $this->assertFalse($response->isNotModified($request));

        $response->headers->set('ETag', 'non-existent-etag');
        $response->headers->set('Last-Modified', $before);
        $this->assertFalse($response->isNotModified($request));

        $response->headers->set('ETag', $etag);
        $response->headers->set('Last-Modified', $modified);
        $this->assertTrue($response->isNotModified($request));
    }

    public function test_is_not_modified_if_modified_since_and_etag_without_last_modified()
    {
        $modified = 'Sun, 25 Aug 2013 18:33:31 GMT';
        $etag = 'randomly_generated_etag';

        $request = new Request;
        $request->headers->set('if_none_match', sprintf('%s, %s', $etag, 'etagThree'));
        $request->headers->set('If-Modified-Since', $modified);

        $response = new Response;

        $response->headers->set('ETag', $etag);
        $this->assertTrue($response->isNotModified($request));

        $response->headers->set('ETag', 'non-existent-etag');
        $this->assertFalse($response->isNotModified($request));
    }

    public function test_is_validateable()
    {
        $response = new Response('', 200, ['Last-Modified' => $this->createDateTimeOneHourAgo()->format(DATE_RFC2822)]);
        $this->assertTrue($response->isValidateable(), '->isValidateable() returns true if Last-Modified is present');

        $response = new Response('', 200, ['ETag' => '"12345"']);
        $this->assertTrue($response->isValidateable(), '->isValidateable() returns true if ETag is present');

        $response = new Response;
        $this->assertFalse($response->isValidateable(), '->isValidateable() returns false when no validator is present');
    }

    public function test_get_date()
    {
        $oneHourAgo = $this->createDateTimeOneHourAgo();
        $response = new Response('', 200, ['Date' => $oneHourAgo->format(DATE_RFC2822)]);
        $date = $response->getDate();
        $this->assertEquals($oneHourAgo->getTimestamp(), $date->getTimestamp(), '->getDate() returns the Date header if present');

        $response = new Response;
        $date = $response->getDate();
        $this->assertEquals(time(), $date->getTimestamp(), '->getDate() returns the current Date if no Date header present');

        $response = new Response('', 200, ['Date' => $this->createDateTimeOneHourAgo()->format(DATE_RFC2822)]);
        $now = $this->createDateTimeNow();
        $response->headers->set('Date', $now->format(DATE_RFC2822));
        $date = $response->getDate();
        $this->assertEquals($now->getTimestamp(), $date->getTimestamp(), '->getDate() returns the date when the header has been modified');

        $response = new Response('', 200);
        $response->headers->remove('Date');
        $this->assertInstanceOf('\DateTime', $response->getDate());
    }

    public function test_get_max_age()
    {
        $response = new Response;
        $response->headers->set('Cache-Control', 's-maxage=600, max-age=0');
        $this->assertEquals(600, $response->getMaxAge(), '->getMaxAge() uses s-maxage cache control directive when present');

        $response = new Response;
        $response->headers->set('Cache-Control', 'max-age=600');
        $this->assertEquals(600, $response->getMaxAge(), '->getMaxAge() falls back to max-age when no s-maxage directive present');

        $response = new Response;
        $response->headers->set('Cache-Control', 'must-revalidate');
        $response->headers->set('Expires', $this->createDateTimeOneHourLater()->format(DATE_RFC2822));
        $this->assertEquals(3600, $response->getMaxAge(), '->getMaxAge() falls back to Expires when no max-age or s-maxage directive present');

        $response = new Response;
        $response->headers->set('Cache-Control', 'must-revalidate');
        $response->headers->set('Expires', -1);
        $this->assertEquals('Sat, 01 Jan 00 00:00:00 +0000', $response->getExpires()->format(DATE_RFC822));

        $response = new Response;
        $this->assertNull($response->getMaxAge(), '->getMaxAge() returns null if no freshness information available');
    }

    public function test_set_shared_max_age()
    {
        $response = new Response;
        $response->setSharedMaxAge(20);

        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertEquals('public, s-maxage=20', $cacheControl);
    }

    public function test_is_private()
    {
        $response = new Response;
        $response->headers->set('Cache-Control', 'max-age=100');
        $response->setPrivate();
        $this->assertEquals(100, $response->headers->getCacheControlDirective('max-age'), '->isPrivate() adds the private Cache-Control directive when set to true');
        $this->assertTrue($response->headers->getCacheControlDirective('private'), '->isPrivate() adds the private Cache-Control directive when set to true');

        $response = new Response;
        $response->headers->set('Cache-Control', 'public, max-age=100');
        $response->setPrivate();
        $this->assertEquals(100, $response->headers->getCacheControlDirective('max-age'), '->isPrivate() adds the private Cache-Control directive when set to true');
        $this->assertTrue($response->headers->getCacheControlDirective('private'), '->isPrivate() adds the private Cache-Control directive when set to true');
        $this->assertFalse($response->headers->hasCacheControlDirective('public'), '->isPrivate() removes the public Cache-Control directive');
    }

    public function test_expire()
    {
        $response = new Response;
        $response->headers->set('Cache-Control', 'max-age=100');
        $response->expire();
        $this->assertEquals(100, $response->headers->get('Age'), '->expire() sets the Age to max-age when present');

        $response = new Response;
        $response->headers->set('Cache-Control', 'max-age=100, s-maxage=500');
        $response->expire();
        $this->assertEquals(500, $response->headers->get('Age'), '->expire() sets the Age to s-maxage when both max-age and s-maxage are present');

        $response = new Response;
        $response->headers->set('Cache-Control', 'max-age=5, s-maxage=500');
        $response->headers->set('Age', '1000');
        $response->expire();
        $this->assertEquals(1000, $response->headers->get('Age'), '->expire() does nothing when the response is already stale/expired');

        $response = new Response;
        $response->expire();
        $this->assertFalse($response->headers->has('Age'), '->expire() does nothing when the response does not include freshness information');

        $response = new Response;
        $response->headers->set('Expires', -1);
        $response->expire();
        $this->assertNull($response->headers->get('Age'), '->expire() does not set the Age when the response is expired');
    }

    public function test_get_ttl()
    {
        $response = new Response;
        $this->assertNull($response->getTtl(), '->getTtl() returns null when no Expires or Cache-Control headers are present');

        $response = new Response;
        $response->headers->set('Expires', $this->createDateTimeOneHourLater()->format(DATE_RFC2822));
        $this->assertEquals(3600, $response->getTtl(), '->getTtl() uses the Expires header when no max-age is present');

        $response = new Response;
        $response->headers->set('Expires', $this->createDateTimeOneHourAgo()->format(DATE_RFC2822));
        $this->assertLessThan(0, $response->getTtl(), '->getTtl() returns negative values when Expires is in past');

        $response = new Response;
        $response->headers->set('Expires', $response->getDate()->format(DATE_RFC2822));
        $response->headers->set('Age', 0);
        $this->assertSame(0, $response->getTtl(), '->getTtl() correctly handles zero');

        $response = new Response;
        $response->headers->set('Cache-Control', 'max-age=60');
        $this->assertEquals(60, $response->getTtl(), '->getTtl() uses Cache-Control max-age when present');
    }

    public function test_set_client_ttl()
    {
        $response = new Response;
        $response->setClientTtl(10);

        $this->assertEquals($response->getMaxAge(), $response->getAge() + 10);
    }

    public function test_get_set_protocol_version()
    {
        $response = new Response;

        $this->assertEquals('1.0', $response->getProtocolVersion());

        $response->setProtocolVersion('1.1');

        $this->assertEquals('1.1', $response->getProtocolVersion());
    }

    public function test_get_vary()
    {
        $response = new Response;
        $this->assertEquals([], $response->getVary(), '->getVary() returns an empty array if no Vary header is present');

        $response = new Response;
        $response->headers->set('Vary', 'Accept-Language');
        $this->assertEquals(['Accept-Language'], $response->getVary(), '->getVary() parses a single header name value');

        $response = new Response;
        $response->headers->set('Vary', 'Accept-Language User-Agent    X-Foo');
        $this->assertEquals(['Accept-Language', 'User-Agent', 'X-Foo'], $response->getVary(), '->getVary() parses multiple header name values separated by spaces');

        $response = new Response;
        $response->headers->set('Vary', 'Accept-Language,User-Agent,    X-Foo');
        $this->assertEquals(['Accept-Language', 'User-Agent', 'X-Foo'], $response->getVary(), '->getVary() parses multiple header name values separated by commas');

        $vary = ['Accept-Language', 'User-Agent', 'X-foo'];

        $response = new Response;
        $response->headers->set('Vary', $vary);
        $this->assertEquals($vary, $response->getVary(), '->getVary() parses multiple header name values in arrays');

        $response = new Response;
        $response->headers->set('Vary', 'Accept-Language, User-Agent, X-foo');
        $this->assertEquals($vary, $response->getVary(), '->getVary() parses multiple header name values in arrays');
    }

    public function test_set_vary()
    {
        $response = new Response;
        $response->setVary('Accept-Language');
        $this->assertEquals(['Accept-Language'], $response->getVary());

        $response->setVary('Accept-Language, User-Agent');
        $this->assertEquals(['Accept-Language', 'User-Agent'], $response->getVary(), '->setVary() replace the vary header by default');

        $response->setVary('X-Foo', false);
        $this->assertEquals(['Accept-Language', 'User-Agent', 'X-Foo'], $response->getVary(), '->setVary() doesn\'t wipe out earlier Vary headers if replace is set to false');
    }

    public function test_default_content_type()
    {
        $headerMock = $this->getMock('Symfony\Component\HttpFoundation\ResponseHeaderBag', ['set']);
        $headerMock->expects($this->at(0))
            ->method('set')
            ->with('Content-Type', 'text/html');
        $headerMock->expects($this->at(1))
            ->method('set')
            ->with('Content-Type', 'text/html; charset=UTF-8');

        $response = new Response('foo');
        $response->headers = $headerMock;

        $response->prepare(new Request);
    }

    public function test_content_type_charset()
    {
        $response = new Response;
        $response->headers->set('Content-Type', 'text/css');

        // force fixContentType() to be called
        $response->prepare(new Request);

        $this->assertEquals('text/css; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function test_prepare_does_nothing_if_content_type_is_set()
    {
        $response = new Response('foo');
        $response->headers->set('Content-Type', 'text/plain');

        $response->prepare(new Request);

        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('content-type'));
    }

    public function test_prepare_does_nothing_if_request_format_is_not_defined()
    {
        $response = new Response('foo');

        $response->prepare(new Request);

        $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('content-type'));
    }

    public function test_prepare_set_content_type()
    {
        $response = new Response('foo');
        $request = Request::create('/');
        $request->setRequestFormat('json');

        $response->prepare($request);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    public function test_prepare_removes_content_for_head_requests()
    {
        $response = new Response('foo');
        $request = Request::create('/', 'HEAD');

        $length = 12345;
        $response->headers->set('Content-Length', $length);
        $response->prepare($request);

        $this->assertEquals('', $response->getContent());
        $this->assertEquals($length, $response->headers->get('Content-Length'), 'Content-Length should be as if it was GET; see RFC2616 14.13');
    }

    public function test_prepare_removes_content_for_informational_response()
    {
        $response = new Response('foo');
        $request = Request::create('/');

        $response->setContent('content');
        $response->setStatusCode(101);
        $response->prepare($request);
        $this->assertEquals('', $response->getContent());
        $this->assertFalse($response->headers->has('Content-Type'));
        $this->assertFalse($response->headers->has('Content-Type'));

        $response->setContent('content');
        $response->setStatusCode(304);
        $response->prepare($request);
        $this->assertEquals('', $response->getContent());
        $this->assertFalse($response->headers->has('Content-Type'));
        $this->assertFalse($response->headers->has('Content-Length'));
    }

    public function test_prepare_removes_content_length()
    {
        $response = new Response('foo');
        $request = Request::create('/');

        $response->headers->set('Content-Length', 12345);
        $response->prepare($request);
        $this->assertEquals(12345, $response->headers->get('Content-Length'));

        $response->headers->set('Transfer-Encoding', 'chunked');
        $response->prepare($request);
        $this->assertFalse($response->headers->has('Content-Length'));
    }

    public function test_prepare_sets_pragma_on_http10_only()
    {
        $request = Request::create('/', 'GET');
        $request->server->set('SERVER_PROTOCOL', 'HTTP/1.0');

        $response = new Response('foo');
        $response->prepare($request);
        $this->assertEquals('no-cache', $response->headers->get('pragma'));
        $this->assertEquals('-1', $response->headers->get('expires'));

        $request->server->set('SERVER_PROTOCOL', 'HTTP/1.1');
        $response = new Response('foo');
        $response->prepare($request);
        $this->assertFalse($response->headers->has('pragma'));
        $this->assertFalse($response->headers->has('expires'));
    }

    public function test_set_cache()
    {
        $response = new Response;
        // array('etag', 'last_modified', 'max_age', 's_maxage', 'private', 'public')
        try {
            $response->setCache(['wrong option' => 'value']);
            $this->fail('->setCache() throws an InvalidArgumentException if an option is not supported');
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e, '->setCache() throws an InvalidArgumentException if an option is not supported');
            $this->assertContains('"wrong option"', $e->getMessage());
        }

        $options = ['etag' => '"whatever"'];
        $response->setCache($options);
        $this->assertEquals($response->getEtag(), '"whatever"');

        $now = $this->createDateTimeNow();
        $options = ['last_modified' => $now];
        $response->setCache($options);
        $this->assertEquals($response->getLastModified()->getTimestamp(), $now->getTimestamp());

        $options = ['max_age' => 100];
        $response->setCache($options);
        $this->assertEquals($response->getMaxAge(), 100);

        $options = ['s_maxage' => 200];
        $response->setCache($options);
        $this->assertEquals($response->getMaxAge(), 200);

        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('private'));

        $response->setCache(['public' => true]);
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('private'));

        $response->setCache(['public' => false]);
        $this->assertFalse($response->headers->hasCacheControlDirective('public'));
        $this->assertTrue($response->headers->hasCacheControlDirective('private'));

        $response->setCache(['private' => true]);
        $this->assertFalse($response->headers->hasCacheControlDirective('public'));
        $this->assertTrue($response->headers->hasCacheControlDirective('private'));

        $response->setCache(['private' => false]);
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('private'));
    }

    public function test_send_content()
    {
        $response = new Response('test response rendering', 200);

        ob_start();
        $response->sendContent();
        $string = ob_get_clean();
        $this->assertContains('test response rendering', $string);
    }

    public function test_set_public()
    {
        $response = new Response;
        $response->setPublic();

        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('private'));
    }

    public function test_set_expires()
    {
        $response = new Response;
        $response->setExpires(null);

        $this->assertNull($response->getExpires(), '->setExpires() remove the header when passed null');

        $now = $this->createDateTimeNow();
        $response->setExpires($now);

        $this->assertEquals($response->getExpires()->getTimestamp(), $now->getTimestamp());
    }

    public function test_set_last_modified()
    {
        $response = new Response;
        $response->setLastModified($this->createDateTimeNow());
        $this->assertNotNull($response->getLastModified());

        $response->setLastModified(null);
        $this->assertNull($response->getLastModified());
    }

    public function test_is_invalid()
    {
        $response = new Response;

        try {
            $response->setStatusCode(99);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue($response->isInvalid());
        }

        try {
            $response->setStatusCode(650);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue($response->isInvalid());
        }

        $response = new Response('', 200);
        $this->assertFalse($response->isInvalid());
    }

    /**
     * @dataProvider getStatusCodeFixtures
     */
    public function test_set_status_code($code, $text, $expectedText)
    {
        $response = new Response;

        $response->setStatusCode($code, $text);

        $statusText = new \ReflectionProperty($response, 'statusText');
        $statusText->setAccessible(true);

        $this->assertEquals($expectedText, $statusText->getValue($response));
    }

    public function getStatusCodeFixtures()
    {
        return [
            ['200', null, 'OK'],
            ['200', false, ''],
            ['200', 'foo', 'foo'],
            ['199', null, 'unknown status'],
            ['199', false, ''],
            ['199', 'foo', 'foo'],
        ];
    }

    public function test_is_informational()
    {
        $response = new Response('', 100);
        $this->assertTrue($response->isInformational());

        $response = new Response('', 200);
        $this->assertFalse($response->isInformational());
    }

    public function test_is_redirect_redirection()
    {
        foreach ([301, 302, 303, 307] as $code) {
            $response = new Response('', $code);
            $this->assertTrue($response->isRedirection());
            $this->assertTrue($response->isRedirect());
        }

        $response = new Response('', 304);
        $this->assertTrue($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 200);
        $this->assertFalse($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 404);
        $this->assertFalse($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 301, ['Location' => '/good-uri']);
        $this->assertFalse($response->isRedirect('/bad-uri'));
        $this->assertTrue($response->isRedirect('/good-uri'));
    }

    public function test_is_not_found()
    {
        $response = new Response('', 404);
        $this->assertTrue($response->isNotFound());

        $response = new Response('', 200);
        $this->assertFalse($response->isNotFound());
    }

    public function test_is_empty()
    {
        foreach ([204, 304] as $code) {
            $response = new Response('', $code);
            $this->assertTrue($response->isEmpty());
        }

        $response = new Response('', 200);
        $this->assertFalse($response->isEmpty());
    }

    public function test_is_forbidden()
    {
        $response = new Response('', 403);
        $this->assertTrue($response->isForbidden());

        $response = new Response('', 200);
        $this->assertFalse($response->isForbidden());
    }

    public function test_is_ok()
    {
        $response = new Response('', 200);
        $this->assertTrue($response->isOk());

        $response = new Response('', 404);
        $this->assertFalse($response->isOk());
    }

    public function test_is_server_or_client_error()
    {
        $response = new Response('', 404);
        $this->assertTrue($response->isClientError());
        $this->assertFalse($response->isServerError());

        $response = new Response('', 500);
        $this->assertFalse($response->isClientError());
        $this->assertTrue($response->isServerError());
    }

    public function test_has_vary()
    {
        $response = new Response;
        $this->assertFalse($response->hasVary());

        $response->setVary('User-Agent');
        $this->assertTrue($response->hasVary());
    }

    public function test_set_etag()
    {
        $response = new Response('', 200, ['ETag' => '"12345"']);
        $response->setEtag();

        $this->assertNull($response->headers->get('Etag'), '->setEtag() removes Etags when call with null');
    }

    /**
     * @dataProvider validContentProvider
     */
    public function test_set_content($content)
    {
        $response = new Response;
        $response->setContent($content);
        $this->assertEquals((string) $content, $response->getContent());
    }

    /**
     * @expectedException \UnexpectedValueException
     *
     * @dataProvider invalidContentProvider
     */
    public function test_set_content_invalid($content)
    {
        $response = new Response;
        $response->setContent($content);
    }

    public function test_setters_are_chainable()
    {
        $response = new Response;

        $setters = [
            'setProtocolVersion' => '1.0',
            'setCharset' => 'UTF-8',
            'setPublic' => null,
            'setPrivate' => null,
            'setDate' => $this->createDateTimeNow(),
            'expire' => null,
            'setMaxAge' => 1,
            'setSharedMaxAge' => 1,
            'setTtl' => 1,
            'setClientTtl' => 1,
        ];

        foreach ($setters as $setter => $arg) {
            $this->assertEquals($response, $response->{$setter}($arg));
        }
    }

    public function validContentProvider()
    {
        return [
            'obj' => [new StringableObject],
            'string' => ['Foo'],
            'int' => [2],
        ];
    }

    public function invalidContentProvider()
    {
        return [
            'obj' => [new \stdClass],
            'array' => [[]],
            'bool' => [true, '1'],
        ];
    }

    protected function createDateTimeOneHourAgo()
    {
        return $this->createDateTimeNow()->sub(new \DateInterval('PT1H'));
    }

    protected function createDateTimeOneHourLater()
    {
        return $this->createDateTimeNow()->add(new \DateInterval('PT1H'));
    }

    protected function createDateTimeNow()
    {
        $date = new \DateTime;

        return $date->setTimestamp(time());
    }

    protected function provideResponse()
    {
        return new Response;
    }
}

class StringableObject
{
    public function __toString()
    {
        return 'Foo';
    }
}
