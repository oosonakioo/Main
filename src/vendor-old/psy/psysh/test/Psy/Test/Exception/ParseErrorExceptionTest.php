<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2015 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Test\Exception;

use Psy\Exception\Exception;
use Psy\Exception\ParseErrorException;

class ParseErrorExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function test_instance()
    {
        $e = new ParseErrorException;

        $this->assertTrue($e instanceof Exception);
        $this->assertTrue($e instanceof \PhpParser\Error);
        $this->assertTrue($e instanceof ParseErrorException);
    }

    public function test_message()
    {
        $e = new ParseErrorException('{msg}', 1);

        $this->assertContains('{msg}', $e->getMessage());
        $this->assertContains('PHP Parse error:', $e->getMessage());
    }

    public function test_construct_from_parse_error()
    {
        $e = ParseErrorException::fromParseError(new \PhpParser\Error('{msg}'));

        $this->assertContains('{msg}', $e->getRawMessage());
        $this->assertContains('PHP Parse error:', $e->getMessage());
    }
}
