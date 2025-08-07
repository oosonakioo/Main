<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process\Tests;

use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Pipes\PipesInterface;
use Symfony\Component\Process\Process;

/**
 * @author Robert Schönthal <seroscho@googlemail.com>
 */
class ProcessTest extends \PHPUnit_Framework_TestCase
{
    private static $phpBin;

    private static $process;

    private static $sigchild;

    private static $notEnhancedSigchild = false;

    public static function setUpBeforeClass()
    {
        $phpBin = new PhpExecutableFinder;
        self::$phpBin = getenv('SYMFONY_PROCESS_PHP_TEST_BINARY') ?: ('phpdbg' === PHP_SAPI ? 'php' : $phpBin->find());
        if ('\\' !== DIRECTORY_SEPARATOR) {
            // exec is mandatory to deal with sending a signal to the process
            // see https://github.com/symfony/symfony/issues/5030 about prepending
            // command with exec
            self::$phpBin = 'exec '.self::$phpBin;
        }

        ob_start();
        phpinfo(INFO_GENERAL);
        self::$sigchild = strpos(ob_get_clean(), '--enable-sigchild') !== false;
    }

    protected function tearDown()
    {
        if (self::$process) {
            self::$process->stop(0);
            self::$process = null;
        }
    }

    public function test_that_process_does_not_throw_warning_during_run()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('This test is transient on Windows');
        }
        @trigger_error('Test Error', E_USER_NOTICE);
        $process = $this->getProcess(self::$phpBin." -r 'sleep(3)'");
        $process->run();
        $actualError = error_get_last();
        $this->assertEquals('Test Error', $actualError['message']);
        $this->assertEquals(E_USER_NOTICE, $actualError['type']);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     */
    public function test_negative_timeout_from_constructor()
    {
        $this->getProcess('', null, null, null, -1);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     */
    public function test_negative_timeout_from_setter()
    {
        $p = $this->getProcess('');
        $p->setTimeout(-1);
    }

    public function test_float_and_null_timeout()
    {
        $p = $this->getProcess('');

        $p->setTimeout(10);
        $this->assertSame(10.0, $p->getTimeout());

        $p->setTimeout(null);
        $this->assertNull($p->getTimeout());

        $p->setTimeout(0.0);
        $this->assertNull($p->getTimeout());
    }

    /**
     * @requires extension pcntl
     */
    public function test_stop_with_timeout_is_actually_working()
    {
        $p = $this->getProcess(self::$phpBin.' '.__DIR__.'/NonStopableProcess.php 30');
        $p->start();

        while (strpos($p->getOutput(), 'received') === false) {
            usleep(1000);
        }
        $start = microtime(true);
        $p->stop(0.1);

        $p->wait();

        $this->assertLessThan(15, microtime(true) - $start);
    }

    public function test_all_output_is_actually_read_on_termination()
    {
        // this code will result in a maximum of 2 reads of 8192 bytes by calling
        // start() and isRunning().  by the time getOutput() is called the process
        // has terminated so the internal pipes array is already empty. normally
        // the call to start() will not read any data as the process will not have
        // generated output, but this is non-deterministic so we must count it as
        // a possibility.  therefore we need 2 * PipesInterface::CHUNK_SIZE plus
        // another byte which will never be read.
        $expectedOutputSize = PipesInterface::CHUNK_SIZE * 2 + 2;

        $code = sprintf('echo str_repeat(\'*\', %d);', $expectedOutputSize);
        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg($code)));

        $p->start();

        // Don't call Process::run nor Process::wait to avoid any read of pipes
        $h = new \ReflectionProperty($p, 'process');
        $h->setAccessible(true);
        $h = $h->getValue($p);
        $s = @proc_get_status($h);

        while (! empty($s['running'])) {
            usleep(1000);
            $s = proc_get_status($h);
        }

        $o = $p->getOutput();

        $this->assertEquals($expectedOutputSize, strlen($o));
    }

    public function test_callbacks_are_executed_with_start()
    {
        $process = $this->getProcess('echo foo');
        $process->start(function ($type, $buffer) use (&$data) {
            $data .= $buffer;
        });

        $process->wait();

        $this->assertSame('foo'.PHP_EOL, $data);
    }

    /**
     * tests results from sub processes.
     *
     * @dataProvider responsesCodeProvider
     */
    public function test_process_responses($expected, $getter, $code)
    {
        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg($code)));
        $p->run();

        $this->assertSame($expected, $p->$getter());
    }

    /**
     * tests results from sub processes.
     *
     * @dataProvider pipesCodeProvider
     */
    public function test_process_pipes($code, $size)
    {
        $expected = str_repeat(str_repeat('*', 1024), $size).'!';
        $expectedLength = (1024 * $size) + 1;

        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg($code)));
        $p->setInput($expected);
        $p->run();

        $this->assertEquals($expectedLength, strlen($p->getOutput()));
        $this->assertEquals($expectedLength, strlen($p->getErrorOutput()));
    }

    /**
     * @dataProvider pipesCodeProvider
     */
    public function test_set_stream_as_input($code, $size)
    {
        $expected = str_repeat(str_repeat('*', 1024), $size).'!';
        $expectedLength = (1024 * $size) + 1;

        $stream = fopen('php://temporary', 'w+');
        fwrite($stream, $expected);
        rewind($stream);

        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg($code)));
        $p->setInput($stream);
        $p->run();

        fclose($stream);

        $this->assertEquals($expectedLength, strlen($p->getOutput()));
        $this->assertEquals($expectedLength, strlen($p->getErrorOutput()));
    }

    public function test_live_stream_as_input()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, 'hello');
        rewind($stream);

        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg('stream_copy_to_stream(STDIN, STDOUT);')));
        $p->setInput($stream);
        $p->start(function ($type, $data) use ($stream) {
            if ($data === 'hello') {
                fclose($stream);
            }
        });
        $p->wait();

        $this->assertSame('hello', $p->getOutput());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\LogicException
     *
     * @expectedExceptionMessage Input can not be set while the process is running.
     */
    public function test_set_input_while_running_throws_an_exception()
    {
        $process = $this->getProcess(self::$phpBin.' -r "sleep(30);"');
        $process->start();
        try {
            $process->setInput('foobar');
            $process->stop();
            $this->fail('A LogicException should have been raised.');
        } catch (LogicException $e) {
        }
        $process->stop();

        throw $e;
    }

    /**
     * @dataProvider provideInvalidInputValues
     *
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     *
     * @expectedExceptionMessage Symfony\Component\Process\Process::setInput only accepts strings or stream resources.
     */
    public function test_invalid_input($value)
    {
        $process = $this->getProcess('foo');
        $process->setInput($value);
    }

    public function provideInvalidInputValues()
    {
        return [
            [[]],
            [new NonStringifiable],
        ];
    }

    /**
     * @dataProvider provideInputValues
     */
    public function test_valid_input($expected, $value)
    {
        $process = $this->getProcess('foo');
        $process->setInput($value);
        $this->assertSame($expected, $process->getInput());
    }

    public function provideInputValues()
    {
        return [
            [null, null],
            ['24.5', 24.5],
            ['input data', 'input data'],
        ];
    }

    public function chainedCommandsOutputProvider()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            return [
                ["2 \r\n2\r\n", '&&', '2'],
            ];
        }

        return [
            ["1\n1\n", ';', '1'],
            ["2\n2\n", '&&', '2'],
        ];
    }

    /**
     * @dataProvider chainedCommandsOutputProvider
     */
    public function test_chained_commands_output($expected, $operator, $input)
    {
        $process = $this->getProcess(sprintf('echo %s %s echo %s', $input, $operator, $input));
        $process->run();
        $this->assertEquals($expected, $process->getOutput());
    }

    public function test_callback_is_executed_for_output()
    {
        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg('echo \'foo\';')));

        $called = false;
        $p->run(function ($type, $buffer) use (&$called) {
            $called = $buffer === 'foo';
        });

        $this->assertTrue($called, 'The callback should be executed with the output');
    }

    public function test_get_error_output()
    {
        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg('$n = 0; while ($n < 3) { file_put_contents(\'php://stderr\', \'ERROR\'); $n++; }')));

        $p->run();
        $this->assertEquals(3, preg_match_all('/ERROR/', $p->getErrorOutput(), $matches));
    }

    public function test_flush_error_output()
    {
        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg('$n = 0; while ($n < 3) { file_put_contents(\'php://stderr\', \'ERROR\'); $n++; }')));

        $p->run();
        $p->clearErrorOutput();
        $this->assertEmpty($p->getErrorOutput());
    }

    /**
     * @dataProvider provideIncrementalOutput
     */
    public function test_incremental_output($getOutput, $getIncrementalOutput, $uri)
    {
        $lock = tempnam(sys_get_temp_dir(), __FUNCTION__);

        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg('file_put_contents($s = \''.$uri.'\', \'foo\'); flock(fopen('.var_export($lock, true).', \'r\'), LOCK_EX); file_put_contents($s, \'bar\');')));

        $h = fopen($lock, 'w');
        flock($h, LOCK_EX);

        $p->start();

        foreach (['foo', 'bar'] as $s) {
            while (strpos($p->$getOutput(), $s) === false) {
                usleep(1000);
            }

            $this->assertSame($s, $p->$getIncrementalOutput());
            $this->assertSame('', $p->$getIncrementalOutput());

            flock($h, LOCK_UN);
        }

        fclose($h);
    }

    public function provideIncrementalOutput()
    {
        return [
            ['getOutput', 'getIncrementalOutput', 'php://stdout'],
            ['getErrorOutput', 'getIncrementalErrorOutput', 'php://stderr'],
        ];
    }

    public function test_get_output()
    {
        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg('$n = 0; while ($n < 3) { echo \' foo \'; $n++; }')));

        $p->run();
        $this->assertEquals(3, preg_match_all('/foo/', $p->getOutput(), $matches));
    }

    public function test_flush_output()
    {
        $p = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg('$n=0;while ($n<3) {echo \' foo \';$n++;}')));

        $p->run();
        $p->clearOutput();
        $this->assertEmpty($p->getOutput());
    }

    public function test_zero_as_output()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            // see http://stackoverflow.com/questions/7105433/windows-batch-echo-without-new-line
            $p = $this->getProcess('echo | set /p dummyName=0');
        } else {
            $p = $this->getProcess('printf 0');
        }

        $p->run();
        $this->assertSame('0', $p->getOutput());
    }

    public function test_exit_code_command_failed()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Windows does not support POSIX exit code');
        }
        $this->skipIfNotEnhancedSigchild();

        // such command run in bash return an exitcode 127
        $process = $this->getProcess('nonexistingcommandIhopeneversomeonewouldnameacommandlikethis');
        $process->run();

        $this->assertGreaterThan(0, $process->getExitCode());
    }

    public function test_tty_command()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Windows does not have /dev/tty support');
        }

        $process = $this->getProcess('echo "foo" >> /dev/null && '.self::$phpBin.' -r "usleep(100000);"');
        $process->setTty(true);
        $process->start();
        $this->assertTrue($process->isRunning());
        $process->wait();

        $this->assertSame(Process::STATUS_TERMINATED, $process->getStatus());
    }

    public function test_tty_command_exit_code()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Windows does have /dev/tty support');
        }
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('echo "foo" >> /dev/null');
        $process->setTty(true);
        $process->run();

        $this->assertTrue($process->isSuccessful());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     *
     * @expectedExceptionMessage TTY mode is not supported on Windows platform.
     */
    public function test_tty_in_windows_environment()
    {
        if ('\\' !== DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('This test is for Windows platform only');
        }

        $process = $this->getProcess('echo "foo" >> /dev/null');
        $process->setTty(false);
        $process->setTty(true);
    }

    public function test_exit_code_text_is_null_when_exit_code_is_null()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('');
        $this->assertNull($process->getExitCodeText());
    }

    public function test_pty_command()
    {
        if (! Process::isPtySupported()) {
            $this->markTestSkipped('PTY is not supported on this operating system.');
        }

        $process = $this->getProcess('echo "foo"');
        $process->setPty(true);
        $process->run();

        $this->assertSame(Process::STATUS_TERMINATED, $process->getStatus());
        $this->assertEquals("foo\r\n", $process->getOutput());
    }

    public function test_must_run()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('echo foo');

        $this->assertSame($process, $process->mustRun());
        $this->assertEquals('foo'.PHP_EOL, $process->getOutput());
    }

    public function test_successful_must_run_has_correct_exit_code()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('echo foo')->mustRun();
        $this->assertEquals(0, $process->getExitCode());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function test_must_run_throws_exception()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('exit 1');
        $process->mustRun();
    }

    public function test_exit_code_text()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('');
        $r = new \ReflectionObject($process);
        $p = $r->getProperty('exitcode');
        $p->setAccessible(true);

        $p->setValue($process, 2);
        $this->assertEquals('Misuse of shell builtins', $process->getExitCodeText());
    }

    public function test_start_is_non_blocking()
    {
        $process = $this->getProcess(self::$phpBin.' -r "usleep(500000);"');
        $start = microtime(true);
        $process->start();
        $end = microtime(true);
        $this->assertLessThan(0.4, $end - $start);
        $process->stop();
    }

    public function test_update_status()
    {
        $process = $this->getProcess('echo foo');
        $process->run();
        $this->assertTrue(strlen($process->getOutput()) > 0);
    }

    public function test_get_exit_code_is_null_on_start()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess(self::$phpBin.' -r "usleep(100000);"');
        $this->assertNull($process->getExitCode());
        $process->start();
        $this->assertNull($process->getExitCode());
        $process->wait();
        $this->assertEquals(0, $process->getExitCode());
    }

    public function test_get_exit_code_is_null_on_when_starting_again()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess(self::$phpBin.' -r "usleep(100000);"');
        $process->run();
        $this->assertEquals(0, $process->getExitCode());
        $process->start();
        $this->assertNull($process->getExitCode());
        $process->wait();
        $this->assertEquals(0, $process->getExitCode());
    }

    public function test_get_exit_code()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('echo foo');
        $process->run();
        $this->assertSame(0, $process->getExitCode());
    }

    public function test_status()
    {
        $process = $this->getProcess(self::$phpBin.' -r "usleep(100000);"');
        $this->assertFalse($process->isRunning());
        $this->assertFalse($process->isStarted());
        $this->assertFalse($process->isTerminated());
        $this->assertSame(Process::STATUS_READY, $process->getStatus());
        $process->start();
        $this->assertTrue($process->isRunning());
        $this->assertTrue($process->isStarted());
        $this->assertFalse($process->isTerminated());
        $this->assertSame(Process::STATUS_STARTED, $process->getStatus());
        $process->wait();
        $this->assertFalse($process->isRunning());
        $this->assertTrue($process->isStarted());
        $this->assertTrue($process->isTerminated());
        $this->assertSame(Process::STATUS_TERMINATED, $process->getStatus());
    }

    public function test_stop()
    {
        $process = $this->getProcess(self::$phpBin.' -r "sleep(31);"');
        $process->start();
        $this->assertTrue($process->isRunning());
        $process->stop();
        $this->assertFalse($process->isRunning());
    }

    public function test_is_successful()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('echo foo');
        $process->run();
        $this->assertTrue($process->isSuccessful());
    }

    public function test_is_successful_only_after_terminated()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess(self::$phpBin.' -r "usleep(100000);"');
        $process->start();

        $this->assertFalse($process->isSuccessful());

        $process->wait();

        $this->assertTrue($process->isSuccessful());
    }

    public function test_is_not_successful()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess(self::$phpBin.' -r "throw new \Exception(\'BOUM\');"');
        $process->run();
        $this->assertFalse($process->isSuccessful());
    }

    public function test_process_is_not_signaled()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('echo foo');
        $process->run();
        $this->assertFalse($process->hasBeenSignaled());
    }

    public function test_process_without_term_signal()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('echo foo');
        $process->run();
        $this->assertEquals(0, $process->getTermSignal());
    }

    public function test_process_is_signaled_if_stopped()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess(self::$phpBin.' -r "sleep(32);"');
        $process->start();
        $process->stop();
        $this->assertTrue($process->hasBeenSignaled());
        $this->assertEquals(15, $process->getTermSignal()); // SIGTERM
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     *
     * @expectedExceptionMessage The process has been signaled
     */
    public function test_process_throws_exception_when_externally_signaled()
    {
        if (! function_exists('posix_kill')) {
            $this->markTestSkipped('Function posix_kill is required.');
        }
        $this->skipIfNotEnhancedSigchild(false);

        $process = $this->getProcess(self::$phpBin.' -r "sleep(32.1)"');
        $process->start();
        posix_kill($process->getPid(), 9); // SIGKILL

        $process->wait();
    }

    public function test_restart()
    {
        $process1 = $this->getProcess(self::$phpBin.' -r "echo getmypid();"');
        $process1->run();
        $process2 = $process1->restart();

        $process2->wait(); // wait for output

        // Ensure that both processed finished and the output is numeric
        $this->assertFalse($process1->isRunning());
        $this->assertFalse($process2->isRunning());
        $this->assertTrue(is_numeric($process1->getOutput()));
        $this->assertTrue(is_numeric($process2->getOutput()));

        // Ensure that restart returned a new process by check that the output is different
        $this->assertNotEquals($process1->getOutput(), $process2->getOutput());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessTimedOutException
     *
     * @expectedExceptionMessage exceeded the timeout of 0.1 seconds.
     */
    public function test_run_process_with_timeout()
    {
        $process = $this->getProcess(self::$phpBin.' -r "sleep(30);"');
        $process->setTimeout(0.1);
        $start = microtime(true);
        try {
            $process->run();
            $this->fail('A RuntimeException should have been raised');
        } catch (RuntimeException $e) {
        }

        $this->assertLessThan(15, microtime(true) - $start);

        throw $e;
    }

    public function test_check_timeout_on_non_started_process()
    {
        $process = $this->getProcess('echo foo');
        $this->assertNull($process->checkTimeout());
    }

    public function test_check_timeout_on_terminated_process()
    {
        $process = $this->getProcess('echo foo');
        $process->run();
        $this->assertNull($process->checkTimeout());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessTimedOutException
     *
     * @expectedExceptionMessage exceeded the timeout of 0.1 seconds.
     */
    public function test_check_timeout_on_started_process()
    {
        $process = $this->getProcess(self::$phpBin.' -r "sleep(33);"');
        $process->setTimeout(0.1);

        $process->start();
        $start = microtime(true);

        try {
            while ($process->isRunning()) {
                $process->checkTimeout();
                usleep(100000);
            }
            $this->fail('A ProcessTimedOutException should have been raised');
        } catch (ProcessTimedOutException $e) {
        }

        $this->assertLessThan(15, microtime(true) - $start);

        throw $e;
    }

    public function test_idle_timeout()
    {
        $process = $this->getProcess(self::$phpBin.' -r "sleep(34);"');
        $process->setTimeout(60);
        $process->setIdleTimeout(0.1);

        try {
            $process->run();

            $this->fail('A timeout exception was expected.');
        } catch (ProcessTimedOutException $e) {
            $this->assertTrue($e->isIdleTimeout());
            $this->assertFalse($e->isGeneralTimeout());
            $this->assertEquals(0.1, $e->getExceededTimeout());
        }
    }

    public function test_idle_timeout_not_exceeded_when_output_is_sent()
    {
        $process = $this->getProcess(sprintf('%s -r %s', self::$phpBin, escapeshellarg('while (true) {echo \'foo \'; usleep(1000);}')));
        $process->setTimeout(1);
        $process->start();

        while (strpos($process->getOutput(), 'foo') === false) {
            usleep(1000);
        }

        $process->setIdleTimeout(0.5);

        try {
            $process->wait();
            $this->fail('A timeout exception was expected.');
        } catch (ProcessTimedOutException $e) {
            $this->assertTrue($e->isGeneralTimeout(), 'A general timeout is expected.');
            $this->assertFalse($e->isIdleTimeout(), 'No idle timeout is expected.');
            $this->assertEquals(1, $e->getExceededTimeout());
        }
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessTimedOutException
     *
     * @expectedExceptionMessage exceeded the timeout of 0.1 seconds.
     */
    public function test_start_after_a_timeout()
    {
        $process = $this->getProcess(self::$phpBin.' -r "sleep(35);"');
        $process->setTimeout(0.1);

        try {
            $process->run();
            $this->fail('A ProcessTimedOutException should have been raised.');
        } catch (ProcessTimedOutException $e) {
        }
        $this->assertFalse($process->isRunning());
        $process->start();
        $this->assertTrue($process->isRunning());
        $process->stop(0);

        throw $e;
    }

    public function test_get_pid()
    {
        $process = $this->getProcess(self::$phpBin.' -r "sleep(36);"');
        $process->start();
        $this->assertGreaterThan(0, $process->getPid());
        $process->stop(0);
    }

    public function test_get_pid_is_null_before_start()
    {
        $process = $this->getProcess('foo');
        $this->assertNull($process->getPid());
    }

    public function test_get_pid_is_null_after_run()
    {
        $process = $this->getProcess('echo foo');
        $process->run();
        $this->assertNull($process->getPid());
    }

    /**
     * @requires extension pcntl
     */
    public function test_signal()
    {
        $process = $this->getProcess(self::$phpBin.' '.__DIR__.'/SignalListener.php');
        $process->start();

        while (strpos($process->getOutput(), 'Caught') === false) {
            usleep(1000);
        }
        $process->signal(SIGUSR1);
        $process->wait();

        $this->assertEquals('Caught SIGUSR1', $process->getOutput());
    }

    /**
     * @requires extension pcntl
     */
    public function test_exit_code_is_available_after_signal()
    {
        $this->skipIfNotEnhancedSigchild();

        $process = $this->getProcess('sleep 4');
        $process->start();
        $process->signal(SIGKILL);

        while ($process->isRunning()) {
            usleep(10000);
        }

        $this->assertFalse($process->isRunning());
        $this->assertTrue($process->hasBeenSignaled());
        $this->assertFalse($process->isSuccessful());
        $this->assertEquals(137, $process->getExitCode());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\LogicException
     *
     * @expectedExceptionMessage Can not send signal on a non running process.
     */
    public function test_signal_process_not_running()
    {
        $process = $this->getProcess('foo');
        $process->signal(1); // SIGHUP
    }

    /**
     * @dataProvider provideMethodsThatNeedARunningProcess
     */
    public function test_methods_that_need_a_running_process($method)
    {
        $process = $this->getProcess('foo');
        $this->setExpectedException('Symfony\Component\Process\Exception\LogicException', sprintf('Process must be started before calling %s.', $method));
        $process->{$method}();
    }

    public function provideMethodsThatNeedARunningProcess()
    {
        return [
            ['getOutput'],
            ['getIncrementalOutput'],
            ['getErrorOutput'],
            ['getIncrementalErrorOutput'],
            ['wait'],
        ];
    }

    /**
     * @dataProvider provideMethodsThatNeedATerminatedProcess
     *
     * @expectedException Symfony\Component\Process\Exception\LogicException
     *
     * @expectedExceptionMessage Process must be terminated before calling
     */
    public function test_methods_that_need_a_terminated_process($method)
    {
        $process = $this->getProcess(self::$phpBin.' -r "sleep(37);"');
        $process->start();
        try {
            $process->{$method}();
            $process->stop(0);
            $this->fail('A LogicException must have been thrown');
        } catch (\Exception $e) {
        }
        $process->stop(0);

        throw $e;
    }

    public function provideMethodsThatNeedATerminatedProcess()
    {
        return [
            ['hasBeenSignaled'],
            ['getTermSignal'],
            ['hasBeenStopped'],
            ['getStopSignal'],
        ];
    }

    /**
     * @dataProvider provideWrongSignal
     *
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     */
    public function test_wrong_signal($signal)
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('POSIX signals do not work on Windows');
        }

        $process = $this->getProcess(self::$phpBin.' -r "sleep(38);"');
        $process->start();
        try {
            $process->signal($signal);
            $this->fail('A RuntimeException must have been thrown');
        } catch (RuntimeException $e) {
            $process->stop(0);
        }

        throw $e;
    }

    public function provideWrongSignal()
    {
        return [
            [-4],
            ['Céphalopodes'],
        ];
    }

    public function test_disable_output_disables_the_output()
    {
        $p = $this->getProcess('foo');
        $this->assertFalse($p->isOutputDisabled());
        $p->disableOutput();
        $this->assertTrue($p->isOutputDisabled());
        $p->enableOutput();
        $this->assertFalse($p->isOutputDisabled());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     *
     * @expectedExceptionMessage Disabling output while the process is running is not possible.
     */
    public function test_disable_output_while_running_throws_exception()
    {
        $p = $this->getProcess(self::$phpBin.' -r "sleep(39);"');
        $p->start();
        $p->disableOutput();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     *
     * @expectedExceptionMessage Enabling output while the process is running is not possible.
     */
    public function test_enable_output_while_running_throws_exception()
    {
        $p = $this->getProcess(self::$phpBin.' -r "sleep(40);"');
        $p->disableOutput();
        $p->start();
        $p->enableOutput();
    }

    public function test_enable_or_disable_output_after_run_does_not_throw_exception()
    {
        $p = $this->getProcess('echo foo');
        $p->disableOutput();
        $p->run();
        $p->enableOutput();
        $p->disableOutput();
        $this->assertTrue($p->isOutputDisabled());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\LogicException
     *
     * @expectedExceptionMessage Output can not be disabled while an idle timeout is set.
     */
    public function test_disable_output_while_idle_timeout_is_set()
    {
        $process = $this->getProcess('foo');
        $process->setIdleTimeout(1);
        $process->disableOutput();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\LogicException
     *
     * @expectedExceptionMessage timeout can not be set while the output is disabled.
     */
    public function test_set_idle_timeout_while_output_is_disabled()
    {
        $process = $this->getProcess('foo');
        $process->disableOutput();
        $process->setIdleTimeout(1);
    }

    public function test_set_null_idle_timeout_while_output_is_disabled()
    {
        $process = $this->getProcess('foo');
        $process->disableOutput();
        $this->assertSame($process, $process->setIdleTimeout(null));
    }

    /**
     * @dataProvider provideStartMethods
     */
    public function test_start_with_a_callback_and_disabled_output($startMethod, $exception, $exceptionMessage)
    {
        $p = $this->getProcess('foo');
        $p->disableOutput();
        $this->setExpectedException($exception, $exceptionMessage);
        if ($startMethod === 'mustRun') {
            $this->skipIfNotEnhancedSigchild();
        }
        $p->{$startMethod}(function () {});
    }

    public function provideStartMethods()
    {
        return [
            ['start', 'Symfony\Component\Process\Exception\LogicException', 'Output has been disabled, enable it to allow the use of a callback.'],
            ['run', 'Symfony\Component\Process\Exception\LogicException', 'Output has been disabled, enable it to allow the use of a callback.'],
            ['mustRun', 'Symfony\Component\Process\Exception\LogicException', 'Output has been disabled, enable it to allow the use of a callback.'],
        ];
    }

    /**
     * @dataProvider provideOutputFetchingMethods
     *
     * @expectedException \Symfony\Component\Process\Exception\LogicException
     *
     * @expectedExceptionMessage Output has been disabled.
     */
    public function test_get_output_while_disabled($fetchMethod)
    {
        $p = $this->getProcess(self::$phpBin.' -r "sleep(41);"');
        $p->disableOutput();
        $p->start();
        $p->{$fetchMethod}();
    }

    public function provideOutputFetchingMethods()
    {
        return [
            ['getOutput'],
            ['getIncrementalOutput'],
            ['getErrorOutput'],
            ['getIncrementalErrorOutput'],
        ];
    }

    public function test_stop_terminates_process_cleanly()
    {
        $process = $this->getProcess(self::$phpBin.' -r "echo 123; sleep(42);"');
        $process->run(function () use ($process) {
            $process->stop();
        });
        $this->assertTrue(true, 'A call to stop() is not expected to cause wait() to throw a RuntimeException');
    }

    public function test_kill_signal_terminates_process_cleanly()
    {
        $process = $this->getProcess(self::$phpBin.' -r "echo 123; sleep(43);"');
        $process->run(function () use ($process) {
            $process->signal(9); // SIGKILL
        });
        $this->assertTrue(true, 'A call to signal() is not expected to cause wait() to throw a RuntimeException');
    }

    public function test_term_signal_terminates_process_cleanly()
    {
        $process = $this->getProcess(self::$phpBin.' -r "echo 123; sleep(44);"');
        $process->run(function () use ($process) {
            $process->signal(15); // SIGTERM
        });
        $this->assertTrue(true, 'A call to signal() is not expected to cause wait() to throw a RuntimeException');
    }

    public function responsesCodeProvider()
    {
        return [
            // expected output / getter / code to execute
            // array(1,'getExitCode','exit(1);'),
            // array(true,'isSuccessful','exit();'),
            ['output', 'getOutput', 'echo \'output\';'],
        ];
    }

    public function pipesCodeProvider()
    {
        $variations = [
            'fwrite(STDOUT, $in = file_get_contents(\'php://stdin\')); fwrite(STDERR, $in);',
            'include \''.__DIR__.'/PipeStdinInStdoutStdErrStreamSelect.php\';',
        ];

        if ('\\' === DIRECTORY_SEPARATOR) {
            // Avoid XL buffers on Windows because of https://bugs.php.net/bug.php?id=65650
            $sizes = [1, 2, 4, 8];
        } else {
            $sizes = [1, 16, 64, 1024, 4096];
        }

        $codes = [];
        foreach ($sizes as $size) {
            foreach ($variations as $code) {
                $codes[] = [$code, $size];
            }
        }

        return $codes;
    }

    /**
     * @dataProvider provideVariousIncrementals
     */
    public function test_incremental_output_does_not_require_another_call($stream, $method)
    {
        $process = $this->getProcess(self::$phpBin.' -r '.escapeshellarg('$n = 0; while ($n < 3) { file_put_contents(\''.$stream.'\', $n, 1); $n++; usleep(1000); }'), null, null, null, null);
        $process->start();
        $result = '';
        $limit = microtime(true) + 3;
        $expected = '012';

        while ($result !== $expected && microtime(true) < $limit) {
            $result .= $process->$method();
        }

        $this->assertSame($expected, $result);
        $process->stop();
    }

    public function provideVariousIncrementals()
    {
        return [
            ['php://stdout', 'getIncrementalOutput'],
            ['php://stderr', 'getIncrementalErrorOutput'],
        ];
    }

    /**
     * @param  string  $commandline
     * @param  null|string  $cwd
     * @param  null|string  $input
     * @param  int  $timeout
     * @return Process
     */
    private function getProcess($commandline, $cwd = null, ?array $env = null, $input = null, $timeout = 60, array $options = [])
    {
        $process = new Process($commandline, $cwd, $env, $input, $timeout, $options);

        if (false !== $enhance = getenv('ENHANCE_SIGCHLD')) {
            try {
                $process->setEnhanceSigchildCompatibility(false);
                $process->getExitCode();
                $this->fail('ENHANCE_SIGCHLD must be used together with a sigchild-enabled PHP.');
            } catch (RuntimeException $e) {
                $this->assertSame('This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.', $e->getMessage());
                if ($enhance) {
                    $process->setEnhanceSigchildCompatibility(true);
                } else {
                    self::$notEnhancedSigchild = true;
                }
            }
        }

        if (self::$process) {
            self::$process->stop(0);
        }

        return self::$process = $process;
    }

    private function skipIfNotEnhancedSigchild($expectException = true)
    {
        if (self::$sigchild) {
            if (! $expectException) {
                $this->markTestSkipped('PHP is compiled with --enable-sigchild.');
            } elseif (self::$notEnhancedSigchild) {
                $this->setExpectedException('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild.');
            }
        }
    }
}

class NonStringifiable {}
