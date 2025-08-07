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

use Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Logger;
use Monolog\TestCase;
use Psr\Log\LogLevel;

class FingersCrossedHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\FingersCrossedHandler::__construct
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     */
    public function test_handle_buffers()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
        $handler->handle($this->getRecord(Logger::WARNING));
        $handler->close();
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue(count($test->getRecords()) === 3);
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     */
    public function test_handle_stops_buffering_after_trigger()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test);
        $handler->handle($this->getRecord(Logger::WARNING));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->close();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     * @covers Monolog\Handler\FingersCrossedHandler::reset
     */
    public function test_handle_restart_buffering_after_reset()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test);
        $handler->handle($this->getRecord(Logger::WARNING));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->reset();
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->close();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     */
    public function test_handle_restart_buffering_after_being_triggered_when_stop_buffering_is_disabled()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, Logger::WARNING, 0, false, false);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::WARNING));
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->close();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     */
    public function test_handle_buffer_limit()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, Logger::WARNING, 2);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasInfoRecords());
        $this->assertFalse($test->hasDebugRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     */
    public function test_handle_with_callback()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler(function ($record, $handler) use ($test) {
            return $test;
        });
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue(count($test->getRecords()) === 3);
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     *
     * @expectedException RuntimeException
     */
    public function test_handle_with_bad_callback_throws_exception()
    {
        $handler = new FingersCrossedHandler(function ($record, $handler) {
            return 'foo';
        });
        $handler->handle($this->getRecord(Logger::WARNING));
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::isHandling
     */
    public function test_is_handling_always()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, Logger::ERROR);
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::__construct
     * @covers Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy::__construct
     * @covers Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy::isHandlerActivated
     */
    public function test_error_level_activation_strategy()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy(Logger::WARNING));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::__construct
     * @covers Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy::__construct
     * @covers Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy::isHandlerActivated
     */
    public function test_error_level_activation_strategy_with_psr_level()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy('warning'));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::__construct
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     */
    public function test_override_activation_strategy()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy('warning'));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $handler->activate();
        $this->assertTrue($test->hasDebugRecords());
        $handler->handle($this->getRecord(Logger::INFO));
        $this->assertTrue($test->hasInfoRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy::__construct
     * @covers Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy::isHandlerActivated
     */
    public function test_channel_level_activation_strategy()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, new ChannelLevelActivationStrategy(Logger::ERROR, ['othertest' => Logger::DEBUG]));
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertFalse($test->hasWarningRecords());
        $record = $this->getRecord(Logger::DEBUG);
        $record['channel'] = 'othertest';
        $handler->handle($record);
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy::__construct
     * @covers Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy::isHandlerActivated
     */
    public function test_channel_level_activation_strategy_with_psr_levels()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, new ChannelLevelActivationStrategy('error', ['othertest' => 'debug']));
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertFalse($test->hasWarningRecords());
        $record = $this->getRecord(Logger::DEBUG);
        $record['channel'] = 'othertest';
        $handler->handle($record);
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::activate
     */
    public function test_handle_uses_processors()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, Logger::INFO);
        $handler->pushProcessor(function ($record) {
            $record['extra']['foo'] = true;

            return $record;
        });
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::close
     */
    public function test_passthru_on_close()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy(Logger::WARNING), 0, true, true, Logger::INFO);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->close();
        $this->assertFalse($test->hasDebugRecords());
        $this->assertTrue($test->hasInfoRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::close
     */
    public function test_psr_level_passthru_on_close()
    {
        $test = new TestHandler;
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy(Logger::WARNING), 0, true, true, LogLevel::INFO);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->close();
        $this->assertFalse($test->hasDebugRecords());
        $this->assertTrue($test->hasInfoRecords());
    }
}
