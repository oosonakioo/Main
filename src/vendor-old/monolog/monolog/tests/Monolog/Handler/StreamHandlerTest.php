<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\TestCase;

class StreamHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write()
    {
        $handle = fopen('php://memory', 'a+');
        $handler = new StreamHandler($handle);
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord(Logger::WARNING, 'test'));
        $handler->handle($this->getRecord(Logger::WARNING, 'test2'));
        $handler->handle($this->getRecord(Logger::WARNING, 'test3'));
        fseek($handle, 0);
        $this->assertEquals('testtest2test3', fread($handle, 100));
    }

    /**
     * @covers Monolog\Handler\StreamHandler::close
     */
    public function test_close_keeps_external_handlers_open()
    {
        $handle = fopen('php://memory', 'a+');
        $handler = new StreamHandler($handle);
        $this->assertTrue(is_resource($handle));
        $handler->close();
        $this->assertTrue(is_resource($handle));
    }

    /**
     * @covers Monolog\Handler\StreamHandler::close
     */
    public function test_close()
    {
        $handler = new StreamHandler('php://memory');
        $handler->handle($this->getRecord(Logger::WARNING, 'test'));
        $streamProp = new \ReflectionProperty('Monolog\Handler\StreamHandler', 'stream');
        $streamProp->setAccessible(true);
        $handle = $streamProp->getValue($handler);

        $this->assertTrue(is_resource($handle));
        $handler->close();
        $this->assertFalse(is_resource($handle));
    }

    /**
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_creates_the_stream_resource()
    {
        $handler = new StreamHandler('php://memory');
        $handler->handle($this->getRecord());
    }

    /**
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_locking()
    {
        $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'monolog_locked_log';
        $handler = new StreamHandler($temp, Logger::DEBUG, true, null, true);
        $handler->handle($this->getRecord());
    }

    /**
     * @expectedException LogicException
     *
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_missing_resource()
    {
        $handler = new StreamHandler(null);
        $handler->handle($this->getRecord());
    }

    public function invalidArgumentProvider()
    {
        return [
            [1],
            [[]],
            [['bogus://url']],
        ];
    }

    /**
     * @dataProvider invalidArgumentProvider
     *
     * @expectedException InvalidArgumentException
     *
     * @covers Monolog\Handler\StreamHandler::__construct
     */
    public function test_write_invalid_argument($invalidArgument)
    {
        $handler = new StreamHandler($invalidArgument);
    }

    /**
     * @expectedException UnexpectedValueException
     *
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_invalid_resource()
    {
        $handler = new StreamHandler('bogus://url');
        $handler->handle($this->getRecord());
    }

    /**
     * @expectedException UnexpectedValueException
     *
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_non_existing_resource()
    {
        $handler = new StreamHandler('ftp://foo/bar/baz/'.rand(0, 10000));
        $handler->handle($this->getRecord());
    }

    /**
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_non_existing_path()
    {
        $handler = new StreamHandler(sys_get_temp_dir().'/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->handle($this->getRecord());
    }

    /**
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_non_existing_file_resource()
    {
        $handler = new StreamHandler('file://'.sys_get_temp_dir().'/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->handle($this->getRecord());
    }

    /**
     * @expectedException Exception
     *
     * @expectedExceptionMessageRegExp /There is no existing directory at/
     *
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_non_existing_and_not_creatable_path()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Permissions checks can not run on windows');
        }
        $handler = new StreamHandler('/foo/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->handle($this->getRecord());
    }

    /**
     * @expectedException Exception
     *
     * @expectedExceptionMessageRegExp /There is no existing directory at/
     *
     * @covers Monolog\Handler\StreamHandler::__construct
     * @covers Monolog\Handler\StreamHandler::write
     */
    public function test_write_non_existing_and_not_creatable_file_resource()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Permissions checks can not run on windows');
        }
        $handler = new StreamHandler('file:///foo/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->handle($this->getRecord());
    }
}
