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

class ScalarFormatterTest extends \PHPUnit_Framework_TestCase
{
    private $formatter;

    protected function setUp()
    {
        $this->formatter = new ScalarFormatter;
    }

    public function buildTrace(\Exception $e)
    {
        $data = [];
        $trace = $e->getTrace();
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $data[] = $frame['file'].':'.$frame['line'];
            } else {
                $data[] = json_encode($frame);
            }
        }

        return $data;
    }

    public function encodeJson($data)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return json_encode($data);
    }

    public function test_format()
    {
        $exception = new \Exception('foo');
        $formatted = $this->formatter->format([
            'foo' => 'string',
            'bar' => 1,
            'baz' => false,
            'bam' => [1, 2, 3],
            'bat' => ['foo' => 'bar'],
            'bap' => \DateTime::createFromFormat(\DateTime::ISO8601, '1970-01-01T00:00:00+0000'),
            'ban' => $exception,
        ]);

        $this->assertSame([
            'foo' => 'string',
            'bar' => 1,
            'baz' => false,
            'bam' => $this->encodeJson([1, 2, 3]),
            'bat' => $this->encodeJson(['foo' => 'bar']),
            'bap' => '1970-01-01 00:00:00',
            'ban' => $this->encodeJson([
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile().':'.$exception->getLine(),
                'trace' => $this->buildTrace($exception),
            ]),
        ], $formatted);
    }

    public function test_format_with_error_context()
    {
        $context = ['file' => 'foo', 'line' => 1];
        $formatted = $this->formatter->format([
            'context' => $context,
        ]);

        $this->assertSame([
            'context' => $this->encodeJson($context),
        ], $formatted);
    }

    public function test_format_with_exception_context()
    {
        $exception = new \Exception('foo');
        $formatted = $this->formatter->format([
            'context' => [
                'exception' => $exception,
            ],
        ]);

        $this->assertSame([
            'context' => $this->encodeJson([
                'exception' => [
                    'class' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile().':'.$exception->getLine(),
                    'trace' => $this->buildTrace($exception),
                ],
            ]),
        ], $formatted);
    }
}
