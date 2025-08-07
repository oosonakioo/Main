<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Util_GetoptTest extends PHPUnit_Framework_TestCase
{
    public function test_it_include_the_long_options_after_the_argument()
    {
        $args = [
            'command',
            'myArgument',
            '--colors',
        ];
        $actual = PHPUnit_Util_Getopt::getopt($args, '', ['colors==']);

        $expected = [
            [
                [
                    '--colors',
                    null,
                ],
            ],
            [
                'myArgument',
            ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function test_it_include_the_short_options_after_the_argument()
    {
        $args = [
            'command',
            'myArgument',
            '-v',
        ];
        $actual = PHPUnit_Util_Getopt::getopt($args, 'v');

        $expected = [
            [
                [
                    'v',
                    null,
                ],
            ],
            [
                'myArgument',
            ],
        ];

        $this->assertEquals($expected, $actual);
    }
}
