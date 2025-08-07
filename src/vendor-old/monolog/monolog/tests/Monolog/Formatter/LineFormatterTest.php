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

/**
 * @covers Monolog\Formatter\LineFormatter
 */
class LineFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function test_def_format_with_string()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format([
            'level_name' => 'WARNING',
            'channel' => 'log',
            'context' => [],
            'message' => 'foo',
            'datetime' => new \DateTime,
            'extra' => [],
        ]);
        $this->assertEquals('['.date('Y-m-d').'] log.WARNING: foo [] []'."\n", $message);
    }

    public function test_def_format_with_array_context()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format([
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'message' => 'foo',
            'datetime' => new \DateTime,
            'extra' => [],
            'context' => [
                'foo' => 'bar',
                'baz' => 'qux',
                'bool' => false,
                'null' => null,
            ],
        ]);
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: foo {"foo":"bar","baz":"qux","bool":false,"null":null} []'."\n", $message);
    }

    public function test_def_format_extras()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format([
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => [],
            'datetime' => new \DateTime,
            'extra' => ['ip' => '127.0.0.1'],
            'message' => 'log',
        ]);
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: log [] {"ip":"127.0.0.1"}'."\n", $message);
    }

    public function test_format_extras()
    {
        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra.file% %extra%\n", 'Y-m-d');
        $message = $formatter->format([
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => [],
            'datetime' => new \DateTime,
            'extra' => ['ip' => '127.0.0.1', 'file' => 'test'],
            'message' => 'log',
        ]);
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: log [] test {"ip":"127.0.0.1"}'."\n", $message);
    }

    public function test_context_and_extra_optionally_not_shown_if_empty()
    {
        $formatter = new LineFormatter(null, 'Y-m-d', false, true);
        $message = $formatter->format([
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => [],
            'datetime' => new \DateTime,
            'extra' => [],
            'message' => 'log',
        ]);
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: log  '."\n", $message);
    }

    public function test_context_and_extra_replacement()
    {
        $formatter = new LineFormatter('%context.foo% => %extra.foo%');
        $message = $formatter->format([
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => ['foo' => 'bar'],
            'datetime' => new \DateTime,
            'extra' => ['foo' => 'xbar'],
            'message' => 'log',
        ]);
        $this->assertEquals('bar => xbar', $message);
    }

    public function test_def_format_with_object()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format([
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => [],
            'datetime' => new \DateTime,
            'extra' => ['foo' => new TestFoo, 'bar' => new TestBar, 'baz' => [], 'res' => fopen('php://memory', 'rb')],
            'message' => 'foobar',
        ]);

        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: foobar [] {"foo":"[object] (Monolog\\\\Formatter\\\\TestFoo: {\\"foo\\":\\"foo\\"})","bar":"[object] (Monolog\\\\Formatter\\\\TestBar: bar)","baz":[],"res":"[resource] (stream)"}'."\n", $message);
    }

    public function test_def_format_with_exception()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format([
            'level_name' => 'CRITICAL',
            'channel' => 'core',
            'context' => ['exception' => new \RuntimeException('Foo')],
            'datetime' => new \DateTime,
            'extra' => [],
            'message' => 'foobar',
        ]);

        $path = str_replace('\\/', '/', json_encode(__FILE__));

        $this->assertEquals('['.date('Y-m-d').'] core.CRITICAL: foobar {"exception":"[object] (RuntimeException(code: 0): Foo at '.substr($path, 1, -1).':'.(__LINE__ - 8).')"} []'."\n", $message);
    }

    public function test_def_format_with_previous_exception()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $previous = new \LogicException('Wut?');
        $message = $formatter->format([
            'level_name' => 'CRITICAL',
            'channel' => 'core',
            'context' => ['exception' => new \RuntimeException('Foo', 0, $previous)],
            'datetime' => new \DateTime,
            'extra' => [],
            'message' => 'foobar',
        ]);

        $path = str_replace('\\/', '/', json_encode(__FILE__));

        $this->assertEquals('['.date('Y-m-d').'] core.CRITICAL: foobar {"exception":"[object] (RuntimeException(code: 0): Foo at '.substr($path, 1, -1).':'.(__LINE__ - 8).', LogicException(code: 0): Wut? at '.substr($path, 1, -1).':'.(__LINE__ - 12).')"} []'."\n", $message);
    }

    public function test_batch_format()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->formatBatch([
            [
                'level_name' => 'CRITICAL',
                'channel' => 'test',
                'message' => 'bar',
                'context' => [],
                'datetime' => new \DateTime,
                'extra' => [],
            ],
            [
                'level_name' => 'WARNING',
                'channel' => 'log',
                'message' => 'foo',
                'context' => [],
                'datetime' => new \DateTime,
                'extra' => [],
            ],
        ]);
        $this->assertEquals('['.date('Y-m-d').'] test.CRITICAL: bar [] []'."\n".'['.date('Y-m-d').'] log.WARNING: foo [] []'."\n", $message);
    }

    public function test_format_should_strip_inline_line_breaks()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(
            [
                'message' => "foo\nbar",
                'context' => [],
                'extra' => [],
            ]
        );

        $this->assertRegExp('/foo bar/', $message);
    }

    public function test_format_should_not_strip_inline_line_breaks_when_flag_is_set()
    {
        $formatter = new LineFormatter(null, 'Y-m-d', true);
        $message = $formatter->format(
            [
                'message' => "foo\nbar",
                'context' => [],
                'extra' => [],
            ]
        );

        $this->assertRegExp('/foo\nbar/', $message);
    }
}

class TestFoo
{
    public $foo = 'foo';
}

class TestBar
{
    public function __toString()
    {
        return 'bar';
    }
}
