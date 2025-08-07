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

/**
 * CookieTest.
 *
 * @author John Kary <john@johnkary.net>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 *
 * @group time-sensitive
 */
class CookieTest extends \PHPUnit_Framework_TestCase
{
    public function invalidNames()
    {
        return [
            [''],
            [',MyName'],
            [';MyName'],
            [' MyName'],
            ["\tMyName"],
            ["\rMyName"],
            ["\nMyName"],
            ["\013MyName"],
            ["\014MyName"],
        ];
    }

    /**
     * @dataProvider invalidNames
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_instantiation_throws_exception_if_cookie_name_contains_invalid_characters($name)
    {
        new Cookie($name);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_invalid_expiration()
    {
        $cookie = new Cookie('MyCookie', 'foo', 'bar');
    }

    public function test_get_value()
    {
        $value = 'MyValue';
        $cookie = new Cookie('MyCookie', $value);

        $this->assertSame($value, $cookie->getValue(), '->getValue() returns the proper value');
    }

    public function test_get_path()
    {
        $cookie = new Cookie('foo', 'bar');

        $this->assertSame('/', $cookie->getPath(), '->getPath() returns / as the default path');
    }

    public function test_get_expires_time()
    {
        $cookie = new Cookie('foo', 'bar', 3600);

        $this->assertEquals(3600, $cookie->getExpiresTime(), '->getExpiresTime() returns the expire date');
    }

    public function test_constructor_with_date_time()
    {
        $expire = new \DateTime;
        $cookie = new Cookie('foo', 'bar', $expire);

        $this->assertEquals($expire->format('U'), $cookie->getExpiresTime(), '->getExpiresTime() returns the expire date');
    }

    /**
     * @requires PHP 5.5
     */
    public function test_constructor_with_date_time_immutable()
    {
        $expire = new \DateTimeImmutable;
        $cookie = new Cookie('foo', 'bar', $expire);

        $this->assertEquals($expire->format('U'), $cookie->getExpiresTime(), '->getExpiresTime() returns the expire date');
    }

    public function test_get_expires_time_with_string_value()
    {
        $value = '+1 day';
        $cookie = new Cookie('foo', 'bar', $value);
        $expire = strtotime($value);

        $this->assertEquals($expire, $cookie->getExpiresTime(), '->getExpiresTime() returns the expire date', 1);
    }

    public function test_get_domain()
    {
        $cookie = new Cookie('foo', 'bar', 3600, '/', '.myfoodomain.com');

        $this->assertEquals('.myfoodomain.com', $cookie->getDomain(), '->getDomain() returns the domain name on which the cookie is valid');
    }

    public function test_is_secure()
    {
        $cookie = new Cookie('foo', 'bar', 3600, '/', '.myfoodomain.com', true);

        $this->assertTrue($cookie->isSecure(), '->isSecure() returns whether the cookie is transmitted over HTTPS');
    }

    public function test_is_http_only()
    {
        $cookie = new Cookie('foo', 'bar', 3600, '/', '.myfoodomain.com', false, true);

        $this->assertTrue($cookie->isHttpOnly(), '->isHttpOnly() returns whether the cookie is only transmitted over HTTP');
    }

    public function test_cookie_is_not_cleared()
    {
        $cookie = new Cookie('foo', 'bar', time() + 3600 * 24);

        $this->assertFalse($cookie->isCleared(), '->isCleared() returns false if the cookie did not expire yet');
    }

    public function test_cookie_is_cleared()
    {
        $cookie = new Cookie('foo', 'bar', time() - 20);

        $this->assertTrue($cookie->isCleared(), '->isCleared() returns true if the cookie has expired');
    }

    public function test_to_string()
    {
        $cookie = new Cookie('foo', 'bar', strtotime('Fri, 20-May-2011 15:25:52 GMT'), '/', '.myfoodomain.com', true);
        $this->assertEquals('foo=bar; expires=Fri, 20-May-2011 15:25:52 GMT; path=/; domain=.myfoodomain.com; secure; httponly', $cookie->__toString(), '->__toString() returns string representation of the cookie');

        $cookie = new Cookie('foo', null, 1, '/admin/', '.myfoodomain.com');
        $this->assertEquals('foo=deleted; expires='.gmdate('D, d-M-Y H:i:s T', time() - 31536001).'; path=/admin/; domain=.myfoodomain.com; httponly', $cookie->__toString(), '->__toString() returns string representation of a cleared cookie if value is NULL');

        $cookie = new Cookie('foo', 'bar', 0, '/', '');
        $this->assertEquals('foo=bar; path=/; httponly', $cookie->__toString());
    }
}
