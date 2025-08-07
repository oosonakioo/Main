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

use Exception;
use Monolog\ErrorHandler;
use Monolog\Logger;
use Monolog\TestCase;
use PhpConsole\Connector;
use PhpConsole\Dispatcher\Debug as DebugDispatcher;
use PhpConsole\Dispatcher\Errors as ErrorDispatcher;
use PhpConsole\Handler;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers Monolog\Handler\PHPConsoleHandler
 *
 * @author Sergey Barbushin https://www.linkedin.com/in/barbushin
 */
class PHPConsoleHandlerTest extends TestCase
{
    /** @var Connector|PHPUnit_Framework_MockObject_MockObject */
    protected $connector;

    /** @var DebugDispatcher|PHPUnit_Framework_MockObject_MockObject */
    protected $debugDispatcher;

    /** @var ErrorDispatcher|PHPUnit_Framework_MockObject_MockObject */
    protected $errorDispatcher;

    protected function setUp()
    {
        if (! class_exists('PhpConsole\Connector')) {
            $this->markTestSkipped('PHP Console library not found. See https://github.com/barbushin/php-console#installation');
        }
        $this->connector = $this->initConnectorMock();

        $this->debugDispatcher = $this->initDebugDispatcherMock($this->connector);
        $this->connector->setDebugDispatcher($this->debugDispatcher);

        $this->errorDispatcher = $this->initErrorDispatcherMock($this->connector);
        $this->connector->setErrorsDispatcher($this->errorDispatcher);
    }

    protected function initDebugDispatcherMock(Connector $connector)
    {
        return $this->getMockBuilder('PhpConsole\Dispatcher\Debug')
            ->disableOriginalConstructor()
            ->setMethods(['dispatchDebug'])
            ->setConstructorArgs([$connector, $connector->getDumper()])
            ->getMock();
    }

    protected function initErrorDispatcherMock(Connector $connector)
    {
        return $this->getMockBuilder('PhpConsole\Dispatcher\Errors')
            ->disableOriginalConstructor()
            ->setMethods(['dispatchError', 'dispatchException'])
            ->setConstructorArgs([$connector, $connector->getDumper()])
            ->getMock();
    }

    protected function initConnectorMock()
    {
        $connector = $this->getMockBuilder('PhpConsole\Connector')
            ->disableOriginalConstructor()
            ->setMethods([
                'sendMessage',
                'onShutDown',
                'isActiveClient',
                'setSourcesBasePath',
                'setServerEncoding',
                'setPassword',
                'enableSslOnlyMode',
                'setAllowedIpMasks',
                'setHeadersLimit',
                'startEvalRequestsListener',
            ])
            ->getMock();

        $connector->expects($this->any())
            ->method('isActiveClient')
            ->will($this->returnValue(true));

        return $connector;
    }

    protected function getHandlerDefaultOption($name)
    {
        $handler = new PHPConsoleHandler([], $this->connector);
        $options = $handler->getOptions();

        return $options[$name];
    }

    protected function initLogger($handlerOptions = [], $level = Logger::DEBUG)
    {
        return new Logger('test', [
            new PHPConsoleHandler($handlerOptions, $this->connector, $level),
        ]);
    }

    public function test_init_with_default_connector()
    {
        $handler = new PHPConsoleHandler;
        $this->assertEquals(spl_object_hash(Connector::getInstance()), spl_object_hash($handler->getConnector()));
    }

    public function test_init_with_custom_connector()
    {
        $handler = new PHPConsoleHandler([], $this->connector);
        $this->assertEquals(spl_object_hash($this->connector), spl_object_hash($handler->getConnector()));
    }

    public function test_debug()
    {
        $this->debugDispatcher->expects($this->once())->method('dispatchDebug')->with($this->equalTo('test'));
        $this->initLogger()->addDebug('test');
    }

    public function test_debug_context_in_message()
    {
        $message = 'test';
        $tag = 'tag';
        $context = [$tag, 'custom' => mt_rand()];
        $expectedMessage = $message.' '.json_encode(array_slice($context, 1));
        $this->debugDispatcher->expects($this->once())->method('dispatchDebug')->with(
            $this->equalTo($expectedMessage),
            $this->equalTo($tag)
        );
        $this->initLogger()->addDebug($message, $context);
    }

    public function test_debug_tags($tagsContextKeys = null)
    {
        $expectedTags = mt_rand();
        $logger = $this->initLogger($tagsContextKeys ? ['debugTagsKeysInContext' => $tagsContextKeys] : []);
        if (! $tagsContextKeys) {
            $tagsContextKeys = $this->getHandlerDefaultOption('debugTagsKeysInContext');
        }
        foreach ($tagsContextKeys as $key) {
            $debugDispatcher = $this->initDebugDispatcherMock($this->connector);
            $debugDispatcher->expects($this->once())->method('dispatchDebug')->with(
                $this->anything(),
                $this->equalTo($expectedTags)
            );
            $this->connector->setDebugDispatcher($debugDispatcher);
            $logger->addDebug('test', [$key => $expectedTags]);
        }
    }

    public function test_error($classesPartialsTraceIgnore = null)
    {
        $code = E_USER_NOTICE;
        $message = 'message';
        $file = __FILE__;
        $line = __LINE__;
        $this->errorDispatcher->expects($this->once())->method('dispatchError')->with(
            $this->equalTo($code),
            $this->equalTo($message),
            $this->equalTo($file),
            $this->equalTo($line),
            $classesPartialsTraceIgnore ?: $this->equalTo($this->getHandlerDefaultOption('classesPartialsTraceIgnore'))
        );
        $errorHandler = ErrorHandler::register($this->initLogger($classesPartialsTraceIgnore ? ['classesPartialsTraceIgnore' => $classesPartialsTraceIgnore] : []), false);
        $errorHandler->registerErrorHandler([], false, E_USER_WARNING);
        $errorHandler->handleError($code, $message, $file, $line);
    }

    public function test_exception()
    {
        $e = new Exception;
        $this->errorDispatcher->expects($this->once())->method('dispatchException')->with(
            $this->equalTo($e)
        );
        $handler = $this->initLogger();
        $handler->log(
            \Psr\Log\LogLevel::ERROR,
            sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()),
            ['exception' => $e]
        );
    }

    /**
     * @expectedException Exception
     */
    public function test_wrong_options_throws_exception()
    {
        new PHPConsoleHandler(['xxx' => 1]);
    }

    public function test_option_enabled()
    {
        $this->debugDispatcher->expects($this->never())->method('dispatchDebug');
        $this->initLogger(['enabled' => false])->addDebug('test');
    }

    public function test_option_classes_partials_trace_ignore()
    {
        $this->testError(['Class', 'Namespace\\']);
    }

    public function test_option_debug_tags_keys_in_context()
    {
        $this->testDebugTags(['key1', 'key2']);
    }

    public function test_option_use_own_errors_and_exceptions_handler()
    {
        $this->initLogger(['useOwnErrorsHandler' => true, 'useOwnExceptionsHandler' => true]);
        $this->assertEquals([Handler::getInstance(), 'handleError'], set_error_handler(function () {}));
        $this->assertEquals([Handler::getInstance(), 'handleException'], set_exception_handler(function () {}));
    }

    public static function provideConnectorMethodsOptionsSets()
    {
        return [
            ['sourcesBasePath', 'setSourcesBasePath', __DIR__],
            ['serverEncoding', 'setServerEncoding', 'cp1251'],
            ['password', 'setPassword', '******'],
            ['enableSslOnlyMode', 'enableSslOnlyMode', true, false],
            ['ipMasks', 'setAllowedIpMasks', ['127.0.0.*']],
            ['headersLimit', 'setHeadersLimit', 2500],
            ['enableEvalListener', 'startEvalRequestsListener', true, false],
        ];
    }

    /**
     * @dataProvider provideConnectorMethodsOptionsSets
     */
    public function test_option_calls_connector_method($option, $method, $value, $isArgument = true)
    {
        $expectCall = $this->connector->expects($this->once())->method($method);
        if ($isArgument) {
            $expectCall->with($value);
        }
        new PHPConsoleHandler([$option => $value], $this->connector);
    }

    public function test_option_detect_dump_trace_and_source()
    {
        new PHPConsoleHandler(['detectDumpTraceAndSource' => true], $this->connector);
        $this->assertTrue($this->connector->getDebugDispatcher()->detectTraceAndSource);
    }

    public static function provideDumperOptionsValues()
    {
        return [
            ['dumperLevelLimit', 'levelLimit', 1001],
            ['dumperItemsCountLimit', 'itemsCountLimit', 1002],
            ['dumperItemSizeLimit', 'itemSizeLimit', 1003],
            ['dumperDumpSizeLimit', 'dumpSizeLimit', 1004],
            ['dumperDetectCallbacks', 'detectCallbacks', true],
        ];
    }

    /**
     * @dataProvider provideDumperOptionsValues
     */
    public function test_dumper_options($option, $dumperProperty, $value)
    {
        new PHPConsoleHandler([$option => $value], $this->connector);
        $this->assertEquals($value, $this->connector->getDumper()->$dumperProperty);
    }
}
