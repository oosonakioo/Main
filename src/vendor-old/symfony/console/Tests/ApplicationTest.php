<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::$fixturesPath = realpath(__DIR__.'/Fixtures/');
        require_once self::$fixturesPath.'/FooCommand.php';
        require_once self::$fixturesPath.'/Foo1Command.php';
        require_once self::$fixturesPath.'/Foo2Command.php';
        require_once self::$fixturesPath.'/Foo3Command.php';
        require_once self::$fixturesPath.'/Foo4Command.php';
        require_once self::$fixturesPath.'/Foo5Command.php';
        require_once self::$fixturesPath.'/FoobarCommand.php';
        require_once self::$fixturesPath.'/BarBucCommand.php';
        require_once self::$fixturesPath.'/FooSubnamespaced1Command.php';
        require_once self::$fixturesPath.'/FooSubnamespaced2Command.php';
    }

    protected function normalizeLineBreaks($text)
    {
        return str_replace(PHP_EOL, "\n", $text);
    }

    /**
     * Replaces the dynamic placeholders of the command help text with a static version.
     * The placeholder %command.full_name% includes the script path that is not predictable
     * and can not be tested against.
     */
    protected function ensureStaticCommandHelp(Application $application)
    {
        foreach ($application->all() as $command) {
            $command->setHelp(str_replace('%command.full_name%', 'app/console %command.name%', $command->getHelp()));
        }
    }

    public function test_constructor()
    {
        $application = new Application('foo', 'bar');
        $this->assertEquals('foo', $application->getName(), '__construct() takes the application name as its first argument');
        $this->assertEquals('bar', $application->getVersion(), '__construct() takes the application version as its second argument');
        $this->assertEquals(['help', 'list'], array_keys($application->all()), '__construct() registered the help and list commands by default');
    }

    public function test_set_get_name()
    {
        $application = new Application;
        $application->setName('foo');
        $this->assertEquals('foo', $application->getName(), '->setName() sets the name of the application');
    }

    public function test_set_get_version()
    {
        $application = new Application;
        $application->setVersion('bar');
        $this->assertEquals('bar', $application->getVersion(), '->setVersion() sets the version of the application');
    }

    public function test_get_long_version()
    {
        $application = new Application('foo', 'bar');
        $this->assertEquals('<info>foo</info> version <comment>bar</comment>', $application->getLongVersion(), '->getLongVersion() returns the long version of the application');
    }

    public function test_help()
    {
        $application = new Application;
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_gethelp.txt', $this->normalizeLineBreaks($application->getHelp()), '->getHelp() returns a help message');
    }

    public function test_all()
    {
        $application = new Application;
        $commands = $application->all();
        $this->assertInstanceOf('Symfony\\Component\\Console\\Command\\HelpCommand', $commands['help'], '->all() returns the registered commands');

        $application->add(new \FooCommand);
        $commands = $application->all('foo');
        $this->assertCount(1, $commands, '->all() takes a namespace as its first argument');
    }

    public function test_register()
    {
        $application = new Application;
        $command = $application->register('foo');
        $this->assertEquals('foo', $command->getName(), '->register() registers a new command');
    }

    public function test_add()
    {
        $application = new Application;
        $application->add($foo = new \FooCommand);
        $commands = $application->all();
        $this->assertEquals($foo, $commands['foo:bar'], '->add() registers a command');

        $application = new Application;
        $application->addCommands([$foo = new \FooCommand, $foo1 = new \Foo1Command]);
        $commands = $application->all();
        $this->assertEquals([$foo, $foo1], [$commands['foo:bar'], $commands['foo:bar1']], '->addCommands() registers an array of commands');
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Command class "Foo5Command" is not correctly initialized. You probably forgot to call the parent constructor.
     */
    public function test_add_command_with_empty_constructor()
    {
        $application = new Application;
        $application->add(new \Foo5Command);
    }

    public function test_has_get()
    {
        $application = new Application;
        $this->assertTrue($application->has('list'), '->has() returns true if a named command is registered');
        $this->assertFalse($application->has('afoobar'), '->has() returns false if a named command is not registered');

        $application->add($foo = new \FooCommand);
        $this->assertTrue($application->has('afoobar'), '->has() returns true if an alias is registered');
        $this->assertEquals($foo, $application->get('foo:bar'), '->get() returns a command by name');
        $this->assertEquals($foo, $application->get('afoobar'), '->get() returns a command by alias');

        $application = new Application;
        $application->add($foo = new \FooCommand);
        // simulate --help
        $r = new \ReflectionObject($application);
        $p = $r->getProperty('wantHelps');
        $p->setAccessible(true);
        $p->setValue($application, true);
        $command = $application->get('foo:bar');
        $this->assertInstanceOf('Symfony\Component\Console\Command\HelpCommand', $command, '->get() returns the help command if --help is provided as the input');
    }

    public function test_silent_help()
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $tester = new ApplicationTester($application);
        $tester->run(['-h' => true, '-q' => true], ['decorated' => false]);

        $this->assertEmpty($tester->getDisplay(true));
    }

    /**
     * @expectedException        Symfony\Component\Console\Exception\CommandNotFoundException
     *
     * @expectedExceptionMessage The command "foofoo" does not exist.
     */
    public function test_get_invalid_command()
    {
        $application = new Application;
        $application->get('foofoo');
    }

    public function test_get_namespaces()
    {
        $application = new Application;
        $application->add(new \FooCommand);
        $application->add(new \Foo1Command);
        $this->assertEquals(['foo'], $application->getNamespaces(), '->getNamespaces() returns an array of unique used namespaces');
    }

    public function test_find_namespace()
    {
        $application = new Application;
        $application->add(new \FooCommand);
        $this->assertEquals('foo', $application->findNamespace('foo'), '->findNamespace() returns the given namespace if it exists');
        $this->assertEquals('foo', $application->findNamespace('f'), '->findNamespace() finds a namespace given an abbreviation');
        $application->add(new \Foo2Command);
        $this->assertEquals('foo', $application->findNamespace('foo'), '->findNamespace() returns the given namespace if it exists');
    }

    public function test_find_namespace_with_subnamespaces()
    {
        $application = new Application;
        $application->add(new \FooSubnamespaced1Command);
        $application->add(new \FooSubnamespaced2Command);
        $this->assertEquals('foo', $application->findNamespace('foo'), '->findNamespace() returns commands even if the commands are only contained in subnamespaces');
    }

    /**
     * @expectedException        Symfony\Component\Console\Exception\CommandNotFoundException
     *
     * @expectedExceptionMessage The namespace "f" is ambiguous (foo, foo1).
     */
    public function test_find_ambiguous_namespace()
    {
        $application = new Application;
        $application->add(new \BarBucCommand);
        $application->add(new \FooCommand);
        $application->add(new \Foo2Command);
        $application->findNamespace('f');
    }

    /**
     * @expectedException        Symfony\Component\Console\Exception\CommandNotFoundException
     *
     * @expectedExceptionMessage There are no commands defined in the "bar" namespace.
     */
    public function test_find_invalid_namespace()
    {
        $application = new Application;
        $application->findNamespace('bar');
    }

    /**
     * @expectedException        Symfony\Component\Console\Exception\CommandNotFoundException
     *
     * @expectedExceptionMessage Command "foo1" is not defined
     */
    public function test_find_unique_name_but_namespace_name()
    {
        $application = new Application;
        $application->add(new \FooCommand);
        $application->add(new \Foo1Command);
        $application->add(new \Foo2Command);

        $application->find($commandName = 'foo1');
    }

    public function test_find()
    {
        $application = new Application;
        $application->add(new \FooCommand);

        $this->assertInstanceOf('FooCommand', $application->find('foo:bar'), '->find() returns a command if its name exists');
        $this->assertInstanceOf('Symfony\Component\Console\Command\HelpCommand', $application->find('h'), '->find() returns a command if its name exists');
        $this->assertInstanceOf('FooCommand', $application->find('f:bar'), '->find() returns a command if the abbreviation for the namespace exists');
        $this->assertInstanceOf('FooCommand', $application->find('f:b'), '->find() returns a command if the abbreviation for the namespace and the command name exist');
        $this->assertInstanceOf('FooCommand', $application->find('a'), '->find() returns a command if the abbreviation exists for an alias');
    }

    /**
     * @dataProvider provideAmbiguousAbbreviations
     */
    public function test_find_with_ambiguous_abbreviations($abbreviation, $expectedExceptionMessage)
    {
        $this->setExpectedException('Symfony\Component\Console\Exception\CommandNotFoundException', $expectedExceptionMessage);

        $application = new Application;
        $application->add(new \FooCommand);
        $application->add(new \Foo1Command);
        $application->add(new \Foo2Command);

        $application->find($abbreviation);
    }

    public function provideAmbiguousAbbreviations()
    {
        return [
            ['f', 'Command "f" is not defined.'],
            ['a', 'Command "a" is ambiguous (afoobar, afoobar1 and 1 more).'],
            ['foo:b', 'Command "foo:b" is ambiguous (foo:bar, foo:bar1 and 1 more).'],
        ];
    }

    public function test_find_command_equal_namespace()
    {
        $application = new Application;
        $application->add(new \Foo3Command);
        $application->add(new \Foo4Command);

        $this->assertInstanceOf('Foo3Command', $application->find('foo3:bar'), '->find() returns the good command even if a namespace has same name');
        $this->assertInstanceOf('Foo4Command', $application->find('foo3:bar:toh'), '->find() returns a command even if its namespace equals another command name');
    }

    public function test_find_command_with_ambiguous_namespaces_but_unique_name()
    {
        $application = new Application;
        $application->add(new \FooCommand);
        $application->add(new \FoobarCommand);

        $this->assertInstanceOf('FoobarCommand', $application->find('f:f'));
    }

    public function test_find_command_with_missing_namespace()
    {
        $application = new Application;
        $application->add(new \Foo4Command);

        $this->assertInstanceOf('Foo4Command', $application->find('f::t'));
    }

    /**
     * @dataProvider             provideInvalidCommandNamesSingle
     *
     * @expectedException        Symfony\Component\Console\Exception\CommandNotFoundException
     *
     * @expectedExceptionMessage Did you mean this
     */
    public function test_find_alternative_exception_message_single($name)
    {
        $application = new Application;
        $application->add(new \Foo3Command);
        $application->find($name);
    }

    public function provideInvalidCommandNamesSingle()
    {
        return [
            ['foo3:baR'],
            ['foO3:bar'],
        ];
    }

    public function test_find_alternative_exception_message_multiple()
    {
        $application = new Application;
        $application->add(new \FooCommand);
        $application->add(new \Foo1Command);
        $application->add(new \Foo2Command);

        // Command + plural
        try {
            $application->find('foo:baR');
            $this->fail('->find() throws a CommandNotFoundException if command does not exist, with alternatives');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Console\Exception\CommandNotFoundException', $e, '->find() throws a CommandNotFoundException if command does not exist, with alternatives');
            $this->assertRegExp('/Did you mean one of these/', $e->getMessage(), '->find() throws a CommandNotFoundException if command does not exist, with alternatives');
            $this->assertRegExp('/foo1:bar/', $e->getMessage());
            $this->assertRegExp('/foo:bar/', $e->getMessage());
        }

        // Namespace + plural
        try {
            $application->find('foo2:bar');
            $this->fail('->find() throws a CommandNotFoundException if command does not exist, with alternatives');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Console\Exception\CommandNotFoundException', $e, '->find() throws a CommandNotFoundException if command does not exist, with alternatives');
            $this->assertRegExp('/Did you mean one of these/', $e->getMessage(), '->find() throws a CommandNotFoundException if command does not exist, with alternatives');
            $this->assertRegExp('/foo1/', $e->getMessage());
        }

        $application->add(new \Foo3Command);
        $application->add(new \Foo4Command);

        // Subnamespace + plural
        try {
            $a = $application->find('foo3:');
            $this->fail('->find() should throw an Symfony\Component\Console\Exception\CommandNotFoundException if a command is ambiguous because of a subnamespace, with alternatives');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Console\Exception\CommandNotFoundException', $e);
            $this->assertRegExp('/foo3:bar/', $e->getMessage());
            $this->assertRegExp('/foo3:bar:toh/', $e->getMessage());
        }
    }

    public function test_find_alternative_commands()
    {
        $application = new Application;

        $application->add(new \FooCommand);
        $application->add(new \Foo1Command);
        $application->add(new \Foo2Command);

        try {
            $application->find($commandName = 'Unknown command');
            $this->fail('->find() throws a CommandNotFoundException if command does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Console\Exception\CommandNotFoundException', $e, '->find() throws a CommandNotFoundException if command does not exist');
            $this->assertSame([], $e->getAlternatives());
            $this->assertEquals(sprintf('Command "%s" is not defined.', $commandName), $e->getMessage(), '->find() throws a CommandNotFoundException if command does not exist, without alternatives');
        }

        // Test if "bar1" command throw a "CommandNotFoundException" and does not contain
        // "foo:bar" as alternative because "bar1" is too far from "foo:bar"
        try {
            $application->find($commandName = 'bar1');
            $this->fail('->find() throws a CommandNotFoundException if command does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Console\Exception\CommandNotFoundException', $e, '->find() throws a CommandNotFoundException if command does not exist');
            $this->assertSame(['afoobar1', 'foo:bar1'], $e->getAlternatives());
            $this->assertRegExp(sprintf('/Command "%s" is not defined./', $commandName), $e->getMessage(), '->find() throws a CommandNotFoundException if command does not exist, with alternatives');
            $this->assertRegExp('/afoobar1/', $e->getMessage(), '->find() throws a CommandNotFoundException if command does not exist, with alternative : "afoobar1"');
            $this->assertRegExp('/foo:bar1/', $e->getMessage(), '->find() throws a CommandNotFoundException if command does not exist, with alternative : "foo:bar1"');
            $this->assertNotRegExp('/foo:bar(?>!1)/', $e->getMessage(), '->find() throws a CommandNotFoundException if command does not exist, without "foo:bar" alternative');
        }
    }

    public function test_find_alternative_commands_with_an_alias()
    {
        $fooCommand = new \FooCommand;
        $fooCommand->setAliases(['foo2']);

        $application = new Application;
        $application->add($fooCommand);

        $result = $application->find('foo');

        $this->assertSame($fooCommand, $result);
    }

    public function test_find_alternative_namespace()
    {
        $application = new Application;

        $application->add(new \FooCommand);
        $application->add(new \Foo1Command);
        $application->add(new \Foo2Command);
        $application->add(new \Foo3Command);

        try {
            $application->find('Unknown-namespace:Unknown-command');
            $this->fail('->find() throws a CommandNotFoundException if namespace does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Console\Exception\CommandNotFoundException', $e, '->find() throws a CommandNotFoundException if namespace does not exist');
            $this->assertSame([], $e->getAlternatives());
            $this->assertEquals('There are no commands defined in the "Unknown-namespace" namespace.', $e->getMessage(), '->find() throws a CommandNotFoundException if namespace does not exist, without alternatives');
        }

        try {
            $application->find('foo2:command');
            $this->fail('->find() throws a CommandNotFoundException if namespace does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Console\Exception\CommandNotFoundException', $e, '->find() throws a CommandNotFoundException if namespace does not exist');
            $this->assertCount(3, $e->getAlternatives());
            $this->assertContains('foo', $e->getAlternatives());
            $this->assertContains('foo1', $e->getAlternatives());
            $this->assertContains('foo3', $e->getAlternatives());
            $this->assertRegExp('/There are no commands defined in the "foo2" namespace./', $e->getMessage(), '->find() throws a CommandNotFoundException if namespace does not exist, with alternative');
            $this->assertRegExp('/foo/', $e->getMessage(), '->find() throws a CommandNotFoundException if namespace does not exist, with alternative : "foo"');
            $this->assertRegExp('/foo1/', $e->getMessage(), '->find() throws a CommandNotFoundException if namespace does not exist, with alternative : "foo1"');
            $this->assertRegExp('/foo3/', $e->getMessage(), '->find() throws a CommandNotFoundException if namespace does not exist, with alternative : "foo3"');
        }
    }

    public function test_find_namespace_does_not_fail_on_deep_similar_namespaces()
    {
        $application = $this->getMock('Symfony\Component\Console\Application', ['getNamespaces']);
        $application->expects($this->once())
            ->method('getNamespaces')
            ->will($this->returnValue(['foo:sublong', 'bar:sub']));

        $this->assertEquals('foo:sublong', $application->findNamespace('f:sub'));
    }

    /**
     * @expectedException Symfony\Component\Console\Exception\CommandNotFoundException
     *
     * @expectedExceptionMessage Command "foo::bar" is not defined.
     */
    public function test_find_with_double_colon_in_name_throws_exception()
    {
        $application = new Application;
        $application->add(new \FooCommand);
        $application->add(new \Foo4Command);
        $application->find('foo::bar');
    }

    public function test_set_catch_exceptions()
    {
        $application = $this->getMock('Symfony\Component\Console\Application', ['getTerminalWidth']);
        $application->setAutoExit(false);
        $application->expects($this->any())
            ->method('getTerminalWidth')
            ->will($this->returnValue(120));
        $tester = new ApplicationTester($application);

        $application->setCatchExceptions(true);
        $tester->run(['command' => 'foo'], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception1.txt', $tester->getDisplay(true), '->setCatchExceptions() sets the catch exception flag');

        $application->setCatchExceptions(false);
        try {
            $tester->run(['command' => 'foo'], ['decorated' => false]);
            $this->fail('->setCatchExceptions() sets the catch exception flag');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Exception', $e, '->setCatchExceptions() sets the catch exception flag');
            $this->assertEquals('Command "foo" is not defined.', $e->getMessage(), '->setCatchExceptions() sets the catch exception flag');
        }
    }

    public function test_render_exception()
    {
        $application = $this->getMock('Symfony\Component\Console\Application', ['getTerminalWidth']);
        $application->setAutoExit(false);
        $application->expects($this->any())
            ->method('getTerminalWidth')
            ->will($this->returnValue(120));
        $tester = new ApplicationTester($application);

        $tester->run(['command' => 'foo'], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception1.txt', $tester->getDisplay(true), '->renderException() renders a pretty exception');

        $tester->run(['command' => 'foo'], ['decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE]);
        $this->assertContains('Exception trace', $tester->getDisplay(), '->renderException() renders a pretty exception with a stack trace when verbosity is verbose');

        $tester->run(['command' => 'list', '--foo' => true], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception2.txt', $tester->getDisplay(true), '->renderException() renders the command synopsis when an exception occurs in the context of a command');

        $application->add(new \Foo3Command);
        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo3:bar'], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception3.txt', $tester->getDisplay(true), '->renderException() renders a pretty exceptions with previous exceptions');

        $tester->run(['command' => 'foo3:bar'], ['decorated' => true]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception3decorated.txt', $tester->getDisplay(true), '->renderException() renders a pretty exceptions with previous exceptions');

        $application = $this->getMock('Symfony\Component\Console\Application', ['getTerminalWidth']);
        $application->setAutoExit(false);
        $application->expects($this->any())
            ->method('getTerminalWidth')
            ->will($this->returnValue(32));
        $tester = new ApplicationTester($application);

        $tester->run(['command' => 'foo'], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception4.txt', $tester->getDisplay(true), '->renderException() wraps messages when they are bigger than the terminal');
    }

    public function test_render_exception_with_double_width_characters()
    {
        $application = $this->getMock('Symfony\Component\Console\Application', ['getTerminalWidth']);
        $application->setAutoExit(false);
        $application->expects($this->any())
            ->method('getTerminalWidth')
            ->will($this->returnValue(120));
        $application->register('foo')->setCode(function () {
            throw new \Exception('エラーメッセージ');
        });
        $tester = new ApplicationTester($application);

        $tester->run(['command' => 'foo'], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception_doublewidth1.txt', $tester->getDisplay(true), '->renderException() renders a pretty exceptions with previous exceptions');

        $tester->run(['command' => 'foo'], ['decorated' => true]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception_doublewidth1decorated.txt', $tester->getDisplay(true), '->renderException() renders a pretty exceptions with previous exceptions');

        $application = $this->getMock('Symfony\Component\Console\Application', ['getTerminalWidth']);
        $application->setAutoExit(false);
        $application->expects($this->any())
            ->method('getTerminalWidth')
            ->will($this->returnValue(32));
        $application->register('foo')->setCode(function () {
            throw new \Exception('コマンドの実行中にエラーが発生しました。');
        });
        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo'], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_renderexception_doublewidth2.txt', $tester->getDisplay(true), '->renderException() wraps messages when they are bigger than the terminal');
    }

    public function test_run()
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->add($command = new \Foo1Command);
        $_SERVER['argv'] = ['cli.php', 'foo:bar1'];

        ob_start();
        $application->run();
        ob_end_clean();

        $this->assertInstanceOf('Symfony\Component\Console\Input\ArgvInput', $command->input, '->run() creates an ArgvInput by default if none is given');
        $this->assertInstanceOf('Symfony\Component\Console\Output\ConsoleOutput', $command->output, '->run() creates a ConsoleOutput by default if none is given');

        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $this->ensureStaticCommandHelp($application);
        $tester = new ApplicationTester($application);

        $tester->run([], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run1.txt', $tester->getDisplay(true), '->run() runs the list command if no argument is passed');

        $tester->run(['--help' => true], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run2.txt', $tester->getDisplay(true), '->run() runs the help command if --help is passed');

        $tester->run(['-h' => true], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run2.txt', $tester->getDisplay(true), '->run() runs the help command if -h is passed');

        $tester->run(['command' => 'list', '--help' => true], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run3.txt', $tester->getDisplay(true), '->run() displays the help if --help is passed');

        $tester->run(['command' => 'list', '-h' => true], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run3.txt', $tester->getDisplay(true), '->run() displays the help if -h is passed');

        $tester->run(['--ansi' => true]);
        $this->assertTrue($tester->getOutput()->isDecorated(), '->run() forces color output if --ansi is passed');

        $tester->run(['--no-ansi' => true]);
        $this->assertFalse($tester->getOutput()->isDecorated(), '->run() forces color output to be disabled if --no-ansi is passed');

        $tester->run(['--version' => true], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run4.txt', $tester->getDisplay(true), '->run() displays the program version if --version is passed');

        $tester->run(['-V' => true], ['decorated' => false]);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run4.txt', $tester->getDisplay(true), '->run() displays the program version if -v is passed');

        $tester->run(['command' => 'list', '--quiet' => true]);
        $this->assertSame('', $tester->getDisplay(), '->run() removes all output if --quiet is passed');

        $tester->run(['command' => 'list', '-q' => true]);
        $this->assertSame('', $tester->getDisplay(), '->run() removes all output if -q is passed');

        $tester->run(['command' => 'list', '--verbose' => true]);
        $this->assertSame(Output::VERBOSITY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if --verbose is passed');

        $tester->run(['command' => 'list', '--verbose' => 1]);
        $this->assertSame(Output::VERBOSITY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if --verbose=1 is passed');

        $tester->run(['command' => 'list', '--verbose' => 2]);
        $this->assertSame(Output::VERBOSITY_VERY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to very verbose if --verbose=2 is passed');

        $tester->run(['command' => 'list', '--verbose' => 3]);
        $this->assertSame(Output::VERBOSITY_DEBUG, $tester->getOutput()->getVerbosity(), '->run() sets the output to debug if --verbose=3 is passed');

        $tester->run(['command' => 'list', '--verbose' => 4]);
        $this->assertSame(Output::VERBOSITY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if unknown --verbose level is passed');

        $tester->run(['command' => 'list', '-v' => true]);
        $this->assertSame(Output::VERBOSITY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if -v is passed');

        $tester->run(['command' => 'list', '-vv' => true]);
        $this->assertSame(Output::VERBOSITY_VERY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if -v is passed');

        $tester->run(['command' => 'list', '-vvv' => true]);
        $this->assertSame(Output::VERBOSITY_DEBUG, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if -v is passed');

        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->add(new \FooCommand);
        $tester = new ApplicationTester($application);

        $tester->run(['command' => 'foo:bar', '--no-interaction' => true], ['decorated' => false]);
        $this->assertSame('called'.PHP_EOL, $tester->getDisplay(), '->run() does not call interact() if --no-interaction is passed');

        $tester->run(['command' => 'foo:bar', '-n' => true], ['decorated' => false]);
        $this->assertSame('called'.PHP_EOL, $tester->getDisplay(), '->run() does not call interact() if -n is passed');
    }

    /**
     * Issue #9285.
     *
     * If the "verbose" option is just before an argument in ArgvInput,
     * an argument value should not be treated as verbosity value.
     * This test will fail with "Not enough arguments." if broken
     */
    public function test_verbose_value_not_break_arguments()
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->add(new \FooCommand);

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        $input = new ArgvInput(['cli.php', '-v', 'foo:bar']);
        $application->run($input, $output);

        $input = new ArgvInput(['cli.php', '--verbose', 'foo:bar']);
        $application->run($input, $output);
    }

    public function test_run_returns_integer_exit_code()
    {
        $exception = new \Exception('', 4);

        $application = $this->getMock('Symfony\Component\Console\Application', ['doRun']);
        $application->setAutoExit(false);
        $application->expects($this->once())
            ->method('doRun')
            ->will($this->throwException($exception));

        $exitCode = $application->run(new ArrayInput([]), new NullOutput);

        $this->assertSame(4, $exitCode, '->run() returns integer exit code extracted from raised exception');
    }

    public function test_run_returns_exit_code_one_for_exception_code_zero()
    {
        $exception = new \Exception('', 0);

        $application = $this->getMock('Symfony\Component\Console\Application', ['doRun']);
        $application->setAutoExit(false);
        $application->expects($this->once())
            ->method('doRun')
            ->will($this->throwException($exception));

        $exitCode = $application->run(new ArrayInput([]), new NullOutput);

        $this->assertSame(1, $exitCode, '->run() returns exit code 1 when exception code is 0');
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage An option with shortcut "e" already exists.
     */
    public function test_adding_option_with_duplicate_shortcut()
    {
        $dispatcher = new EventDispatcher;
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->setDispatcher($dispatcher);

        $application->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'Environment'));

        $application
            ->register('foo')
            ->setAliases(['f'])
            ->setDefinition([new InputOption('survey', 'e', InputOption::VALUE_REQUIRED, 'My option with a shortcut.')])
            ->setCode(function (InputInterface $input, OutputInterface $output) {});

        $input = new ArrayInput(['command' => 'foo']);
        $output = new NullOutput;

        $application->run($input, $output);
    }

    /**
     * @expectedException \LogicException
     *
     * @dataProvider getAddingAlreadySetDefinitionElementData
     */
    public function test_adding_already_set_definition_element_data($def)
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application
            ->register('foo')
            ->setDefinition([$def])
            ->setCode(function (InputInterface $input, OutputInterface $output) {});

        $input = new ArrayInput(['command' => 'foo']);
        $output = new NullOutput;
        $application->run($input, $output);
    }

    public function getAddingAlreadySetDefinitionElementData()
    {
        return [
            [new InputArgument('command', InputArgument::REQUIRED)],
            [new InputOption('quiet', '', InputOption::VALUE_NONE)],
            [new InputOption('query', 'q', InputOption::VALUE_NONE)],
        ];
    }

    public function test_get_default_helper_set_returns_default_values()
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $helperSet = $application->getHelperSet();

        $this->assertTrue($helperSet->has('formatter'));
    }

    public function test_adding_single_helper_set_overwrites_default_values()
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $application->setHelperSet(new HelperSet([new FormatterHelper]));

        $helperSet = $application->getHelperSet();

        $this->assertTrue($helperSet->has('formatter'));

        // no other default helper set should be returned
        $this->assertFalse($helperSet->has('dialog'));
        $this->assertFalse($helperSet->has('progress'));
    }

    public function test_overwriting_default_helper_set_overwrites_default_values()
    {
        $application = new CustomApplication;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $application->setHelperSet(new HelperSet([new FormatterHelper]));

        $helperSet = $application->getHelperSet();

        $this->assertTrue($helperSet->has('formatter'));

        // no other default helper set should be returned
        $this->assertFalse($helperSet->has('dialog'));
        $this->assertFalse($helperSet->has('progress'));
    }

    public function test_get_default_input_definition_returns_default_values()
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $inputDefinition = $application->getDefinition();

        $this->assertTrue($inputDefinition->hasArgument('command'));

        $this->assertTrue($inputDefinition->hasOption('help'));
        $this->assertTrue($inputDefinition->hasOption('quiet'));
        $this->assertTrue($inputDefinition->hasOption('verbose'));
        $this->assertTrue($inputDefinition->hasOption('version'));
        $this->assertTrue($inputDefinition->hasOption('ansi'));
        $this->assertTrue($inputDefinition->hasOption('no-ansi'));
        $this->assertTrue($inputDefinition->hasOption('no-interaction'));
    }

    public function test_overwriting_default_input_definition_overwrites_default_values()
    {
        $application = new CustomApplication;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $inputDefinition = $application->getDefinition();

        // check whether the default arguments and options are not returned any more
        $this->assertFalse($inputDefinition->hasArgument('command'));

        $this->assertFalse($inputDefinition->hasOption('help'));
        $this->assertFalse($inputDefinition->hasOption('quiet'));
        $this->assertFalse($inputDefinition->hasOption('verbose'));
        $this->assertFalse($inputDefinition->hasOption('version'));
        $this->assertFalse($inputDefinition->hasOption('ansi'));
        $this->assertFalse($inputDefinition->hasOption('no-ansi'));
        $this->assertFalse($inputDefinition->hasOption('no-interaction'));

        $this->assertTrue($inputDefinition->hasOption('custom'));
    }

    public function test_setting_custom_input_definition_overwrites_default_values()
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $application->setDefinition(new InputDefinition([new InputOption('--custom', '-c', InputOption::VALUE_NONE, 'Set the custom input definition.')]));

        $inputDefinition = $application->getDefinition();

        // check whether the default arguments and options are not returned any more
        $this->assertFalse($inputDefinition->hasArgument('command'));

        $this->assertFalse($inputDefinition->hasOption('help'));
        $this->assertFalse($inputDefinition->hasOption('quiet'));
        $this->assertFalse($inputDefinition->hasOption('verbose'));
        $this->assertFalse($inputDefinition->hasOption('version'));
        $this->assertFalse($inputDefinition->hasOption('ansi'));
        $this->assertFalse($inputDefinition->hasOption('no-ansi'));
        $this->assertFalse($inputDefinition->hasOption('no-interaction'));

        $this->assertTrue($inputDefinition->hasOption('custom'));
    }

    public function test_run_with_dispatcher()
    {
        $application = new Application;
        $application->setAutoExit(false);
        $application->setDispatcher($this->getDispatcher());

        $application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output) {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo']);
        $this->assertEquals('before.foo.after.'.PHP_EOL, $tester->getDisplay());
    }

    /**
     * @expectedException        \LogicException
     *
     * @expectedExceptionMessage caught
     */
    public function test_run_with_exception_and_dispatcher()
    {
        $application = new Application;
        $application->setDispatcher($this->getDispatcher());
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output) {
            throw new \RuntimeException('foo');
        });

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo']);
    }

    public function test_run_dispatches_all_events_with_exception()
    {
        $application = new Application;
        $application->setDispatcher($this->getDispatcher());
        $application->setAutoExit(false);

        $application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output) {
            $output->write('foo.');

            throw new \RuntimeException('foo');
        });

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo']);
        $this->assertContains('before.foo.caught.after.', $tester->getDisplay());
    }

    public function test_run_with_dispatcher_skipping_command()
    {
        $application = new Application;
        $application->setDispatcher($this->getDispatcher(true));
        $application->setAutoExit(false);

        $application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output) {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($application);
        $exitCode = $tester->run(['command' => 'foo']);
        $this->assertContains('before.after.', $tester->getDisplay());
        $this->assertEquals(ConsoleCommandEvent::RETURN_CODE_DISABLED, $exitCode);
    }

    public function test_run_with_dispatcher_accessing_input_options()
    {
        $noInteractionValue = null;
        $quietValue = null;

        $dispatcher = $this->getDispatcher();
        $dispatcher->addListener('console.command', function (ConsoleCommandEvent $event) use (&$noInteractionValue, &$quietValue) {
            $input = $event->getInput();

            $noInteractionValue = $input->getOption('no-interaction');
            $quietValue = $input->getOption('quiet');
        });

        $application = new Application;
        $application->setDispatcher($dispatcher);
        $application->setAutoExit(false);

        $application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output) {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo', '--no-interaction' => true]);

        $this->assertTrue($noInteractionValue);
        $this->assertFalse($quietValue);
    }

    public function test_run_with_dispatcher_adding_input_options()
    {
        $extraValue = null;

        $dispatcher = $this->getDispatcher();
        $dispatcher->addListener('console.command', function (ConsoleCommandEvent $event) use (&$extraValue) {
            $definition = $event->getCommand()->getDefinition();
            $input = $event->getInput();

            $definition->addOption(new InputOption('extra', null, InputOption::VALUE_REQUIRED));
            $input->bind($definition);

            $extraValue = $input->getOption('extra');
        });

        $application = new Application;
        $application->setDispatcher($dispatcher);
        $application->setAutoExit(false);

        $application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output) {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo', '--extra' => 'some test value']);

        $this->assertEquals('some test value', $extraValue);
    }

    public function test_terminal_dimensions()
    {
        $application = new Application;
        $originalDimensions = $application->getTerminalDimensions();
        $this->assertCount(2, $originalDimensions);

        $width = 80;
        if ($originalDimensions[0] == $width) {
            $width = 100;
        }

        $application->setTerminalDimensions($width, 80);
        $this->assertSame([$width, 80], $application->getTerminalDimensions());
    }

    protected function getDispatcher($skipCommand = false)
    {
        $dispatcher = new EventDispatcher;
        $dispatcher->addListener('console.command', function (ConsoleCommandEvent $event) use ($skipCommand) {
            $event->getOutput()->write('before.');

            if ($skipCommand) {
                $event->disableCommand();
            }
        });
        $dispatcher->addListener('console.terminate', function (ConsoleTerminateEvent $event) use ($skipCommand) {
            $event->getOutput()->writeln('after.');

            if (! $skipCommand) {
                $event->setExitCode(113);
            }
        });
        $dispatcher->addListener('console.exception', function (ConsoleExceptionEvent $event) {
            $event->getOutput()->write('caught.');

            $event->setException(new \LogicException('caught.', $event->getExitCode(), $event->getException()));
        });

        return $dispatcher;
    }

    public function test_set_run_custom_default_command()
    {
        $command = new \FooCommand;

        $application = new Application;
        $application->setAutoExit(false);
        $application->add($command);
        $application->setDefaultCommand($command->getName());

        $tester = new ApplicationTester($application);
        $tester->run([]);
        $this->assertEquals('interact called'.PHP_EOL.'called'.PHP_EOL, $tester->getDisplay(), 'Application runs the default set command if different from \'list\' command');

        $application = new CustomDefaultCommandApplication;
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $tester->run([]);

        $this->assertEquals('interact called'.PHP_EOL.'called'.PHP_EOL, $tester->getDisplay(), 'Application runs the default set command if different from \'list\' command');
    }

    /**
     * @requires function posix_isatty
     */
    public function test_can_check_if_terminal_is_interactive()
    {
        $application = new CustomDefaultCommandApplication;
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'help']);

        $this->assertFalse($tester->getInput()->hasParameterOption(['--no-interaction', '-n']));

        $inputStream = $application->getHelperSet()->get('question')->getInputStream();
        $this->assertEquals($tester->getInput()->isInteractive(), @posix_isatty($inputStream));
    }
}

class CustomApplication extends Application
{
    /**
     * Overwrites the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition([new InputOption('--custom', '-c', InputOption::VALUE_NONE, 'Set the custom input definition.')]);
    }

    /**
     * Gets the default helper set with the helpers that should always be available.
     *
     * @return HelperSet A HelperSet instance
     */
    protected function getDefaultHelperSet()
    {
        return new HelperSet([new FormatterHelper]);
    }
}

class CustomDefaultCommandApplication extends Application
{
    /**
     * Overwrites the constructor in order to set a different default command.
     */
    public function __construct()
    {
        parent::__construct();

        $command = new \FooCommand;
        $this->add($command);
        $this->setDefaultCommand($command->getName());
    }
}
