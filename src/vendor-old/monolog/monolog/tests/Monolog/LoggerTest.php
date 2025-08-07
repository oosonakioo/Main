<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog;

use Monolog\Handler\TestHandler;
use Monolog\Processor\WebProcessor;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Monolog\Logger::getName
     */
    public function test_get_name()
    {
        $logger = new Logger('foo');
        $this->assertEquals('foo', $logger->getName());
    }

    /**
     * @covers Monolog\Logger::getLevelName
     */
    public function test_get_level_name()
    {
        $this->assertEquals('ERROR', Logger::getLevelName(Logger::ERROR));
    }

    /**
     * @covers Monolog\Logger::withName
     */
    public function test_with_name()
    {
        $first = new Logger('first', [$handler = new TestHandler]);
        $second = $first->withName('second');

        $this->assertSame('first', $first->getName());
        $this->assertSame('second', $second->getName());
        $this->assertSame($handler, $second->popHandler());
    }

    /**
     * @covers Monolog\Logger::toMonologLevel
     */
    public function test_convert_ps_r3_to_monolog_level()
    {
        $this->assertEquals(Logger::toMonologLevel('debug'), 100);
        $this->assertEquals(Logger::toMonologLevel('info'), 200);
        $this->assertEquals(Logger::toMonologLevel('notice'), 250);
        $this->assertEquals(Logger::toMonologLevel('warning'), 300);
        $this->assertEquals(Logger::toMonologLevel('error'), 400);
        $this->assertEquals(Logger::toMonologLevel('critical'), 500);
        $this->assertEquals(Logger::toMonologLevel('alert'), 550);
        $this->assertEquals(Logger::toMonologLevel('emergency'), 600);
    }

    /**
     * @covers Monolog\Logger::getLevelName
     *
     * @expectedException InvalidArgumentException
     */
    public function test_get_level_name_throws()
    {
        Logger::getLevelName(5);
    }

    /**
     * @covers Monolog\Logger::__construct
     */
    public function test_channel()
    {
        $logger = new Logger('foo');
        $handler = new TestHandler;
        $logger->pushHandler($handler);
        $logger->addWarning('test');
        [$record] = $handler->getRecords();
        $this->assertEquals('foo', $record['channel']);
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_log()
    {
        $logger = new Logger(__METHOD__);

        $handler = $this->getMock('Monolog\Handler\NullHandler', ['handle']);
        $handler->expects($this->once())
            ->method('handle');
        $logger->pushHandler($handler);

        $this->assertTrue($logger->addWarning('test'));
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_log_not_handled()
    {
        $logger = new Logger(__METHOD__);

        $handler = $this->getMock('Monolog\Handler\NullHandler', ['handle'], [Logger::ERROR]);
        $handler->expects($this->never())
            ->method('handle');
        $logger->pushHandler($handler);

        $this->assertFalse($logger->addWarning('test'));
    }

    public function test_handlers_in_ctor()
    {
        $handler1 = new TestHandler;
        $handler2 = new TestHandler;
        $logger = new Logger(__METHOD__, [$handler1, $handler2]);

        $this->assertEquals($handler1, $logger->popHandler());
        $this->assertEquals($handler2, $logger->popHandler());
    }

    public function test_processors_in_ctor()
    {
        $processor1 = new WebProcessor;
        $processor2 = new WebProcessor;
        $logger = new Logger(__METHOD__, [], [$processor1, $processor2]);

        $this->assertEquals($processor1, $logger->popProcessor());
        $this->assertEquals($processor2, $logger->popProcessor());
    }

    /**
     * @covers Monolog\Logger::pushHandler
     * @covers Monolog\Logger::popHandler
     *
     * @expectedException LogicException
     */
    public function test_push_pop_handler()
    {
        $logger = new Logger(__METHOD__);
        $handler1 = new TestHandler;
        $handler2 = new TestHandler;

        $logger->pushHandler($handler1);
        $logger->pushHandler($handler2);

        $this->assertEquals($handler2, $logger->popHandler());
        $this->assertEquals($handler1, $logger->popHandler());
        $logger->popHandler();
    }

    /**
     * @covers Monolog\Logger::setHandlers
     */
    public function test_set_handlers()
    {
        $logger = new Logger(__METHOD__);
        $handler1 = new TestHandler;
        $handler2 = new TestHandler;

        $logger->pushHandler($handler1);
        $logger->setHandlers([$handler2]);

        // handler1 has been removed
        $this->assertEquals([$handler2], $logger->getHandlers());

        $logger->setHandlers([
            'AMapKey' => $handler1,
            'Woop' => $handler2,
        ]);

        // Keys have been scrubbed
        $this->assertEquals([$handler1, $handler2], $logger->getHandlers());
    }

    /**
     * @covers Monolog\Logger::pushProcessor
     * @covers Monolog\Logger::popProcessor
     *
     * @expectedException LogicException
     */
    public function test_push_pop_processor()
    {
        $logger = new Logger(__METHOD__);
        $processor1 = new WebProcessor;
        $processor2 = new WebProcessor;

        $logger->pushProcessor($processor1);
        $logger->pushProcessor($processor2);

        $this->assertEquals($processor2, $logger->popProcessor());
        $this->assertEquals($processor1, $logger->popProcessor());
        $logger->popProcessor();
    }

    /**
     * @covers Monolog\Logger::pushProcessor
     *
     * @expectedException InvalidArgumentException
     */
    public function test_push_processor_with_non_callable()
    {
        $logger = new Logger(__METHOD__);

        $logger->pushProcessor(new \stdClass);
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_processors_are_executed()
    {
        $logger = new Logger(__METHOD__);
        $handler = new TestHandler;
        $logger->pushHandler($handler);
        $logger->pushProcessor(function ($record) {
            $record['extra']['win'] = true;

            return $record;
        });
        $logger->addError('test');
        [$record] = $handler->getRecords();
        $this->assertTrue($record['extra']['win']);
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_processors_are_called_only_once()
    {
        $logger = new Logger(__METHOD__);
        $handler = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $handler->expects($this->any())
            ->method('handle')
            ->will($this->returnValue(true));
        $logger->pushHandler($handler);

        $processor = $this->getMockBuilder('Monolog\Processor\WebProcessor')
            ->disableOriginalConstructor()
            ->setMethods(['__invoke'])
            ->getMock();
        $processor->expects($this->once())
            ->method('__invoke')
            ->will($this->returnArgument(0));
        $logger->pushProcessor($processor);

        $logger->addError('test');
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_processors_not_called_when_not_handled()
    {
        $logger = new Logger(__METHOD__);
        $handler = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(false));
        $logger->pushHandler($handler);
        $that = $this;
        $logger->pushProcessor(function ($record) use ($that) {
            $that->fail('The processor should not be called');
        });
        $logger->addAlert('test');
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_handlers_not_called_before_first_handling()
    {
        $logger = new Logger(__METHOD__);

        $handler1 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler1->expects($this->never())
            ->method('isHandling')
            ->will($this->returnValue(false));
        $handler1->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false));
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler2->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $handler2->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false));
        $logger->pushHandler($handler2);

        $handler3 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler3->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(false));
        $handler3->expects($this->never())
            ->method('handle');
        $logger->pushHandler($handler3);

        $logger->debug('test');
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_handlers_not_called_before_first_handling_with_assoc_array()
    {
        $handler1 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler1->expects($this->never())
            ->method('isHandling')
            ->will($this->returnValue(false));
        $handler1->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false));

        $handler2 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler2->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $handler2->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false));

        $handler3 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler3->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(false));
        $handler3->expects($this->never())
            ->method('handle');

        $logger = new Logger(__METHOD__, ['last' => $handler3, 'second' => $handler2, 'first' => $handler1]);

        $logger->debug('test');
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_bubbling_when_the_handler_returns_false()
    {
        $logger = new Logger(__METHOD__);

        $handler1 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $handler1->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false));
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler2->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $handler2->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false));
        $logger->pushHandler($handler2);

        $logger->debug('test');
    }

    /**
     * @covers Monolog\Logger::addRecord
     */
    public function test_not_bubbling_when_the_handler_returns_true()
    {
        $logger = new Logger(__METHOD__);

        $handler1 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $handler1->expects($this->never())
            ->method('handle');
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler2->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $handler2->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(true));
        $logger->pushHandler($handler2);

        $logger->debug('test');
    }

    /**
     * @covers Monolog\Logger::isHandling
     */
    public function test_is_handling()
    {
        $logger = new Logger(__METHOD__);

        $handler1 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(false));

        $logger->pushHandler($handler1);
        $this->assertFalse($logger->isHandling(Logger::DEBUG));

        $handler2 = $this->getMock('Monolog\Handler\HandlerInterface');
        $handler2->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true));

        $logger->pushHandler($handler2);
        $this->assertTrue($logger->isHandling(Logger::DEBUG));
    }

    /**
     * @dataProvider logMethodProvider
     *
     * @covers Monolog\Logger::addDebug
     * @covers Monolog\Logger::addInfo
     * @covers Monolog\Logger::addNotice
     * @covers Monolog\Logger::addWarning
     * @covers Monolog\Logger::addError
     * @covers Monolog\Logger::addCritical
     * @covers Monolog\Logger::addAlert
     * @covers Monolog\Logger::addEmergency
     * @covers Monolog\Logger::debug
     * @covers Monolog\Logger::info
     * @covers Monolog\Logger::notice
     * @covers Monolog\Logger::warn
     * @covers Monolog\Logger::err
     * @covers Monolog\Logger::crit
     * @covers Monolog\Logger::alert
     * @covers Monolog\Logger::emerg
     */
    public function test_log_methods($method, $expectedLevel)
    {
        $logger = new Logger('foo');
        $handler = new TestHandler;
        $logger->pushHandler($handler);
        $logger->{$method}('test');
        [$record] = $handler->getRecords();
        $this->assertEquals($expectedLevel, $record['level']);
    }

    public function logMethodProvider()
    {
        return [
            // monolog methods
            ['addDebug',     Logger::DEBUG],
            ['addInfo',      Logger::INFO],
            ['addNotice',    Logger::NOTICE],
            ['addWarning',   Logger::WARNING],
            ['addError',     Logger::ERROR],
            ['addCritical',  Logger::CRITICAL],
            ['addAlert',     Logger::ALERT],
            ['addEmergency', Logger::EMERGENCY],

            // ZF/Sf2 compat methods
            ['debug',  Logger::DEBUG],
            ['info',   Logger::INFO],
            ['notice', Logger::NOTICE],
            ['warn',   Logger::WARNING],
            ['err',    Logger::ERROR],
            ['crit',   Logger::CRITICAL],
            ['alert',  Logger::ALERT],
            ['emerg',  Logger::EMERGENCY],
        ];
    }

    /**
     * @dataProvider setTimezoneProvider
     *
     * @covers Monolog\Logger::setTimezone
     */
    public function test_set_timezone($tz)
    {
        Logger::setTimezone($tz);
        $logger = new Logger('foo');
        $handler = new TestHandler;
        $logger->pushHandler($handler);
        $logger->info('test');
        [$record] = $handler->getRecords();
        $this->assertEquals($tz, $record['datetime']->getTimezone());
    }

    public function setTimezoneProvider()
    {
        return array_map(
            function ($tz) {
                return [new \DateTimeZone($tz)];
            },
            \DateTimeZone::listIdentifiers()
        );
    }

    /**
     * @dataProvider useMicrosecondTimestampsProvider
     *
     * @covers Monolog\Logger::useMicrosecondTimestamps
     * @covers Monolog\Logger::addRecord
     */
    public function test_use_microsecond_timestamps($micro, $assert)
    {
        $logger = new Logger('foo');
        $logger->useMicrosecondTimestamps($micro);
        $handler = new TestHandler;
        $logger->pushHandler($handler);
        $logger->info('test');
        [$record] = $handler->getRecords();
        $this->{$assert}('000000', $record['datetime']->format('u'));
    }

    public function useMicrosecondTimestampsProvider()
    {
        return [
            // this has a very small chance of a false negative (1/10^6)
            'with microseconds' => [true, 'assertNotSame'],
            'without microseconds' => [false, PHP_VERSION_ID >= 70100 ? 'assertNotSame' : 'assertSame'],
        ];
    }
}
