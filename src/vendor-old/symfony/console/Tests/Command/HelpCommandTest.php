<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Tester\CommandTester;

class HelpCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test_execute_for_command_alias()
    {
        $command = new HelpCommand;
        $command->setApplication(new Application);
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command_name' => 'li'], ['decorated' => false]);
        $this->assertContains('list [options] [--] [<namespace>]', $commandTester->getDisplay(), '->execute() returns a text help for the given command alias');
        $this->assertContains('format=FORMAT', $commandTester->getDisplay(), '->execute() returns a text help for the given command alias');
        $this->assertContains('raw', $commandTester->getDisplay(), '->execute() returns a text help for the given command alias');
    }

    public function test_execute_for_command()
    {
        $command = new HelpCommand;
        $commandTester = new CommandTester($command);
        $command->setCommand(new ListCommand);
        $commandTester->execute([], ['decorated' => false]);
        $this->assertContains('list [options] [--] [<namespace>]', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
        $this->assertContains('format=FORMAT', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
        $this->assertContains('raw', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
    }

    public function test_execute_for_command_with_xml_option()
    {
        $command = new HelpCommand;
        $commandTester = new CommandTester($command);
        $command->setCommand(new ListCommand);
        $commandTester->execute(['--format' => 'xml']);
        $this->assertContains('<command', $commandTester->getDisplay(), '->execute() returns an XML help text if --xml is passed');
    }

    public function test_execute_for_application_command()
    {
        $application = new Application;
        $commandTester = new CommandTester($application->get('help'));
        $commandTester->execute(['command_name' => 'list']);
        $this->assertContains('list [options] [--] [<namespace>]', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
        $this->assertContains('format=FORMAT', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
        $this->assertContains('raw', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
    }

    public function test_execute_for_application_command_with_xml_option()
    {
        $application = new Application;
        $commandTester = new CommandTester($application->get('help'));
        $commandTester->execute(['command_name' => 'list', '--format' => 'xml']);
        $this->assertContains('list [--raw] [--format FORMAT] [--] [&lt;namespace&gt;]', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
        $this->assertContains('<command', $commandTester->getDisplay(), '->execute() returns an XML help text if --format=xml is passed');
    }
}
