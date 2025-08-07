<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\Logger;

class WildfireFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Monolog\Formatter\WildfireFormatter::format
     */
    public function test_default_format()
    {
        $wildfire = new WildfireFormatter;
        $record = [
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => ['from' => 'logger'],
            'datetime' => new \DateTime('@0'),
            'extra' => ['ip' => '127.0.0.1'],
            'message' => 'log',
        ];

        $message = $wildfire->format($record);

        $this->assertEquals(
            '125|[{"Type":"ERROR","File":"","Line":"","Label":"meh"},'
                .'{"message":"log","context":{"from":"logger"},"extra":{"ip":"127.0.0.1"}}]|',
            $message
        );
    }

    /**
     * @covers Monolog\Formatter\WildfireFormatter::format
     */
    public function test_format_with_file_and_line()
    {
        $wildfire = new WildfireFormatter;
        $record = [
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => ['from' => 'logger'],
            'datetime' => new \DateTime('@0'),
            'extra' => ['ip' => '127.0.0.1', 'file' => 'test', 'line' => 14],
            'message' => 'log',
        ];

        $message = $wildfire->format($record);

        $this->assertEquals(
            '129|[{"Type":"ERROR","File":"test","Line":14,"Label":"meh"},'
                .'{"message":"log","context":{"from":"logger"},"extra":{"ip":"127.0.0.1"}}]|',
            $message
        );
    }

    /**
     * @covers Monolog\Formatter\WildfireFormatter::format
     */
    public function test_format_without_context()
    {
        $wildfire = new WildfireFormatter;
        $record = [
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => [],
            'datetime' => new \DateTime('@0'),
            'extra' => [],
            'message' => 'log',
        ];

        $message = $wildfire->format($record);

        $this->assertEquals(
            '58|[{"Type":"ERROR","File":"","Line":"","Label":"meh"},"log"]|',
            $message
        );
    }

    /**
     * @covers Monolog\Formatter\WildfireFormatter::formatBatch
     *
     * @expectedException BadMethodCallException
     */
    public function test_batch_format_throw_exception()
    {
        $wildfire = new WildfireFormatter;
        $record = [
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => [],
            'datetime' => new \DateTime('@0'),
            'extra' => [],
            'message' => 'log',
        ];

        $wildfire->formatBatch([$record]);
    }

    /**
     * @covers Monolog\Formatter\WildfireFormatter::format
     */
    public function test_table_format()
    {
        $wildfire = new WildfireFormatter;
        $record = [
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'table-channel',
            'context' => [
                WildfireFormatter::TABLE => [
                    ['col1', 'col2', 'col3'],
                    ['val1', 'val2', 'val3'],
                    ['foo1', 'foo2', 'foo3'],
                    ['bar1', 'bar2', 'bar3'],
                ],
            ],
            'datetime' => new \DateTime('@0'),
            'extra' => [],
            'message' => 'table-message',
        ];

        $message = $wildfire->format($record);

        $this->assertEquals(
            '171|[{"Type":"TABLE","File":"","Line":"","Label":"table-channel: table-message"},[["col1","col2","col3"],["val1","val2","val3"],["foo1","foo2","foo3"],["bar1","bar2","bar3"]]]|',
            $message
        );
    }
}
