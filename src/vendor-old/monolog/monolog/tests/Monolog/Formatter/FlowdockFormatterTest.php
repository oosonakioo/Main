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
use Monolog\TestCase;

class FlowdockFormatterTest extends TestCase
{
    /**
     * @covers Monolog\Formatter\FlowdockFormatter::format
     */
    public function test_format()
    {
        $formatter = new FlowdockFormatter('test_source', 'source@test.com');
        $record = $this->getRecord();

        $expected = [
            'source' => 'test_source',
            'from_address' => 'source@test.com',
            'subject' => 'in test_source: WARNING - test',
            'content' => 'test',
            'tags' => ['#logs', '#warning', '#test'],
            'project' => 'test_source',
        ];
        $formatted = $formatter->format($record);

        $this->assertEquals($expected, $formatted['flowdock']);
    }

    /**
     * @ covers Monolog\Formatter\FlowdockFormatter::formatBatch
     */
    public function test_format_batch()
    {
        $formatter = new FlowdockFormatter('test_source', 'source@test.com');
        $records = [
            $this->getRecord(Logger::WARNING),
            $this->getRecord(Logger::DEBUG),
        ];
        $formatted = $formatter->formatBatch($records);

        $this->assertArrayHasKey('flowdock', $formatted[0]);
        $this->assertArrayHasKey('flowdock', $formatted[1]);
    }
}
