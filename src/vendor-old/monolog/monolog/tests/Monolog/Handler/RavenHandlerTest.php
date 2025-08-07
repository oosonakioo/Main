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

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\TestCase;

class RavenHandlerTest extends TestCase
{
    protected function setUp()
    {
        if (! class_exists('Raven_Client')) {
            $this->markTestSkipped('raven/raven not installed');
        }

        require_once __DIR__.'/MockRavenClient.php';
    }

    /**
     * @covers Monolog\Handler\RavenHandler::__construct
     */
    public function test_construct()
    {
        $handler = new RavenHandler($this->getRavenClient());
        $this->assertInstanceOf('Monolog\Handler\RavenHandler', $handler);
    }

    protected function getHandler($ravenClient)
    {
        $handler = new RavenHandler($ravenClient);

        return $handler;
    }

    protected function getRavenClient()
    {
        $dsn = 'http://43f6017361224d098402974103bfc53d:a6a0538fc2934ba2bed32e08741b2cd3@marca.python.live.cheggnet.com:9000/1';

        return new MockRavenClient($dsn);
    }

    public function test_debug()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $record = $this->getRecord(Logger::DEBUG, 'A test debug message');
        $handler->handle($record);

        $this->assertEquals($ravenClient::DEBUG, $ravenClient->lastData['level']);
        $this->assertContains($record['message'], $ravenClient->lastData['message']);
    }

    public function test_warning()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $record = $this->getRecord(Logger::WARNING, 'A test warning message');
        $handler->handle($record);

        $this->assertEquals($ravenClient::WARNING, $ravenClient->lastData['level']);
        $this->assertContains($record['message'], $ravenClient->lastData['message']);
    }

    public function test_tag()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $tags = [1, 2, 'foo'];
        $record = $this->getRecord(Logger::INFO, 'test', ['tags' => $tags]);
        $handler->handle($record);

        $this->assertEquals($tags, $ravenClient->lastData['tags']);
    }

    public function test_extra_parameters()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $checksum = '098f6bcd4621d373cade4e832627b4f6';
        $release = '05a671c66aefea124cc08b76ea6d30bb';
        $eventId = '31423';
        $record = $this->getRecord(Logger::INFO, 'test', ['checksum' => $checksum, 'release' => $release, 'event_id' => $eventId]);
        $handler->handle($record);

        $this->assertEquals($checksum, $ravenClient->lastData['checksum']);
        $this->assertEquals($release, $ravenClient->lastData['release']);
        $this->assertEquals($eventId, $ravenClient->lastData['event_id']);
    }

    public function test_fingerprint()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $fingerprint = ['{{ default }}', 'other value'];
        $record = $this->getRecord(Logger::INFO, 'test', ['fingerprint' => $fingerprint]);
        $handler->handle($record);

        $this->assertEquals($fingerprint, $ravenClient->lastData['fingerprint']);
    }

    public function test_user_context()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $recordWithNoContext = $this->getRecord(Logger::INFO, 'test with default user context');
        // set user context 'externally'

        $user = [
            'id' => '123',
            'email' => 'test@test.com',
        ];

        $recordWithContext = $this->getRecord(Logger::INFO, 'test', ['user' => $user]);

        $ravenClient->user_context(['id' => 'test_user_id']);
        // handle context
        $handler->handle($recordWithContext);
        $this->assertEquals($user, $ravenClient->lastData['user']);

        // check to see if its reset
        $handler->handle($recordWithNoContext);
        $this->assertInternalType('array', $ravenClient->context->user);
        $this->assertSame('test_user_id', $ravenClient->context->user['id']);

        // handle with null context
        $ravenClient->user_context(null);
        $handler->handle($recordWithContext);
        $this->assertEquals($user, $ravenClient->lastData['user']);

        // check to see if its reset
        $handler->handle($recordWithNoContext);
        $this->assertNull($ravenClient->context->user);
    }

    public function test_exception()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        try {
            $this->methodThatThrowsAnException();
        } catch (\Exception $e) {
            $record = $this->getRecord(Logger::ERROR, $e->getMessage(), ['exception' => $e]);
            $handler->handle($record);
        }

        $this->assertEquals($record['message'], $ravenClient->lastData['message']);
    }

    public function test_handle_batch()
    {
        $records = $this->getMultipleRecords();
        $records[] = $this->getRecord(Logger::WARNING, 'warning');
        $records[] = $this->getRecord(Logger::WARNING, 'warning');

        $logFormatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $logFormatter->expects($this->once())->method('formatBatch');

        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $formatter->expects($this->once())->method('format')->with($this->callback(function ($record) {
            return $record['level'] == 400;
        }));

        $handler = $this->getHandler($this->getRavenClient());
        $handler->setBatchFormatter($logFormatter);
        $handler->setFormatter($formatter);
        $handler->handleBatch($records);
    }

    public function test_handle_batch_do_nothing_if_records_are_below_level()
    {
        $records = [
            $this->getRecord(Logger::DEBUG, 'debug message 1'),
            $this->getRecord(Logger::DEBUG, 'debug message 2'),
            $this->getRecord(Logger::INFO, 'information'),
        ];

        $handler = $this->getMock('Monolog\Handler\RavenHandler', null, [$this->getRavenClient()]);
        $handler->expects($this->never())->method('handle');
        $handler->setLevel(Logger::ERROR);
        $handler->handleBatch($records);
    }

    public function test_handle_batch_picks_proper_message()
    {
        $records = [
            $this->getRecord(Logger::DEBUG, 'debug message 1'),
            $this->getRecord(Logger::DEBUG, 'debug message 2'),
            $this->getRecord(Logger::INFO, 'information 1'),
            $this->getRecord(Logger::ERROR, 'error 1'),
            $this->getRecord(Logger::WARNING, 'warning'),
            $this->getRecord(Logger::ERROR, 'error 2'),
            $this->getRecord(Logger::INFO, 'information 2'),
        ];

        $logFormatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $logFormatter->expects($this->once())->method('formatBatch');

        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $formatter->expects($this->once())->method('format')->with($this->callback(function ($record) {
            return $record['message'] == 'error 1';
        }));

        $handler = $this->getHandler($this->getRavenClient());
        $handler->setBatchFormatter($logFormatter);
        $handler->setFormatter($formatter);
        $handler->handleBatch($records);
    }

    public function test_get_set_batch_formatter()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $handler->setBatchFormatter($formatter = new LineFormatter);
        $this->assertSame($formatter, $handler->getBatchFormatter());
    }

    public function test_release()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);
        $release = 'v42.42.42';
        $handler->setRelease($release);
        $record = $this->getRecord(Logger::INFO, 'test');
        $handler->handle($record);
        $this->assertEquals($release, $ravenClient->lastData['release']);

        $localRelease = 'v41.41.41';
        $record = $this->getRecord(Logger::INFO, 'test', ['release' => $localRelease]);
        $handler->handle($record);
        $this->assertEquals($localRelease, $ravenClient->lastData['release']);
    }

    private function methodThatThrowsAnException()
    {
        throw new \Exception('This is an exception');
    }
}
