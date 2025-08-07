<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Handler\SyslogUdp\UdpSocket;
use Monolog\TestCase;

/**
 * @requires extension sockets
 */
class UdpSocketTest extends TestCase
{
    public function test_we_do_not_truncate_short_messages()
    {
        $socket = $this->getMock('\Monolog\Handler\SyslogUdp\UdpSocket', ['send'], ['lol', 'lol']);

        $socket->expects($this->at(0))
            ->method('send')
            ->with('HEADER: The quick brown fox jumps over the lazy dog');

        $socket->write('The quick brown fox jumps over the lazy dog', 'HEADER: ');
    }

    public function test_long_messages_are_truncated()
    {
        $socket = $this->getMock('\Monolog\Handler\SyslogUdp\UdpSocket', ['send'], ['lol', 'lol']);

        $truncatedString = str_repeat('derp', 16254).'d';

        $socket->expects($this->exactly(1))
            ->method('send')
            ->with('HEADER'.$truncatedString);

        $longString = str_repeat('derp', 20000);

        $socket->write($longString, 'HEADER');
    }

    public function test_double_close_does_not_error()
    {
        $socket = new UdpSocket('127.0.0.1', 514);
        $socket->close();
        $socket->close();
    }

    /**
     * @expectedException LogicException
     */
    public function test_write_after_close_errors()
    {
        $socket = new UdpSocket('127.0.0.1', 514);
        $socket->close();
        $socket->write('foo', 'HEADER');
    }
}
