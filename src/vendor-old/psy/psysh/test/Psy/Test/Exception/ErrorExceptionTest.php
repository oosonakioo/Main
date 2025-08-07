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

use Psy\Exception\ErrorException;
use Psy\Exception\Exception;

class ErrorExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function test_instance()
    {
        $e = new ErrorException;

        $this->assertTrue($e instanceof Exception);
        $this->assertTrue($e instanceof \ErrorException);
        $this->assertTrue($e instanceof ErrorException);
    }

    public function test_message()
    {
        $e = new ErrorException('foo');

        $this->assertContains('foo', $e->getMessage());
        $this->assertEquals('foo', $e->getRawMessage());
    }

    /**
     * @dataProvider getLevels
     */
    public function test_error_levels($level, $type)
    {
        $e = new ErrorException('foo', 0, $level);
        $this->assertContains('PHP '.$type, $e->getMessage());
    }

    /**
     * @dataProvider getLevels
     */
    public function test_throw_exception($level, $type)
    {
        try {
            ErrorException::throwException($level, '{whot}', '{file}', '13');
        } catch (ErrorException $e) {
            $this->assertContains('PHP '.$type, $e->getMessage());
            $this->assertContains('{whot}', $e->getMessage());
            $this->assertContains('in {file}', $e->getMessage());
            $this->assertContains('on line 13', $e->getMessage());
        }
    }

    public function getLevels()
    {
        return [
            [E_WARNING,         'warning'],
            [E_CORE_WARNING,    'warning'],
            [E_COMPILE_WARNING, 'warning'],
            [E_USER_WARNING,    'warning'],
            [E_STRICT,          'Strict error'],
            [0,                 'error'],
        ];
    }

    /**
     * @dataProvider getUserLevels
     */
    public function test_throw_exception_as_error_handler($level, $type)
    {
        set_error_handler(['Psy\Exception\ErrorException', 'throwException']);
        try {
            trigger_error('{whot}', $level);
        } catch (ErrorException $e) {
            $this->assertContains('PHP '.$type, $e->getMessage());
            $this->assertContains('{whot}', $e->getMessage());
        }
        restore_error_handler();
    }

    public function getUserLevels()
    {
        return [
            [E_USER_ERROR,      'error'],
            [E_USER_WARNING,    'warning'],
            [E_USER_NOTICE,     'error'],
            [E_USER_DEPRECATED, 'error'],
        ];
    }

    public function test_ignore_execution_loop_filename()
    {
        $e = new ErrorException('{{message}}', 0, 1, '/fake/path/to/Psy/ExecutionLoop/Loop.php');
        $this->assertEmpty($e->getFile());

        $e = new ErrorException('{{message}}', 0, 1, 'c:\fake\path\to\Psy\ExecutionLoop\Loop.php');
        $this->assertEmpty($e->getFile());

        $e = new ErrorException('{{message}}', 0, 1, '/fake/path/to/Psy/File.php');
        $this->assertNotEmpty($e->getFile());
    }
}
