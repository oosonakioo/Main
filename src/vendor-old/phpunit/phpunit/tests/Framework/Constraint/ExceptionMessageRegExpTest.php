<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @since      Class available since Release 4.3.0
 *
 * @covers     PHPUnit_Framework_Constraint_ExceptionMessageRegExp
 */
class ExceptionMessageRegExpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     *
     * @expectedExceptionMessageRegExp /^A polymorphic \w+ message/
     */
    public function test_regex_message()
    {
        throw new Exception('A polymorphic exception message');
    }

    /**
     * @expectedException \Exception
     *
     * @expectedExceptionMessageRegExp /^a poly[a-z]+ [a-zA-Z0-9_]+ me(s){2}age$/i
     */
    public function test_regex_message_extreme()
    {
        throw new Exception('A polymorphic exception message');
    }

    /**
     * @runInSeparateProcess
     *
     * @requires extension xdebug
     *
     * @expectedException \Exception
     *
     * @expectedExceptionMessageRegExp #Screaming preg_match#
     */
    public function test_message_xdebug_scream_compatibility()
    {
        ini_set('xdebug.scream', '1');
        throw new Exception('Screaming preg_match');
    }

    /**
     * @coversNothing
     *
     * @expectedException \Exception variadic
     *
     * @expectedExceptionMessageRegExp /^A variadic \w+ message/
     */
    public function test_simultaneous_literal_and_reg_exp_exception_message()
    {
        throw new Exception('A variadic exception message');
    }
}
