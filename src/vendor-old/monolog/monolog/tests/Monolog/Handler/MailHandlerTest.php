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

class MailHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\MailHandler::handleBatch
     */
    public function test_handle_batch()
    {
        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $formatter->expects($this->once())
            ->method('formatBatch'); // Each record is formatted

        $handler = $this->getMockForAbstractClass('Monolog\\Handler\\MailHandler');
        $handler->expects($this->once())
            ->method('send');
        $handler->expects($this->never())
            ->method('write'); // write is for individual records

        $handler->setFormatter($formatter);

        $handler->handleBatch($this->getMultipleRecords());
    }

    /**
     * @covers Monolog\Handler\MailHandler::handleBatch
     */
    public function test_handle_batch_not_sends_mail_if_messages_are_below_level()
    {
        $records = [
            $this->getRecord(Logger::DEBUG, 'debug message 1'),
            $this->getRecord(Logger::DEBUG, 'debug message 2'),
            $this->getRecord(Logger::INFO, 'information'),
        ];

        $handler = $this->getMockForAbstractClass('Monolog\\Handler\\MailHandler');
        $handler->expects($this->never())
            ->method('send');
        $handler->setLevel(Logger::ERROR);

        $handler->handleBatch($records);
    }

    /**
     * @covers Monolog\Handler\MailHandler::write
     */
    public function test_handle()
    {
        $handler = $this->getMockForAbstractClass('Monolog\\Handler\\MailHandler');

        $record = $this->getRecord();
        $records = [$record];
        $records[0]['formatted'] = '['.$record['datetime']->format('Y-m-d H:i:s').'] test.WARNING: test [] []'."\n";

        $handler->expects($this->once())
            ->method('send')
            ->with($records[0]['formatted'], $records);

        $handler->handle($record);
    }
}
