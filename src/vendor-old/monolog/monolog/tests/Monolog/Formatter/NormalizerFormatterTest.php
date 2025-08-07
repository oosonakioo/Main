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
 * @covers Monolog\Formatter\NormalizerFormatter
 */
class NormalizerFormatterTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \PHPUnit_Framework_Error_Warning::$enabled = true;

        return parent::tearDown();
    }

    public function test_format()
    {
        $formatter = new NormalizerFormatter('Y-m-d');
        $formatted = $formatter->format([
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'message' => 'foo',
            'datetime' => new \DateTime,
            'extra' => ['foo' => new TestFooNorm, 'bar' => new TestBarNorm, 'baz' => [], 'res' => fopen('php://memory', 'rb')],
            'context' => [
                'foo' => 'bar',
                'baz' => 'qux',
                'inf' => INF,
                '-inf' => -INF,
                'nan' => acos(4),
            ],
        ]);

        $this->assertEquals([
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'message' => 'foo',
            'datetime' => date('Y-m-d'),
            'extra' => [
                'foo' => '[object] (Monolog\\Formatter\\TestFooNorm: {"foo":"foo"})',
                'bar' => '[object] (Monolog\\Formatter\\TestBarNorm: bar)',
                'baz' => [],
                'res' => '[resource] (stream)',
            ],
            'context' => [
                'foo' => 'bar',
                'baz' => 'qux',
                'inf' => 'INF',
                '-inf' => '-INF',
                'nan' => 'NaN',
            ],
        ], $formatted);
    }

    public function test_format_exceptions()
    {
        $formatter = new NormalizerFormatter('Y-m-d');
        $e = new \LogicException('bar');
        $e2 = new \RuntimeException('foo', 0, $e);
        $formatted = $formatter->format([
            'exception' => $e2,
        ]);

        $this->assertGreaterThan(5, count($formatted['exception']['trace']));
        $this->assertTrue(isset($formatted['exception']['previous']));
        unset($formatted['exception']['trace'], $formatted['exception']['previous']);

        $this->assertEquals([
            'exception' => [
                'class' => get_class($e2),
                'message' => $e2->getMessage(),
                'code' => $e2->getCode(),
                'file' => $e2->getFile().':'.$e2->getLine(),
            ],
        ], $formatted);
    }

    public function test_format_soap_fault_exception()
    {
        if (! class_exists('SoapFault')) {
            $this->markTestSkipped('Requires the soap extension');
        }

        $formatter = new NormalizerFormatter('Y-m-d');
        $e = new \SoapFault('foo', 'bar', 'hello', 'world');
        $formatted = $formatter->format([
            'exception' => $e,
        ]);

        unset($formatted['exception']['trace']);

        $this->assertEquals([
            'exception' => [
                'class' => 'SoapFault',
                'message' => 'bar',
                'code' => 0,
                'file' => $e->getFile().':'.$e->getLine(),
                'faultcode' => 'foo',
                'faultactor' => 'hello',
                'detail' => 'world',
            ],
        ], $formatted);
    }

    public function test_format_to_string_exception_handle()
    {
        $formatter = new NormalizerFormatter('Y-m-d');
        $this->setExpectedException('RuntimeException', 'Could not convert to string');
        $formatter->format([
            'myObject' => new TestToStringError,
        ]);
    }

    public function test_batch_format()
    {
        $formatter = new NormalizerFormatter('Y-m-d');
        $formatted = $formatter->formatBatch([
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
        $this->assertEquals([
            [
                'level_name' => 'CRITICAL',
                'channel' => 'test',
                'message' => 'bar',
                'context' => [],
                'datetime' => date('Y-m-d'),
                'extra' => [],
            ],
            [
                'level_name' => 'WARNING',
                'channel' => 'log',
                'message' => 'foo',
                'context' => [],
                'datetime' => date('Y-m-d'),
                'extra' => [],
            ],
        ], $formatted);
    }

    /**
     * Test issue #137
     */
    public function test_ignores_recursive_object_references()
    {
        // set up the recursion
        $foo = new \stdClass;
        $bar = new \stdClass;

        $foo->bar = $bar;
        $bar->foo = $foo;

        // set an error handler to assert that the error is not raised anymore
        $that = $this;
        set_error_handler(function ($level, $message, $file, $line, $context) use ($that) {
            if (error_reporting() & $level) {
                restore_error_handler();
                $that->fail("$message should not be raised");
            }
        });

        $formatter = new NormalizerFormatter;
        $reflMethod = new \ReflectionMethod($formatter, 'toJson');
        $reflMethod->setAccessible(true);
        $res = $reflMethod->invoke($formatter, [$foo, $bar], true);

        restore_error_handler();

        $this->assertEquals(@json_encode([$foo, $bar]), $res);
    }

    public function test_ignores_invalid_types()
    {
        // set up the recursion
        $resource = fopen(__FILE__, 'r');

        // set an error handler to assert that the error is not raised anymore
        $that = $this;
        set_error_handler(function ($level, $message, $file, $line, $context) use ($that) {
            if (error_reporting() & $level) {
                restore_error_handler();
                $that->fail("$message should not be raised");
            }
        });

        $formatter = new NormalizerFormatter;
        $reflMethod = new \ReflectionMethod($formatter, 'toJson');
        $reflMethod->setAccessible(true);
        $res = $reflMethod->invoke($formatter, [$resource], true);

        restore_error_handler();

        $this->assertEquals(@json_encode([$resource]), $res);
    }

    public function test_normalize_handle_large_arrays()
    {
        $formatter = new NormalizerFormatter;
        $largeArray = range(1, 2000);

        $res = $formatter->format([
            'level_name' => 'CRITICAL',
            'channel' => 'test',
            'message' => 'bar',
            'context' => [$largeArray],
            'datetime' => new \DateTime,
            'extra' => [],
        ]);

        $this->assertCount(1000, $res['context'][0]);
        $this->assertEquals('Over 1000 items (2000 total), aborting normalization', $res['context'][0]['...']);
    }

    /**
     * @expectedException RuntimeException
     */
    public function test_throws_on_invalid_encoding()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            // Ignore the warning that will be emitted by PHP <5.5.0
            \PHPUnit_Framework_Error_Warning::$enabled = false;
        }
        $formatter = new NormalizerFormatter;
        $reflMethod = new \ReflectionMethod($formatter, 'toJson');
        $reflMethod->setAccessible(true);

        // send an invalid unicode sequence as a object that can't be cleaned
        $record = new \stdClass;
        $record->message = "\xB1\x31";
        $res = $reflMethod->invoke($formatter, $record);
        if (PHP_VERSION_ID < 50500 && $res === '{"message":null}') {
            throw new \RuntimeException('PHP 5.3/5.4 throw a warning and null the value instead of returning false entirely');
        }
    }

    public function test_converts_invalid_encoding_as_latin9()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            // Ignore the warning that will be emitted by PHP <5.5.0
            \PHPUnit_Framework_Error_Warning::$enabled = false;
        }
        $formatter = new NormalizerFormatter;
        $reflMethod = new \ReflectionMethod($formatter, 'toJson');
        $reflMethod->setAccessible(true);

        $res = $reflMethod->invoke($formatter, ['message' => "\xA4\xA6\xA8\xB4\xB8\xBC\xBD\xBE"]);

        if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            $this->assertSame('{"message":"€ŠšŽžŒœŸ"}', $res);
        } else {
            // PHP <5.5 does not return false for an element encoding failure,
            // instead it emits a warning (possibly) and nulls the value.
            $this->assertSame('{"message":null}', $res);
        }
    }

    /**
     * @param  mixed  $in  Input
     * @param  mixed  $expect  Expected output
     *
     * @covers Monolog\Formatter\NormalizerFormatter::detectAndCleanUtf8
     *
     * @dataProvider providesDetectAndCleanUtf8
     */
    public function test_detect_and_clean_utf8($in, $expect)
    {
        $formatter = new NormalizerFormatter;
        $formatter->detectAndCleanUtf8($in);
        $this->assertSame($expect, $in);
    }

    public function providesDetectAndCleanUtf8()
    {
        $obj = new \stdClass;

        return [
            'null' => [null, null],
            'int' => [123, 123],
            'float' => [123.45, 123.45],
            'bool false' => [false, false],
            'bool true' => [true, true],
            'ascii string' => ['abcdef', 'abcdef'],
            'latin9 string' => ["\xB1\x31\xA4\xA6\xA8\xB4\xB8\xBC\xBD\xBE\xFF", '±1€ŠšŽžŒœŸÿ'],
            'unicode string' => ['¤¦¨´¸¼½¾€ŠšŽžŒœŸ', '¤¦¨´¸¼½¾€ŠšŽžŒœŸ'],
            'empty array' => [[], []],
            'array' => [['abcdef'], ['abcdef']],
            'object' => [$obj, $obj],
        ];
    }

    /**
     * @param  int  $code
     * @param  string  $msg
     *
     * @dataProvider providesHandleJsonErrorFailure
     */
    public function test_handle_json_error_failure($code, $msg)
    {
        $formatter = new NormalizerFormatter;
        $reflMethod = new \ReflectionMethod($formatter, 'handleJsonError');
        $reflMethod->setAccessible(true);

        $this->setExpectedException('RuntimeException', $msg);
        $reflMethod->invoke($formatter, $code, 'faked');
    }

    public function providesHandleJsonErrorFailure()
    {
        return [
            'depth' => [JSON_ERROR_DEPTH, 'Maximum stack depth exceeded'],
            'state' => [JSON_ERROR_STATE_MISMATCH, 'Underflow or the modes mismatch'],
            'ctrl' => [JSON_ERROR_CTRL_CHAR, 'Unexpected control character found'],
            'default' => [-1, 'Unknown error'],
        ];
    }

    public function test_exception_trace_with_args()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('Not supported in HHVM since it detects errors differently');
        }

        // This happens i.e. in React promises or Guzzle streams where stream wrappers are registered
        // and no file or line are included in the trace because it's treated as internal function
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        try {
            // This will contain $resource and $wrappedResource as arguments in the trace item
            $resource = fopen('php://memory', 'rw+');
            fwrite($resource, 'test_resource');
            $wrappedResource = new TestFooNorm;
            $wrappedResource->foo = $resource;
            // Just do something stupid with a resource/wrapped resource as argument
            array_keys($wrappedResource);
        } catch (\Exception $e) {
            restore_error_handler();
        }

        $formatter = new NormalizerFormatter;
        $record = ['context' => ['exception' => $e]];
        $result = $formatter->format($record);

        $this->assertRegExp(
            '%"resource":"\[resource\] \(stream\)"%',
            $result['context']['exception']['trace'][0]
        );

        if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            $pattern = '%"wrappedResource":"\[object\] \(Monolog\\\\\\\\Formatter\\\\\\\\TestFooNorm: \)"%';
        } else {
            $pattern = '%\\\\"foo\\\\":null%';
        }

        // Tests that the wrapped resource is ignored while encoding, only works for PHP <= 5.4
        $this->assertRegExp(
            $pattern,
            $result['context']['exception']['trace'][0]
        );
    }
}

class TestFooNorm
{
    public $foo = 'foo';
}

class TestBarNorm
{
    public function __toString()
    {
        return 'bar';
    }
}

class TestStreamFoo
{
    public $foo;

    public $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
        $this->foo = 'BAR';
    }

    public function __toString()
    {
        fseek($this->resource, 0);

        return $this->foo.' - '.(string) stream_get_contents($this->resource);
    }
}

class TestToStringError
{
    public function __toString()
    {
        throw new \RuntimeException('Could not convert to string');
    }
}
