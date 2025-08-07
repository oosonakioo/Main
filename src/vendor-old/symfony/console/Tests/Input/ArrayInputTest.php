<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Input;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class ArrayInputTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_first_argument()
    {
        $input = new ArrayInput([]);
        $this->assertNull($input->getFirstArgument(), '->getFirstArgument() returns null if no argument were passed');
        $input = new ArrayInput(['name' => 'Fabien']);
        $this->assertEquals('Fabien', $input->getFirstArgument(), '->getFirstArgument() returns the first passed argument');
        $input = new ArrayInput(['--foo' => 'bar', 'name' => 'Fabien']);
        $this->assertEquals('Fabien', $input->getFirstArgument(), '->getFirstArgument() returns the first passed argument');
    }

    public function test_has_parameter_option()
    {
        $input = new ArrayInput(['name' => 'Fabien', '--foo' => 'bar']);
        $this->assertTrue($input->hasParameterOption('--foo'), '->hasParameterOption() returns true if an option is present in the passed parameters');
        $this->assertFalse($input->hasParameterOption('--bar'), '->hasParameterOption() returns false if an option is not present in the passed parameters');

        $input = new ArrayInput(['--foo']);
        $this->assertTrue($input->hasParameterOption('--foo'), '->hasParameterOption() returns true if an option is present in the passed parameters');

        $input = new ArrayInput(['--foo', '--', '--bar']);
        $this->assertTrue($input->hasParameterOption('--bar'), '->hasParameterOption() returns true if an option is present in the passed parameters');
        $this->assertFalse($input->hasParameterOption('--bar', true), '->hasParameterOption() returns false if an option is present in the passed parameters after an end of options signal');
    }

    public function test_get_parameter_option()
    {
        $input = new ArrayInput(['name' => 'Fabien', '--foo' => 'bar']);
        $this->assertEquals('bar', $input->getParameterOption('--foo'), '->getParameterOption() returns the option of specified name');
        $this->assertFalse($input->getParameterOption('--bar'), '->getParameterOption() returns the default if an option is not present in the passed parameters');

        $input = new ArrayInput(['Fabien', '--foo' => 'bar']);
        $this->assertEquals('bar', $input->getParameterOption('--foo'), '->getParameterOption() returns the option of specified name');

        $input = new ArrayInput(['--foo', '--', '--bar' => 'woop']);
        $this->assertEquals('woop', $input->getParameterOption('--bar'), '->getParameterOption() returns the correct value if an option is present in the passed parameters');
        $this->assertFalse($input->getParameterOption('--bar', false, true), '->getParameterOption() returns false if an option is present in the passed parameters after an end of options signal');
    }

    public function test_parse_arguments()
    {
        $input = new ArrayInput(['name' => 'foo'], new InputDefinition([new InputArgument('name')]));

        $this->assertEquals(['name' => 'foo'], $input->getArguments(), '->parse() parses required arguments');
    }

    /**
     * @dataProvider provideOptions
     */
    public function test_parse_options($input, $options, $expectedOptions, $message)
    {
        $input = new ArrayInput($input, new InputDefinition($options));

        $this->assertEquals($expectedOptions, $input->getOptions(), $message);
    }

    public function provideOptions()
    {
        return [
            [
                ['--foo' => 'bar'],
                [new InputOption('foo')],
                ['foo' => 'bar'],
                '->parse() parses long options',
            ],
            [
                ['--foo' => 'bar'],
                [new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL, '', 'default')],
                ['foo' => 'bar'],
                '->parse() parses long options with a default value',
            ],
            [
                ['--foo' => null],
                [new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL, '', 'default')],
                ['foo' => 'default'],
                '->parse() parses long options with a default value',
            ],
            [
                ['-f' => 'bar'],
                [new InputOption('foo', 'f')],
                ['foo' => 'bar'],
                '->parse() parses short options',
            ],
            [
                ['--' => null, '-f' => 'bar'],
                [new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL, '', 'default')],
                ['foo' => 'default'],
                '->parse() does not parse opts after an end of options signal',
            ],
            [
                ['--' => null],
                [],
                [],
                '->parse() does not choke on end of options signal',
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidInput
     */
    public function test_parse_invalid_input($parameters, $definition, $expectedExceptionMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedExceptionMessage);

        new ArrayInput($parameters, $definition);
    }

    public function provideInvalidInput()
    {
        return [
            [
                ['foo' => 'foo'],
                new InputDefinition([new InputArgument('name')]),
                'The "foo" argument does not exist.',
            ],
            [
                ['--foo' => null],
                new InputDefinition([new InputOption('foo', 'f', InputOption::VALUE_REQUIRED)]),
                'The "--foo" option requires a value.',
            ],
            [
                ['--foo' => 'foo'],
                new InputDefinition,
                'The "--foo" option does not exist.',
            ],
            [
                ['-o' => 'foo'],
                new InputDefinition,
                'The "-o" option does not exist.',
            ],
        ];
    }

    public function test_to_string()
    {
        $input = new ArrayInput(['-f' => null, '-b' => 'bar', '--foo' => 'b a z', '--lala' => null, 'test' => 'Foo', 'test2' => "A\nB'C"]);
        $this->assertEquals('-f -b=bar --foo='.escapeshellarg('b a z').' --lala Foo '.escapeshellarg("A\nB'C"), (string) $input);
    }
}
