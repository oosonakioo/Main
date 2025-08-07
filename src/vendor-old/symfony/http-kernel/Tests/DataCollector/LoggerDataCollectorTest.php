<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\LoggerDataCollector;

class LoggerDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getCollectTestData
     */
    public function test_collect($nb, $logs, $expectedLogs, $expectedDeprecationCount, $expectedScreamCount, $expectedPriorities = null)
    {
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\DebugLoggerInterface');
        $logger->expects($this->once())->method('countErrors')->will($this->returnValue($nb));
        $logger->expects($this->exactly(2))->method('getLogs')->will($this->returnValue($logs));

        $c = new LoggerDataCollector($logger);
        $c->lateCollect();

        $this->assertSame('logger', $c->getName());
        $this->assertSame($nb, $c->countErrors());
        $this->assertSame($expectedLogs ?: $logs, $c->getLogs());
        $this->assertSame($expectedDeprecationCount, $c->countDeprecations());
        $this->assertSame($expectedScreamCount, $c->countScreams());

        if (isset($expectedPriorities)) {
            $this->assertSame($expectedPriorities, $c->getPriorities());
        }
    }

    public function getCollectTestData()
    {
        return [
            [
                1,
                [['message' => 'foo', 'context' => [], 'priority' => 100, 'priorityName' => 'DEBUG']],
                null,
                0,
                0,
            ],
            [
                1,
                [['message' => 'foo', 'context' => ['foo' => fopen(__FILE__, 'r')], 'priority' => 100, 'priorityName' => 'DEBUG']],
                [['message' => 'foo', 'context' => ['foo' => 'Resource(stream)'], 'priority' => 100, 'priorityName' => 'DEBUG']],
                0,
                0,
            ],
            [
                1,
                [['message' => 'foo', 'context' => ['foo' => new \stdClass], 'priority' => 100, 'priorityName' => 'DEBUG']],
                [['message' => 'foo', 'context' => ['foo' => 'Object(stdClass)'], 'priority' => 100, 'priorityName' => 'DEBUG']],
                0,
                0,
            ],
            [
                1,
                [
                    ['message' => 'foo', 'context' => ['type' => E_DEPRECATED, 'level' => E_ALL], 'priority' => 100, 'priorityName' => 'DEBUG'],
                    ['message' => 'foo2', 'context' => ['type' => E_USER_DEPRECATED, 'level' => E_ALL], 'priority' => 100, 'priorityName' => 'DEBUG'],
                ],
                null,
                2,
                0,
                [100 => ['count' => 2, 'name' => 'DEBUG']],
            ],
            [
                1,
                [['message' => 'foo3', 'context' => ['name' => 'E_USER_WARNING', 'type' => E_USER_WARNING, 'level' => 0, 'file' => __FILE__, 'line' => 123], 'priority' => 100, 'priorityName' => 'DEBUG']],
                [['message' => 'foo3', 'context' => ['name' => 'E_USER_WARNING', 'type' => E_USER_WARNING, 'level' => 0, 'file' => __FILE__, 'line' => 123, 'scream' => true], 'priority' => 100, 'priorityName' => 'DEBUG']],
                0,
                1,
            ],
            [
                1,
                [
                    ['message' => 'foo3', 'context' => ['type' => E_USER_WARNING, 'level' => 0, 'file' => __FILE__, 'line' => 123], 'priority' => 100, 'priorityName' => 'DEBUG'],
                    ['message' => 'foo3', 'context' => ['type' => E_USER_WARNING, 'level' => -1, 'file' => __FILE__, 'line' => 123], 'priority' => 100, 'priorityName' => 'DEBUG'],
                ],
                [['message' => 'foo3', 'context' => ['name' => 'E_USER_WARNING', 'type' => E_USER_WARNING, 'level' => -1, 'file' => __FILE__, 'line' => 123, 'errorCount' => 2], 'priority' => 100, 'priorityName' => 'DEBUG']],
                0,
                1,
            ],
        ];
    }
}
