<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyleStack;

class OutputFormatterStyleStackTest extends \PHPUnit_Framework_TestCase
{
    public function test_push()
    {
        $stack = new OutputFormatterStyleStack;
        $stack->push($s1 = new OutputFormatterStyle('white', 'black'));
        $stack->push($s2 = new OutputFormatterStyle('yellow', 'blue'));

        $this->assertEquals($s2, $stack->getCurrent());

        $stack->push($s3 = new OutputFormatterStyle('green', 'red'));

        $this->assertEquals($s3, $stack->getCurrent());
    }

    public function test_pop()
    {
        $stack = new OutputFormatterStyleStack;
        $stack->push($s1 = new OutputFormatterStyle('white', 'black'));
        $stack->push($s2 = new OutputFormatterStyle('yellow', 'blue'));

        $this->assertEquals($s2, $stack->pop());
        $this->assertEquals($s1, $stack->pop());
    }

    public function test_pop_empty()
    {
        $stack = new OutputFormatterStyleStack;
        $style = new OutputFormatterStyle;

        $this->assertEquals($style, $stack->pop());
    }

    public function test_pop_not_last()
    {
        $stack = new OutputFormatterStyleStack;
        $stack->push($s1 = new OutputFormatterStyle('white', 'black'));
        $stack->push($s2 = new OutputFormatterStyle('yellow', 'blue'));
        $stack->push($s3 = new OutputFormatterStyle('green', 'red'));

        $this->assertEquals($s2, $stack->pop($s2));
        $this->assertEquals($s1, $stack->pop());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_invalid_pop()
    {
        $stack = new OutputFormatterStyleStack;
        $stack->push(new OutputFormatterStyle('white', 'black'));
        $stack->pop(new OutputFormatterStyle('yellow', 'blue'));
    }
}
