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

use InvalidArgumentException;
use Monolog\Logger;
use Monolog\TestCase;

function mail($to, $subject, $message, $additional_headers = null, $additional_parameters = null)
{
    $GLOBALS['mail'][] = func_get_args();
}

class NativeMailerHandlerTest extends TestCase
{
    protected function setUp()
    {
        $GLOBALS['mail'] = [];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_constructor_header_injection()
    {
        $mailer = new NativeMailerHandler('spammer@example.org', 'dear victim', "receiver@example.org\r\nFrom: faked@attacker.org");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_setter_header_injection()
    {
        $mailer = new NativeMailerHandler('spammer@example.org', 'dear victim', 'receiver@example.org');
        $mailer->addHeader("Content-Type: text/html\r\nFrom: faked@attacker.org");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_setter_array_header_injection()
    {
        $mailer = new NativeMailerHandler('spammer@example.org', 'dear victim', 'receiver@example.org');
        $mailer->addHeader(["Content-Type: text/html\r\nFrom: faked@attacker.org"]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_setter_content_type_injection()
    {
        $mailer = new NativeMailerHandler('spammer@example.org', 'dear victim', 'receiver@example.org');
        $mailer->setContentType("text/html\r\nFrom: faked@attacker.org");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_setter_encoding_injection()
    {
        $mailer = new NativeMailerHandler('spammer@example.org', 'dear victim', 'receiver@example.org');
        $mailer->setEncoding("utf-8\r\nFrom: faked@attacker.org");
    }

    public function test_send()
    {
        $to = 'spammer@example.org';
        $subject = 'dear victim';
        $from = 'receiver@example.org';

        $mailer = new NativeMailerHandler($to, $subject, $from);
        $mailer->handleBatch([]);

        // batch is empty, nothing sent
        $this->assertEmpty($GLOBALS['mail']);

        // non-empty batch
        $mailer->handle($this->getRecord(Logger::ERROR, "Foo\nBar\r\n\r\nBaz"));
        $this->assertNotEmpty($GLOBALS['mail']);
        $this->assertInternalType('array', $GLOBALS['mail']);
        $this->assertArrayHasKey('0', $GLOBALS['mail']);
        $params = $GLOBALS['mail'][0];
        $this->assertCount(5, $params);
        $this->assertSame($to, $params[0]);
        $this->assertSame($subject, $params[1]);
        $this->assertStringEndsWith(" test.ERROR: Foo Bar  Baz [] []\n", $params[2]);
        $this->assertSame("From: $from\r\nContent-type: text/plain; charset=utf-8\r\n", $params[3]);
        $this->assertSame('', $params[4]);
    }

    public function test_message_subject_formatting()
    {
        $mailer = new NativeMailerHandler('to@example.org', 'Alert: %level_name% %message%', 'from@example.org');
        $mailer->handle($this->getRecord(Logger::ERROR, "Foo\nBar\r\n\r\nBaz"));
        $this->assertNotEmpty($GLOBALS['mail']);
        $this->assertInternalType('array', $GLOBALS['mail']);
        $this->assertArrayHasKey('0', $GLOBALS['mail']);
        $params = $GLOBALS['mail'][0];
        $this->assertCount(5, $params);
        $this->assertSame('Alert: ERROR Foo Bar  Baz', $params[1]);
    }
}
