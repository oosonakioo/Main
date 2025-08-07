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

use Symfony\Component\Process\ProcessBuilder;

class ProcessBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_inherit_environment_vars()
    {
        $_ENV['MY_VAR_1'] = 'foo';

        $proc = ProcessBuilder::create()
            ->add('foo')
            ->getProcess();

        unset($_ENV['MY_VAR_1']);

        $env = $proc->getEnv();
        $this->assertArrayHasKey('MY_VAR_1', $env);
        $this->assertEquals('foo', $env['MY_VAR_1']);
    }

    public function test_add_environment_variables()
    {
        $pb = new ProcessBuilder;
        $env = [
            'foo' => 'bar',
            'foo2' => 'bar2',
        ];
        $proc = $pb
            ->add('command')
            ->setEnv('foo', 'bar2')
            ->addEnvironmentVariables($env)
            ->inheritEnvironmentVariables(false)
            ->getProcess();

        $this->assertSame($env, $proc->getEnv());
    }

    public function test_process_should_inherit_and_override_environment_vars()
    {
        $_ENV['MY_VAR_1'] = 'foo';

        $proc = ProcessBuilder::create()
            ->setEnv('MY_VAR_1', 'bar')
            ->add('foo')
            ->getProcess();

        unset($_ENV['MY_VAR_1']);

        $env = $proc->getEnv();
        $this->assertArrayHasKey('MY_VAR_1', $env);
        $this->assertEquals('bar', $env['MY_VAR_1']);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     */
    public function test_negative_timeout_from_setter()
    {
        $pb = new ProcessBuilder;
        $pb->setTimeout(-1);
    }

    public function test_null_timeout()
    {
        $pb = new ProcessBuilder;
        $pb->setTimeout(10);
        $pb->setTimeout(null);

        $r = new \ReflectionObject($pb);
        $p = $r->getProperty('timeout');
        $p->setAccessible(true);

        $this->assertNull($p->getValue($pb));
    }

    public function test_should_set_arguments()
    {
        $pb = new ProcessBuilder(['initial']);
        $pb->setArguments(['second']);

        $proc = $pb->getProcess();

        $this->assertContains('second', $proc->getCommandLine());
    }

    public function test_prefix_is_prepended_to_all_generated_process()
    {
        $pb = new ProcessBuilder;
        $pb->setPrefix('/usr/bin/php');

        $proc = $pb->setArguments(['-v'])->getProcess();
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertEquals('"/usr/bin/php" "-v"', $proc->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php' '-v'", $proc->getCommandLine());
        }

        $proc = $pb->setArguments(['-i'])->getProcess();
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertEquals('"/usr/bin/php" "-i"', $proc->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php' '-i'", $proc->getCommandLine());
        }
    }

    public function test_array_prefixes_are_prepended_to_all_generated_process()
    {
        $pb = new ProcessBuilder;
        $pb->setPrefix(['/usr/bin/php', 'composer.phar']);

        $proc = $pb->setArguments(['-v'])->getProcess();
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertEquals('"/usr/bin/php" "composer.phar" "-v"', $proc->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php' 'composer.phar' '-v'", $proc->getCommandLine());
        }

        $proc = $pb->setArguments(['-i'])->getProcess();
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertEquals('"/usr/bin/php" "composer.phar" "-i"', $proc->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php' 'composer.phar' '-i'", $proc->getCommandLine());
        }
    }

    public function test_should_escape_arguments()
    {
        $pb = new ProcessBuilder(['%path%', 'foo " bar', '%baz%baz']);
        $proc = $pb->getProcess();

        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertSame('^%"path"^% "foo \\" bar" "%baz%baz"', $proc->getCommandLine());
        } else {
            $this->assertSame("'%path%' 'foo \" bar' '%baz%baz'", $proc->getCommandLine());
        }
    }

    public function test_should_escape_arguments_and_prefix()
    {
        $pb = new ProcessBuilder(['arg']);
        $pb->setPrefix('%prefix%');
        $proc = $pb->getProcess();

        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertSame('^%"prefix"^% "arg"', $proc->getCommandLine());
        } else {
            $this->assertSame("'%prefix%' 'arg'", $proc->getCommandLine());
        }
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\LogicException
     */
    public function test_should_throw_a_logic_exception_if_no_prefix_and_no_argument()
    {
        ProcessBuilder::create()->getProcess();
    }

    public function test_should_not_throw_a_logic_exception_if_no_argument()
    {
        $process = ProcessBuilder::create()
            ->setPrefix('/usr/bin/php')
            ->getProcess();

        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertEquals('"/usr/bin/php"', $process->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php'", $process->getCommandLine());
        }
    }

    public function test_should_not_throw_a_logic_exception_if_no_prefix()
    {
        $process = ProcessBuilder::create(['/usr/bin/php'])
            ->getProcess();

        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertEquals('"/usr/bin/php"', $process->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php'", $process->getCommandLine());
        }
    }

    public function test_should_return_process_with_disabled_output()
    {
        $process = ProcessBuilder::create(['/usr/bin/php'])
            ->disableOutput()
            ->getProcess();

        $this->assertTrue($process->isOutputDisabled());
    }

    public function test_should_return_process_with_enabled_output()
    {
        $process = ProcessBuilder::create(['/usr/bin/php'])
            ->disableOutput()
            ->enableOutput()
            ->getProcess();

        $this->assertFalse($process->isOutputDisabled());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     *
     * @expectedExceptionMessage Symfony\Component\Process\ProcessBuilder::setInput only accepts strings or stream resources.
     */
    public function test_invalid_input()
    {
        $builder = ProcessBuilder::create();
        $builder->setInput([]);
    }
}
