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

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @group time-sensitive
 */
class ResponseHeaderBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideAllPreserveCase
     */
    public function test_all_preserve_case($headers, $expected)
    {
        $bag = new ResponseHeaderBag($headers);

        $this->assertEquals($expected, $bag->allPreserveCase(), '->allPreserveCase() gets all input keys in original case');
    }

    public function provideAllPreserveCase()
    {
        return [
            [
                ['fOo' => 'BAR'],
                ['fOo' => ['BAR'], 'Cache-Control' => ['no-cache']],
            ],
            [
                ['ETag' => 'xyzzy'],
                ['ETag' => ['xyzzy'], 'Cache-Control' => ['private, must-revalidate']],
            ],
            [
                ['Content-MD5' => 'Q2hlY2sgSW50ZWdyaXR5IQ=='],
                ['Content-MD5' => ['Q2hlY2sgSW50ZWdyaXR5IQ=='], 'Cache-Control' => ['no-cache']],
            ],
            [
                ['P3P' => 'CP="CAO PSA OUR"'],
                ['P3P' => ['CP="CAO PSA OUR"'], 'Cache-Control' => ['no-cache']],
            ],
            [
                ['WWW-Authenticate' => 'Basic realm="WallyWorld"'],
                ['WWW-Authenticate' => ['Basic realm="WallyWorld"'], 'Cache-Control' => ['no-cache']],
            ],
            [
                ['X-UA-Compatible' => 'IE=edge,chrome=1'],
                ['X-UA-Compatible' => ['IE=edge,chrome=1'], 'Cache-Control' => ['no-cache']],
            ],
            [
                ['X-XSS-Protection' => '1; mode=block'],
                ['X-XSS-Protection' => ['1; mode=block'], 'Cache-Control' => ['no-cache']],
            ],
        ];
    }

    public function test_cache_control_header()
    {
        $bag = new ResponseHeaderBag([]);
        $this->assertEquals('no-cache', $bag->get('Cache-Control'));
        $this->assertTrue($bag->hasCacheControlDirective('no-cache'));

        $bag = new ResponseHeaderBag(['Cache-Control' => 'public']);
        $this->assertEquals('public', $bag->get('Cache-Control'));
        $this->assertTrue($bag->hasCacheControlDirective('public'));

        $bag = new ResponseHeaderBag(['ETag' => 'abcde']);
        $this->assertEquals('private, must-revalidate', $bag->get('Cache-Control'));
        $this->assertTrue($bag->hasCacheControlDirective('private'));
        $this->assertTrue($bag->hasCacheControlDirective('must-revalidate'));
        $this->assertFalse($bag->hasCacheControlDirective('max-age'));

        $bag = new ResponseHeaderBag(['Expires' => 'Wed, 16 Feb 2011 14:17:43 GMT']);
        $this->assertEquals('private, must-revalidate', $bag->get('Cache-Control'));

        $bag = new ResponseHeaderBag([
            'Expires' => 'Wed, 16 Feb 2011 14:17:43 GMT',
            'Cache-Control' => 'max-age=3600',
        ]);
        $this->assertEquals('max-age=3600, private', $bag->get('Cache-Control'));

        $bag = new ResponseHeaderBag(['Last-Modified' => 'abcde']);
        $this->assertEquals('private, must-revalidate', $bag->get('Cache-Control'));

        $bag = new ResponseHeaderBag(['Etag' => 'abcde', 'Last-Modified' => 'abcde']);
        $this->assertEquals('private, must-revalidate', $bag->get('Cache-Control'));

        $bag = new ResponseHeaderBag(['cache-control' => 'max-age=100']);
        $this->assertEquals('max-age=100, private', $bag->get('Cache-Control'));

        $bag = new ResponseHeaderBag(['cache-control' => 's-maxage=100']);
        $this->assertEquals('s-maxage=100', $bag->get('Cache-Control'));

        $bag = new ResponseHeaderBag(['cache-control' => 'private, max-age=100']);
        $this->assertEquals('max-age=100, private', $bag->get('Cache-Control'));

        $bag = new ResponseHeaderBag(['cache-control' => 'public, max-age=100']);
        $this->assertEquals('max-age=100, public', $bag->get('Cache-Control'));

        $bag = new ResponseHeaderBag;
        $bag->set('Last-Modified', 'abcde');
        $this->assertEquals('private, must-revalidate', $bag->get('Cache-Control'));
    }

    public function test_to_string_includes_cookie_headers()
    {
        $bag = new ResponseHeaderBag([]);
        $bag->setCookie(new Cookie('foo', 'bar'));

        $this->assertContains('Set-Cookie: foo=bar; path=/; httponly', explode("\r\n", $bag->__toString()));

        $bag->clearCookie('foo');

        $this->assertRegExp('#^Set-Cookie: foo=deleted; expires='.gmdate('D, d-M-Y H:i:s T', time() - 31536001).'; path=/; httponly#m', $bag->__toString());
    }

    public function test_clear_cookie_secure_not_http_only()
    {
        $bag = new ResponseHeaderBag([]);

        $bag->clearCookie('foo', '/', null, true, false);

        $this->assertRegExp('#^Set-Cookie: foo=deleted; expires='.gmdate('D, d-M-Y H:i:s T', time() - 31536001).'; path=/; secure#m', $bag->__toString());
    }

    public function test_replace()
    {
        $bag = new ResponseHeaderBag([]);
        $this->assertEquals('no-cache', $bag->get('Cache-Control'));
        $this->assertTrue($bag->hasCacheControlDirective('no-cache'));

        $bag->replace(['Cache-Control' => 'public']);
        $this->assertEquals('public', $bag->get('Cache-Control'));
        $this->assertTrue($bag->hasCacheControlDirective('public'));
    }

    public function test_replace_with_remove()
    {
        $bag = new ResponseHeaderBag([]);
        $this->assertEquals('no-cache', $bag->get('Cache-Control'));
        $this->assertTrue($bag->hasCacheControlDirective('no-cache'));

        $bag->remove('Cache-Control');
        $bag->replace([]);
        $this->assertEquals('no-cache', $bag->get('Cache-Control'));
        $this->assertTrue($bag->hasCacheControlDirective('no-cache'));
    }

    public function test_cookies_with_same_names()
    {
        $bag = new ResponseHeaderBag;
        $bag->setCookie(new Cookie('foo', 'bar', 0, '/path/foo', 'foo.bar'));
        $bag->setCookie(new Cookie('foo', 'bar', 0, '/path/bar', 'foo.bar'));
        $bag->setCookie(new Cookie('foo', 'bar', 0, '/path/bar', 'bar.foo'));
        $bag->setCookie(new Cookie('foo', 'bar'));

        $this->assertCount(4, $bag->getCookies());

        $headers = explode("\r\n", $bag->__toString());
        $this->assertContains('Set-Cookie: foo=bar; path=/path/foo; domain=foo.bar; httponly', $headers);
        $this->assertContains('Set-Cookie: foo=bar; path=/path/foo; domain=foo.bar; httponly', $headers);
        $this->assertContains('Set-Cookie: foo=bar; path=/path/bar; domain=bar.foo; httponly', $headers);
        $this->assertContains('Set-Cookie: foo=bar; path=/; httponly', $headers);

        $cookies = $bag->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        $this->assertTrue(isset($cookies['foo.bar']['/path/foo']['foo']));
        $this->assertTrue(isset($cookies['foo.bar']['/path/bar']['foo']));
        $this->assertTrue(isset($cookies['bar.foo']['/path/bar']['foo']));
        $this->assertTrue(isset($cookies['']['/']['foo']));
    }

    public function test_remove_cookie()
    {
        $bag = new ResponseHeaderBag;
        $bag->setCookie(new Cookie('foo', 'bar', 0, '/path/foo', 'foo.bar'));
        $bag->setCookie(new Cookie('bar', 'foo', 0, '/path/bar', 'foo.bar'));

        $cookies = $bag->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        $this->assertTrue(isset($cookies['foo.bar']['/path/foo']));

        $bag->removeCookie('foo', '/path/foo', 'foo.bar');

        $cookies = $bag->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        $this->assertFalse(isset($cookies['foo.bar']['/path/foo']));

        $bag->removeCookie('bar', '/path/bar', 'foo.bar');

        $cookies = $bag->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        $this->assertFalse(isset($cookies['foo.bar']));
    }

    public function test_remove_cookie_with_null_remove()
    {
        $bag = new ResponseHeaderBag;
        $bag->setCookie(new Cookie('foo', 'bar', 0));
        $bag->setCookie(new Cookie('bar', 'foo', 0));

        $cookies = $bag->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        $this->assertTrue(isset($cookies['']['/']));

        $bag->removeCookie('foo', null);
        $cookies = $bag->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        $this->assertFalse(isset($cookies['']['/']['foo']));

        $bag->removeCookie('bar', null);
        $cookies = $bag->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        $this->assertFalse(isset($cookies['']['/']['bar']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_get_cookies_with_invalid_argument()
    {
        $bag = new ResponseHeaderBag;

        $cookies = $bag->getCookies('invalid_argument');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_make_disposition_invalid_disposition()
    {
        $headers = new ResponseHeaderBag;

        $headers->makeDisposition('invalid', 'foo.html');
    }

    /**
     * @dataProvider provideMakeDisposition
     */
    public function test_make_disposition($disposition, $filename, $filenameFallback, $expected)
    {
        $headers = new ResponseHeaderBag;

        $this->assertEquals($expected, $headers->makeDisposition($disposition, $filename, $filenameFallback));
    }

    public function test_to_string_doesnt_mess_up_headers()
    {
        $headers = new ResponseHeaderBag;

        $headers->set('Location', 'http://www.symfony.com');
        $headers->set('Content-type', 'text/html');

        (string) $headers;

        $allHeaders = $headers->allPreserveCase();
        $this->assertEquals(['http://www.symfony.com'], $allHeaders['Location']);
        $this->assertEquals(['text/html'], $allHeaders['Content-type']);
    }

    public function provideMakeDisposition()
    {
        return [
            ['attachment', 'foo.html', 'foo.html', 'attachment; filename="foo.html"'],
            ['attachment', 'foo.html', '', 'attachment; filename="foo.html"'],
            ['attachment', 'foo bar.html', '', 'attachment; filename="foo bar.html"'],
            ['attachment', 'foo "bar".html', '', 'attachment; filename="foo \\"bar\\".html"'],
            ['attachment', 'foo%20bar.html', 'foo bar.html', 'attachment; filename="foo bar.html"; filename*=utf-8\'\'foo%2520bar.html'],
            ['attachment', 'föö.html', 'foo.html', 'attachment; filename="foo.html"; filename*=utf-8\'\'f%C3%B6%C3%B6.html'],
        ];
    }

    /**
     * @dataProvider provideMakeDispositionFail
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_make_disposition_fail($disposition, $filename)
    {
        $headers = new ResponseHeaderBag;

        $headers->makeDisposition($disposition, $filename);
    }

    public function provideMakeDispositionFail()
    {
        return [
            ['attachment', 'foo%20bar.html'],
            ['attachment', 'foo/bar.html'],
            ['attachment', '/foo.html'],
            ['attachment', 'foo\bar.html'],
            ['attachment', '\foo.html'],
            ['attachment', 'föö.html'],
        ];
    }
}
