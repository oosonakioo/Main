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

use Monolog\Logger;
use Monolog\TestCase;

class SwiftMailerHandlerTest extends TestCase
{
    /** @var \Swift_Mailer|\PHPUnit_Framework_MockObject_MockObject */
    private $mailer;

    protected function setUp()
    {
        $this->mailer = $this
            ->getMockBuilder('Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_message_creation_is_lazy_when_using_callback()
    {
        $this->mailer->expects($this->never())
            ->method('send');

        $callback = function () {
            throw new \RuntimeException('Swift_Message creation callback should not have been called in this test');
        };
        $handler = new SwiftMailerHandler($this->mailer, $callback);

        $records = [
            $this->getRecord(Logger::DEBUG),
            $this->getRecord(Logger::INFO),
        ];
        $handler->handleBatch($records);
    }

    public function test_message_can_be_customized_given_logged_data()
    {
        // Wire Mailer to expect a specific Swift_Message with a customized Subject
        $expectedMessage = new \Swift_Message;
        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($value) use ($expectedMessage) {
                return $value instanceof \Swift_Message
                    && $value->getSubject() === 'Emergency'
                    && $value === $expectedMessage;
            }));

        // Callback dynamically changes subject based on number of logged records
        $callback = function ($content, array $records) use ($expectedMessage) {
            $subject = count($records) > 0 ? 'Emergency' : 'Normal';
            $expectedMessage->setSubject($subject);

            return $expectedMessage;
        };
        $handler = new SwiftMailerHandler($this->mailer, $callback);

        // Logging 1 record makes this an Emergency
        $records = [
            $this->getRecord(Logger::EMERGENCY),
        ];
        $handler->handleBatch($records);
    }

    public function test_message_subject_formatting()
    {
        // Wire Mailer to expect a specific Swift_Message with a customized Subject
        $messageTemplate = new \Swift_Message;
        $messageTemplate->setSubject('Alert: %level_name% %message%');
        $receivedMessage = null;

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($value) use (&$receivedMessage) {
                $receivedMessage = $value;

                return true;
            }));

        $handler = new SwiftMailerHandler($this->mailer, $messageTemplate);

        $records = [
            $this->getRecord(Logger::EMERGENCY),
        ];
        $handler->handleBatch($records);

        $this->assertEquals('Alert: EMERGENCY test', $receivedMessage->getSubject());
    }

    public function test_message_have_unique_id()
    {
        $messageTemplate = \Swift_Message::newInstance();
        $handler = new SwiftMailerHandler($this->mailer, $messageTemplate);

        $method = new \ReflectionMethod('Monolog\Handler\SwiftMailerHandler', 'buildMessage');
        $method->setAccessible(true);
        $method->invokeArgs($handler, [$messageTemplate, []]);

        $builtMessage1 = $method->invoke($handler, $messageTemplate, []);
        $builtMessage2 = $method->invoke($handler, $messageTemplate, []);

        $this->assertFalse($builtMessage1->getId() === $builtMessage2->getId(), 'Two different messages have the same id');
    }
}
