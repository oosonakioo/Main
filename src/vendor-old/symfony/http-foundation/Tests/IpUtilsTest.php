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

use Symfony\Component\HttpFoundation\IpUtils;

class IpUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testIpv4Provider
     */
    public function test_ipv4($matches, $remoteAddr, $cidr)
    {
        $this->assertSame($matches, IpUtils::checkIp($remoteAddr, $cidr));
    }

    public function test_ipv4_provider()
    {
        return [
            [true, '192.168.1.1', '192.168.1.1'],
            [true, '192.168.1.1', '192.168.1.1/1'],
            [true, '192.168.1.1', '192.168.1.0/24'],
            [false, '192.168.1.1', '1.2.3.4/1'],
            [false, '192.168.1.1', '192.168.1.1/33'], // invalid subnet
            [true, '192.168.1.1', ['1.2.3.4/1', '192.168.1.0/24']],
            [true, '192.168.1.1', ['192.168.1.0/24', '1.2.3.4/1']],
            [false, '192.168.1.1', ['1.2.3.4/1', '4.3.2.1/1']],
            [true, '1.2.3.4', '0.0.0.0/0'],
            [true, '1.2.3.4', '192.168.1.0/0'],
            [false, '1.2.3.4', '256.256.256/0'], // invalid CIDR notation
        ];
    }

    /**
     * @dataProvider testIpv6Provider
     */
    public function test_ipv6($matches, $remoteAddr, $cidr)
    {
        if (! defined('AF_INET6')) {
            $this->markTestSkipped('Only works when PHP is compiled without the option "disable-ipv6".');
        }

        $this->assertSame($matches, IpUtils::checkIp($remoteAddr, $cidr));
    }

    public function test_ipv6_provider()
    {
        return [
            [true, '2a01:198:603:0:396e:4789:8e99:890f', '2a01:198:603:0::/65'],
            [false, '2a00:198:603:0:396e:4789:8e99:890f', '2a01:198:603:0::/65'],
            [false, '2a01:198:603:0:396e:4789:8e99:890f', '::1'],
            [true, '0:0:0:0:0:0:0:1', '::1'],
            [false, '0:0:603:0:396e:4789:8e99:0001', '::1'],
            [true, '2a01:198:603:0:396e:4789:8e99:890f', ['::1', '2a01:198:603:0::/65']],
            [true, '2a01:198:603:0:396e:4789:8e99:890f', ['2a01:198:603:0::/65', '::1']],
            [false, '2a01:198:603:0:396e:4789:8e99:890f', ['::1', '1a01:198:603:0::/65']],
            [false, '}__test|O:21:&quot;JDatabaseDriverMysqli&quot;:3:{s:2', '::1'],
            [false, '2a01:198:603:0:396e:4789:8e99:890f', 'unknown'],
        ];
    }

    /**
     * @expectedException \RuntimeException
     *
     * @requires extension sockets
     */
    public function test_an_ipv6_with_option_disabled_ipv6()
    {
        if (defined('AF_INET6')) {
            $this->markTestSkipped('Only works when PHP is compiled with the option "disable-ipv6".');
        }

        IpUtils::checkIp('2a01:198:603:0:396e:4789:8e99:890f', '2a01:198:603:0::/65');
    }
}
