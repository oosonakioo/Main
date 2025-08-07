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

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\TestCase;

class NewRelicHandlerTest extends TestCase
{
    public static $appname;

    public static $customParameters;

    public static $transactionName;

    protected function setUp()
    {
        self::$appname = null;
        self::$customParameters = [];
        self::$transactionName = null;
    }

    /**
     * @expectedException Monolog\Handler\MissingExtensionException
     */
    public function test_thehandler_throws_an_exception_if_the_nr_extension_is_not_loaded()
    {
        $handler = new StubNewRelicHandlerWithoutExtension;
        $handler->handle($this->getRecord(Logger::ERROR));
    }

    public function test_thehandler_can_handle_the_record()
    {
        $handler = new StubNewRelicHandler;
        $handler->handle($this->getRecord(Logger::ERROR));
    }

    public function test_thehandler_can_add_context_params_to_the_new_relic_trace()
    {
        $handler = new StubNewRelicHandler;
        $handler->handle($this->getRecord(Logger::ERROR, 'log message', ['a' => 'b']));
        $this->assertEquals(['context_a' => 'b'], self::$customParameters);
    }

    public function test_thehandler_can_add_exploded_context_params_to_the_new_relic_trace()
    {
        $handler = new StubNewRelicHandler(Logger::ERROR, true, self::$appname, true);
        $handler->handle($this->getRecord(
            Logger::ERROR,
            'log message',
            ['a' => ['key1' => 'value1', 'key2' => 'value2']]
        ));
        $this->assertEquals(
            ['context_a_key1' => 'value1', 'context_a_key2' => 'value2'],
            self::$customParameters
        );
    }

    public function test_thehandler_can_add_extra_params_to_the_new_relic_trace()
    {
        $record = $this->getRecord(Logger::ERROR, 'log message');
        $record['extra'] = ['c' => 'd'];

        $handler = new StubNewRelicHandler;
        $handler->handle($record);

        $this->assertEquals(['extra_c' => 'd'], self::$customParameters);
    }

    public function test_thehandler_can_add_exploded_extra_params_to_the_new_relic_trace()
    {
        $record = $this->getRecord(Logger::ERROR, 'log message');
        $record['extra'] = ['c' => ['key1' => 'value1', 'key2' => 'value2']];

        $handler = new StubNewRelicHandler(Logger::ERROR, true, self::$appname, true);
        $handler->handle($record);

        $this->assertEquals(
            ['extra_c_key1' => 'value1', 'extra_c_key2' => 'value2'],
            self::$customParameters
        );
    }

    public function test_thehandler_can_add_extra_context_and_params_to_the_new_relic_trace()
    {
        $record = $this->getRecord(Logger::ERROR, 'log message', ['a' => 'b']);
        $record['extra'] = ['c' => 'd'];

        $handler = new StubNewRelicHandler;
        $handler->handle($record);

        $expected = [
            'context_a' => 'b',
            'extra_c' => 'd',
        ];

        $this->assertEquals($expected, self::$customParameters);
    }

    public function test_thehandler_can_handle_the_records_formatted_using_the_line_formatter()
    {
        $handler = new StubNewRelicHandler;
        $handler->setFormatter(new LineFormatter);
        $handler->handle($this->getRecord(Logger::ERROR));
    }

    public function test_the_app_name_is_null_by_default()
    {
        $handler = new StubNewRelicHandler;
        $handler->handle($this->getRecord(Logger::ERROR, 'log message'));

        $this->assertEquals(null, self::$appname);
    }

    public function test_the_app_name_can_be_injected_fromthe_constructor()
    {
        $handler = new StubNewRelicHandler(Logger::DEBUG, false, 'myAppName');
        $handler->handle($this->getRecord(Logger::ERROR, 'log message'));

        $this->assertEquals('myAppName', self::$appname);
    }

    public function test_the_app_name_can_be_overridden_from_each_log()
    {
        $handler = new StubNewRelicHandler(Logger::DEBUG, false, 'myAppName');
        $handler->handle($this->getRecord(Logger::ERROR, 'log message', ['appname' => 'logAppName']));

        $this->assertEquals('logAppName', self::$appname);
    }

    public function test_the_transaction_name_is_null_by_default()
    {
        $handler = new StubNewRelicHandler;
        $handler->handle($this->getRecord(Logger::ERROR, 'log message'));

        $this->assertEquals(null, self::$transactionName);
    }

    public function test_the_transaction_name_can_be_injected_from_the_constructor()
    {
        $handler = new StubNewRelicHandler(Logger::DEBUG, false, null, false, 'myTransaction');
        $handler->handle($this->getRecord(Logger::ERROR, 'log message'));

        $this->assertEquals('myTransaction', self::$transactionName);
    }

    public function test_the_transaction_name_can_be_overridden_from_each_log()
    {
        $handler = new StubNewRelicHandler(Logger::DEBUG, false, null, false, 'myTransaction');
        $handler->handle($this->getRecord(Logger::ERROR, 'log message', ['transaction_name' => 'logTransactName']));

        $this->assertEquals('logTransactName', self::$transactionName);
    }
}

class StubNewRelicHandlerWithoutExtension extends NewRelicHandler
{
    protected function isNewRelicEnabled()
    {
        return false;
    }
}

class StubNewRelicHandler extends NewRelicHandler
{
    protected function isNewRelicEnabled()
    {
        return true;
    }
}

function newrelic_notice_error()
{
    return true;
}

function newrelic_set_appname($appname)
{
    return NewRelicHandlerTest::$appname = $appname;
}

function newrelic_name_transaction($transactionName)
{
    return NewRelicHandlerTest::$transactionName = $transactionName;
}

function newrelic_add_custom_parameter($key, $value)
{
    NewRelicHandlerTest::$customParameters[$key] = $value;

    return true;
}
