<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2015 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Test;

use Psy\Configuration;
use Psy\Exception\ErrorException;
use Psy\Exception\ParseErrorException;
use Psy\Shell;
use Psy\TabCompletion\Matcher\ClassMethodsMatcher;
use Symfony\Component\Console\Output\StreamOutput;

class ShellTest extends \PHPUnit_Framework_TestCase
{
    private $streams = [];

    protected function tearDown()
    {
        foreach ($this->streams as $stream) {
            fclose($stream);
        }
    }

    public function test_scope_variables()
    {
        $one = 'banana';
        $two = 123;
        $three = new \StdClass;
        $__psysh__ = 'ignore this';
        $_ = 'ignore this';
        $_e = 'ignore this';

        $shell = new Shell($this->getConfig());
        $shell->setScopeVariables(compact('one', 'two', 'three', '__psysh__', '_', '_e'));

        $this->assertNotContains('__psysh__', $shell->getScopeVariableNames());
        $this->assertEquals(['one', 'two', 'three', '_'], $shell->getScopeVariableNames());
        $this->assertEquals('banana', $shell->getScopeVariable('one'));
        $this->assertEquals(123, $shell->getScopeVariable('two'));
        $this->assertSame($three, $shell->getScopeVariable('three'));
        $this->assertNull($shell->getScopeVariable('_'));

        $shell->setScopeVariables([]);
        $this->assertEquals(['_'], $shell->getScopeVariableNames());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_unknown_scope_variables_throw_exceptions()
    {
        $shell = new Shell($this->getConfig());
        $shell->setScopeVariables(['foo' => 'FOO', 'bar' => 1]);
        $shell->getScopeVariable('baz');
    }

    public function test_includes()
    {
        $config = $this->getConfig(['configFile' => __DIR__.'/../../fixtures/empty.php']);

        $shell = new Shell($config);
        $this->assertEmpty($shell->getIncludes());
        $shell->setIncludes(['foo', 'bar', 'baz']);
        $this->assertEquals(['foo', 'bar', 'baz'], $shell->getIncludes());
    }

    public function test_includes_config()
    {
        $config = $this->getConfig([
            'defaultIncludes' => ['/file.php'],
            'configFile' => __DIR__.'/../../fixtures/empty.php',
        ]);

        $shell = new Shell($config);

        $includes = $shell->getIncludes();
        $this->assertEquals('/file.php', $includes[0]);
    }

    public function test_add_matchers_via_config()
    {
        $config = $this->getConfig([
            'tabCompletionMatchers' => [
                new ClassMethodsMatcher,
            ],
        ]);

        $matchers = $config->getTabCompletionMatchers();

        $this->assertTrue(array_pop($matchers) instanceof ClassMethodsMatcher);
    }

    public function test_rendering_exceptions()
    {
        $shell = new Shell($this->getConfig());
        $output = $this->getOutput();
        $stream = $output->getStream();
        $e = new ParseErrorException('message', 13);

        $shell->setOutput($output);
        $shell->addCode('code');
        $this->assertTrue($shell->hasCode());
        $this->assertNotEmpty($shell->getCodeBuffer());

        $shell->writeException($e);

        $this->assertSame($e, $shell->getScopeVariable('_e'));
        $this->assertFalse($shell->hasCode());
        $this->assertEmpty($shell->getCodeBuffer());

        rewind($stream);
        $streamContents = stream_get_contents($stream);

        $this->assertContains('PHP Parse error', $streamContents);
        $this->assertContains('message', $streamContents);
        $this->assertContains('line 13', $streamContents);
    }

    public function test_handling_errors()
    {
        $shell = new Shell($this->getConfig());
        $output = $this->getOutput();
        $stream = $output->getStream();
        $shell->setOutput($output);

        $oldLevel = error_reporting();
        error_reporting($oldLevel & ~E_USER_NOTICE);

        try {
            $shell->handleError(E_USER_NOTICE, 'wheee', null, 13);
        } catch (ErrorException $e) {
            error_reporting($oldLevel);
            $this->fail('Unexpected error exception');
        }
        error_reporting($oldLevel);

        rewind($stream);
        $streamContents = stream_get_contents($stream);

        $this->assertContains('PHP error:', $streamContents);
        $this->assertContains('wheee', $streamContents);
        $this->assertContains('line 13', $streamContents);
    }

    /**
     * @expectedException Psy\Exception\ErrorException
     */
    public function test_not_handling_errors()
    {
        $shell = new Shell($this->getConfig());
        $oldLevel = error_reporting();
        error_reporting($oldLevel | E_USER_NOTICE);

        try {
            $shell->handleError(E_USER_NOTICE, 'wheee', null, 13);
        } catch (ErrorException $e) {
            error_reporting($oldLevel);
            throw $e;
        }
    }

    public function test_version()
    {
        $shell = new Shell($this->getConfig());

        $this->assertInstanceOf('Symfony\Component\Console\Application', $shell);
        $this->assertContains(Shell::VERSION, $shell->getVersion());
        $this->assertContains(phpversion(), $shell->getVersion());
        $this->assertContains(php_sapi_name(), $shell->getVersion());
    }

    public function test_code_buffer()
    {
        $shell = new Shell($this->getConfig());

        $shell->addCode('class');
        $this->assertNull($shell->flushCode());
        $this->assertTrue($shell->hasCode());

        $shell->addCode('a');
        $this->assertNull($shell->flushCode());
        $this->assertTrue($shell->hasCode());

        $shell->addCode('{}');
        $code = $shell->flushCode();
        $this->assertFalse($shell->hasCode());
        $code = preg_replace('/\s+/', ' ', $code);
        $this->assertNotNull($code);
        $this->assertEquals('class a { }', $code);
    }

    public function test_keep_code_buffer_open()
    {
        $shell = new Shell($this->getConfig());

        $shell->addCode('1 \\');
        $this->assertNull($shell->flushCode());
        $this->assertTrue($shell->hasCode());

        $shell->addCode('+ 1 \\');
        $this->assertNull($shell->flushCode());
        $this->assertTrue($shell->hasCode());

        $shell->addCode('+ 1');
        $code = $shell->flushCode();
        $this->assertFalse($shell->hasCode());
        $code = preg_replace('/\s+/', ' ', $code);
        $this->assertNotNull($code);
        $this->assertEquals('return 1 + 1 + 1;', $code);
    }

    /**
     * @expectedException \Psy\Exception\ParseErrorException
     */
    public function test_code_buffer_throws_parse_exceptions()
    {
        $shell = new Shell($this->getConfig());
        $shell->addCode('this is not valid');
        $shell->flushCode();
    }

    public function test_closures_support()
    {
        $shell = new Shell($this->getConfig());
        $code = '$test = function () {}';
        $shell->addCode($code);
        $shell->flushCode();
        $code = '$test()';
        $shell->addCode($code);
        $shell->flushCode();
    }

    public function test_write_stdout()
    {
        $output = $this->getOutput();
        $stream = $output->getStream();
        $shell = new Shell($this->getConfig());
        $shell->setOutput($output);

        $shell->writeStdout("{{stdout}}\n");

        rewind($stream);
        $streamContents = stream_get_contents($stream);

        $this->assertEquals('{{stdout}}'.PHP_EOL, $streamContents);
    }

    public function test_write_stdout_without_newline()
    {
        $output = $this->getOutput();
        $stream = $output->getStream();
        $shell = new Shell($this->getConfig());
        $shell->setOutput($output);

        $shell->writeStdout('{{stdout}}');

        rewind($stream);
        $streamContents = stream_get_contents($stream);

        $this->assertEquals('{{stdout}}<aside>⏎</aside>'.PHP_EOL, $streamContents);
    }

    /**
     * @dataProvider getReturnValues
     */
    public function test_write_return_value($input, $expected)
    {
        $output = $this->getOutput();
        $stream = $output->getStream();
        $shell = new Shell($this->getConfig());
        $shell->setOutput($output);

        $shell->writeReturnValue($input);
        rewind($stream);
        $this->assertEquals($expected, stream_get_contents($stream));
    }

    public function getReturnValues()
    {
        return [
            ['{{return value}}', "=> \"\033[32m{{return value}}\033[39m\"".PHP_EOL],
            [1, "=> \033[35m1\033[39m".PHP_EOL],
        ];
    }

    /**
     * @dataProvider getRenderedExceptions
     */
    public function test_write_exception($exception, $expected)
    {
        $output = $this->getOutput();
        $stream = $output->getStream();
        $shell = new Shell($this->getConfig());
        $shell->setOutput($output);

        $shell->writeException($exception);
        rewind($stream);
        $this->assertEquals($expected, stream_get_contents($stream));
    }

    public function getRenderedExceptions()
    {
        return [
            [new \Exception('{{message}}'), "Exception with message '{{message}}'".PHP_EOL],
        ];
    }

    private function getOutput()
    {
        $stream = fopen('php://memory', 'w+');
        $this->streams[] = $stream;

        $output = new StreamOutput($stream, StreamOutput::VERBOSITY_NORMAL, false);

        return $output;
    }

    private function getConfig(array $config = [])
    {
        // Mebbe there's a better way than this?
        $dir = tempnam(sys_get_temp_dir(), 'psysh_shell_test_');
        unlink($dir);

        $defaults = [
            'configDir' => $dir,
            'dataDir' => $dir,
            'runtimeDir' => $dir,
        ];

        return new Configuration(array_merge($defaults, $config));
    }
}
