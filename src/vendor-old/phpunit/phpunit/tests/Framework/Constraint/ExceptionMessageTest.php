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
 * @since      Class available since Release 4.0.20
 *
 * @covers     PHPUnit_Framework_Constraint_ExceptionMessage
 */
class ExceptionMessageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     *
     * @expectedExceptionMessage A literal exception message
     */
    public function test_literal_message()
    {
        throw new Exception('A literal exception message');
    }

    /**
     * @expectedException \Exception
     *
     * @expectedExceptionMessage A partial
     */
    public function test_patial_message_begin()
    {
        throw new Exception('A partial exception message');
    }

    /**
     * @expectedException \Exception
     *
     * @expectedExceptionMessage partial exception
     */
    public function test_patial_message_middle()
    {
        throw new Exception('A partial exception message');
    }

    /**
     * @expectedException \Exception
     *
     * @expectedExceptionMessage exception message
     */
    public function test_patial_message_end()
    {
        throw new Exception('A partial exception message');
    }
}
