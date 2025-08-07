<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler\Slack;

use Monolog\Logger;
use Monolog\TestCase;

/**
 * @coversDefaultClass Monolog\Handler\Slack\SlackRecord
 */
class SlackRecordTest extends TestCase
{
    private $jsonPrettyPrintFlag;

    protected function setUp()
    {
        $this->jsonPrettyPrintFlag = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 128;
    }

    public function dataGetAttachmentColor()
    {
        return [
            [Logger::DEBUG, SlackRecord::COLOR_DEFAULT],
            [Logger::INFO, SlackRecord::COLOR_GOOD],
            [Logger::NOTICE, SlackRecord::COLOR_GOOD],
            [Logger::WARNING, SlackRecord::COLOR_WARNING],
            [Logger::ERROR, SlackRecord::COLOR_DANGER],
            [Logger::CRITICAL, SlackRecord::COLOR_DANGER],
            [Logger::ALERT, SlackRecord::COLOR_DANGER],
            [Logger::EMERGENCY, SlackRecord::COLOR_DANGER],
        ];
    }

    /**
     * @dataProvider dataGetAttachmentColor
     *
     * @param  int  $logLevel
     * @param  string  $expectedColour  RGB hex color or name of Slack color
     *
     * @covers ::getAttachmentColor
     */
    public function test_get_attachment_color($logLevel, $expectedColour)
    {
        $slackRecord = new SlackRecord;
        $this->assertSame(
            $expectedColour,
            $slackRecord->getAttachmentColor($logLevel)
        );
    }

    public function test_adds_channel()
    {
        $channel = '#test';
        $record = new SlackRecord($channel);
        $data = $record->getSlackData($this->getRecord());

        $this->assertArrayHasKey('channel', $data);
        $this->assertSame($channel, $data['channel']);
    }

    public function test_no_username_by_default()
    {
        $record = new SlackRecord;
        $data = $record->getSlackData($this->getRecord());

        $this->assertArrayNotHasKey('username', $data);
    }

    /**
     * @return array
     */
    public function dataStringify()
    {
        $jsonPrettyPrintFlag = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 128;

        $multipleDimensions = [[1, 2]];
        $numericKeys = ['library' => 'monolog'];
        $singleDimension = [1, 'Hello', 'Jordi'];

        return [
            [[], '[]'],
            [$multipleDimensions, json_encode($multipleDimensions, $jsonPrettyPrintFlag)],
            [$numericKeys, json_encode($numericKeys, $jsonPrettyPrintFlag)],
            [$singleDimension, json_encode($singleDimension)],
        ];
    }

    /**
     * @dataProvider dataStringify
     */
    public function test_stringify($fields, $expectedResult)
    {
        $slackRecord = new SlackRecord(
            '#test',
            'test',
            true,
            null,
            true,
            true
        );

        $this->assertSame($expectedResult, $slackRecord->stringify($fields));
    }

    public function test_adds_custom_username()
    {
        $username = 'Monolog bot';
        $record = new SlackRecord(null, $username);
        $data = $record->getSlackData($this->getRecord());

        $this->assertArrayHasKey('username', $data);
        $this->assertSame($username, $data['username']);
    }

    public function test_no_icon()
    {
        $record = new SlackRecord;
        $data = $record->getSlackData($this->getRecord());

        $this->assertArrayNotHasKey('icon_emoji', $data);
    }

    public function test_adds_icon()
    {
        $record = $this->getRecord();
        $slackRecord = new SlackRecord(null, null, false, 'ghost');
        $data = $slackRecord->getSlackData($record);

        $slackRecord2 = new SlackRecord(null, null, false, 'http://github.com/Seldaek/monolog');
        $data2 = $slackRecord2->getSlackData($record);

        $this->assertArrayHasKey('icon_emoji', $data);
        $this->assertSame(':ghost:', $data['icon_emoji']);
        $this->assertArrayHasKey('icon_url', $data2);
        $this->assertSame('http://github.com/Seldaek/monolog', $data2['icon_url']);
    }

    public function test_attachments_not_present_if_no_attachment()
    {
        $record = new SlackRecord(null, null, false);
        $data = $record->getSlackData($this->getRecord());

        $this->assertArrayNotHasKey('attachments', $data);
    }

    public function test_adds_one_attachment()
    {
        $record = new SlackRecord;
        $data = $record->getSlackData($this->getRecord());

        $this->assertArrayHasKey('attachments', $data);
        $this->assertArrayHasKey(0, $data['attachments']);
        $this->assertInternalType('array', $data['attachments'][0]);
    }

    public function test_text_equals_message_if_no_attachment()
    {
        $message = 'Test message';
        $record = new SlackRecord(null, null, false);
        $data = $record->getSlackData($this->getRecord(Logger::WARNING, $message));

        $this->assertArrayHasKey('text', $data);
        $this->assertSame($message, $data['text']);
    }

    public function test_text_equals_formatter_output()
    {
        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $formatter
            ->expects($this->any())
            ->method('format')
            ->will($this->returnCallback(function ($record) {
                return $record['message'].'test';
            }));

        $formatter2 = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $formatter2
            ->expects($this->any())
            ->method('format')
            ->will($this->returnCallback(function ($record) {
                return $record['message'].'test1';
            }));

        $message = 'Test message';
        $record = new SlackRecord(null, null, false, null, false, false, [], $formatter);
        $data = $record->getSlackData($this->getRecord(Logger::WARNING, $message));

        $this->assertArrayHasKey('text', $data);
        $this->assertSame($message.'test', $data['text']);

        $record->setFormatter($formatter2);
        $data = $record->getSlackData($this->getRecord(Logger::WARNING, $message));

        $this->assertArrayHasKey('text', $data);
        $this->assertSame($message.'test1', $data['text']);
    }

    public function test_adds_fallback_and_text_to_attachment()
    {
        $message = 'Test message';
        $record = new SlackRecord(null);
        $data = $record->getSlackData($this->getRecord(Logger::WARNING, $message));

        $this->assertSame($message, $data['attachments'][0]['text']);
        $this->assertSame($message, $data['attachments'][0]['fallback']);
    }

    public function test_maps_level_to_color_attachment_color()
    {
        $record = new SlackRecord(null);
        $errorLoggerRecord = $this->getRecord(Logger::ERROR);
        $emergencyLoggerRecord = $this->getRecord(Logger::EMERGENCY);
        $warningLoggerRecord = $this->getRecord(Logger::WARNING);
        $infoLoggerRecord = $this->getRecord(Logger::INFO);
        $debugLoggerRecord = $this->getRecord(Logger::DEBUG);

        $data = $record->getSlackData($errorLoggerRecord);
        $this->assertSame(SlackRecord::COLOR_DANGER, $data['attachments'][0]['color']);

        $data = $record->getSlackData($emergencyLoggerRecord);
        $this->assertSame(SlackRecord::COLOR_DANGER, $data['attachments'][0]['color']);

        $data = $record->getSlackData($warningLoggerRecord);
        $this->assertSame(SlackRecord::COLOR_WARNING, $data['attachments'][0]['color']);

        $data = $record->getSlackData($infoLoggerRecord);
        $this->assertSame(SlackRecord::COLOR_GOOD, $data['attachments'][0]['color']);

        $data = $record->getSlackData($debugLoggerRecord);
        $this->assertSame(SlackRecord::COLOR_DEFAULT, $data['attachments'][0]['color']);
    }

    public function test_adds_short_attachment_without_context_and_extra()
    {
        $level = Logger::ERROR;
        $levelName = Logger::getLevelName($level);
        $record = new SlackRecord(null, null, true, null, true);
        $data = $record->getSlackData($this->getRecord($level, 'test', ['test' => 1]));

        $attachment = $data['attachments'][0];
        $this->assertArrayHasKey('title', $attachment);
        $this->assertArrayHasKey('fields', $attachment);
        $this->assertSame($levelName, $attachment['title']);
        $this->assertSame([], $attachment['fields']);
    }

    public function test_adds_short_attachment_with_context_and_extra()
    {
        $level = Logger::ERROR;
        $levelName = Logger::getLevelName($level);
        $context = ['test' => 1];
        $extra = ['tags' => ['web']];
        $record = new SlackRecord(null, null, true, null, true, true);
        $loggerRecord = $this->getRecord($level, 'test', $context);
        $loggerRecord['extra'] = $extra;
        $data = $record->getSlackData($loggerRecord);

        $attachment = $data['attachments'][0];
        $this->assertArrayHasKey('title', $attachment);
        $this->assertArrayHasKey('fields', $attachment);
        $this->assertCount(2, $attachment['fields']);
        $this->assertSame($levelName, $attachment['title']);
        $this->assertSame(
            [
                [
                    'title' => 'Extra',
                    'value' => sprintf('```%s```', json_encode($extra, $this->jsonPrettyPrintFlag)),
                    'short' => false,
                ],
                [
                    'title' => 'Context',
                    'value' => sprintf('```%s```', json_encode($context, $this->jsonPrettyPrintFlag)),
                    'short' => false,
                ],
            ],
            $attachment['fields']
        );
    }

    public function test_adds_long_attachment_without_context_and_extra()
    {
        $level = Logger::ERROR;
        $levelName = Logger::getLevelName($level);
        $record = new SlackRecord(null, null, true, null);
        $data = $record->getSlackData($this->getRecord($level, 'test', ['test' => 1]));

        $attachment = $data['attachments'][0];
        $this->assertArrayHasKey('title', $attachment);
        $this->assertArrayHasKey('fields', $attachment);
        $this->assertCount(1, $attachment['fields']);
        $this->assertSame('Message', $attachment['title']);
        $this->assertSame(
            [[
                'title' => 'Level',
                'value' => $levelName,
                'short' => false,
            ]],
            $attachment['fields']
        );
    }

    public function test_adds_long_attachment_with_context_and_extra()
    {
        $level = Logger::ERROR;
        $levelName = Logger::getLevelName($level);
        $context = ['test' => 1];
        $extra = ['tags' => ['web']];
        $record = new SlackRecord(null, null, true, null, false, true);
        $loggerRecord = $this->getRecord($level, 'test', $context);
        $loggerRecord['extra'] = $extra;
        $data = $record->getSlackData($loggerRecord);

        $expectedFields = [
            [
                'title' => 'Level',
                'value' => $levelName,
                'short' => false,
            ],
            [
                'title' => 'tags',
                'value' => sprintf('```%s```', json_encode($extra['tags'])),
                'short' => false,
            ],
            [
                'title' => 'test',
                'value' => $context['test'],
                'short' => false,
            ],
        ];

        $attachment = $data['attachments'][0];
        $this->assertArrayHasKey('title', $attachment);
        $this->assertArrayHasKey('fields', $attachment);
        $this->assertCount(3, $attachment['fields']);
        $this->assertSame('Message', $attachment['title']);
        $this->assertSame(
            $expectedFields,
            $attachment['fields']
        );
    }

    public function test_adds_timestamp_to_attachment()
    {
        $record = $this->getRecord();
        $slackRecord = new SlackRecord;
        $data = $slackRecord->getSlackData($this->getRecord());

        $attachment = $data['attachments'][0];
        $this->assertArrayHasKey('ts', $attachment);
        $this->assertSame($record['datetime']->getTimestamp(), $attachment['ts']);
    }

    public function test_exclude_extra_and_context_fields()
    {
        $record = $this->getRecord(
            Logger::WARNING,
            'test',
            ['info' => ['library' => 'monolog', 'author' => 'Jordi']]
        );
        $record['extra'] = ['tags' => ['web', 'cli']];

        $slackRecord = new SlackRecord(null, null, true, null, false, true, ['context.info.library', 'extra.tags.1']);
        $data = $slackRecord->getSlackData($record);
        $attachment = $data['attachments'][0];

        $expected = [
            [
                'title' => 'info',
                'value' => sprintf('```%s```', json_encode(['author' => 'Jordi'], $this->jsonPrettyPrintFlag)),
                'short' => false,
            ],
            [
                'title' => 'tags',
                'value' => sprintf('```%s```', json_encode(['web'])),
                'short' => false,
            ],
        ];

        foreach ($expected as $field) {
            $this->assertNotFalse(array_search($field, $attachment['fields']));
            break;
        }
    }
}
