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

use Monolog\TestCase;

/**
 * @covers Monolog\Handler\RotatingFileHandler
 */
class RotatingFileHandlerTest extends TestCase
{
    /**
     * This var should be private but then the anonymous function
     * in the `setUp` method won't be able to set it. `$this` cant't
     * be used in the anonymous function in `setUp` because PHP 5.3
     * does not support it.
     */
    public $lastError;

    protected function setUp()
    {
        $dir = __DIR__.'/Fixtures';
        chmod($dir, 0777);
        if (! is_writable($dir)) {
            $this->markTestSkipped($dir.' must be writable to test the RotatingFileHandler.');
        }
        $this->lastError = null;
        $self = $this;
        // workaround with &$self used for PHP 5.3
        set_error_handler(function ($code, $message) use (&$self) {
            $self->lastError = [
                'code' => $code,
                'message' => $message,
            ];
        });
    }

    private function assertErrorWasTriggered($code, $message)
    {
        if (empty($this->lastError)) {
            $this->fail(
                sprintf(
                    'Failed asserting that error with code `%d` and message `%s` was triggered',
                    $code,
                    $message
                )
            );
        }
        $this->assertEquals($code, $this->lastError['code'], sprintf('Expected an error with code %d to be triggered, got `%s` instead', $code, $this->lastError['code']));
        $this->assertEquals($message, $this->lastError['message'], sprintf('Expected an error with message `%d` to be triggered, got `%s` instead', $message, $this->lastError['message']));
    }

    public function test_rotation_creates_new_file()
    {
        touch(__DIR__.'/Fixtures/foo-'.date('Y-m-d', time() - 86400).'.rot');

        $handler = new RotatingFileHandler(__DIR__.'/Fixtures/foo.rot');
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord());

        $log = __DIR__.'/Fixtures/foo-'.date('Y-m-d').'.rot';
        $this->assertTrue(file_exists($log));
        $this->assertEquals('test', file_get_contents($log));
    }

    /**
     * @dataProvider rotationTests
     */
    public function test_rotation($createFile, $dateFormat, $timeCallback)
    {
        touch($old1 = __DIR__.'/Fixtures/foo-'.date($dateFormat, $timeCallback(-1)).'.rot');
        touch($old2 = __DIR__.'/Fixtures/foo-'.date($dateFormat, $timeCallback(-2)).'.rot');
        touch($old3 = __DIR__.'/Fixtures/foo-'.date($dateFormat, $timeCallback(-3)).'.rot');
        touch($old4 = __DIR__.'/Fixtures/foo-'.date($dateFormat, $timeCallback(-4)).'.rot');

        $log = __DIR__.'/Fixtures/foo-'.date($dateFormat).'.rot';

        if ($createFile) {
            touch($log);
        }

        $handler = new RotatingFileHandler(__DIR__.'/Fixtures/foo.rot', 2);
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->setFilenameFormat('{filename}-{date}', $dateFormat);
        $handler->handle($this->getRecord());

        $handler->close();

        $this->assertTrue(file_exists($log));
        $this->assertTrue(file_exists($old1));
        $this->assertEquals($createFile, file_exists($old2));
        $this->assertEquals($createFile, file_exists($old3));
        $this->assertEquals($createFile, file_exists($old4));
        $this->assertEquals('test', file_get_contents($log));
    }

    public function rotationTests()
    {
        $now = time();
        $dayCallback = function ($ago) use ($now) {
            return $now + 86400 * $ago;
        };
        $monthCallback = function ($ago) {
            return gmmktime(0, 0, 0, date('n') + $ago, date('d'), date('Y'));
        };
        $yearCallback = function ($ago) {
            return gmmktime(0, 0, 0, date('n'), date('d'), date('Y') + $ago);
        };

        return [
            'Rotation is triggered when the file of the current day is not present' => [true, RotatingFileHandler::FILE_PER_DAY, $dayCallback],
            'Rotation is not triggered when the file of the current day is already present' => [false, RotatingFileHandler::FILE_PER_DAY, $dayCallback],

            'Rotation is triggered when the file of the current month is not present' => [true, RotatingFileHandler::FILE_PER_MONTH, $monthCallback],
            'Rotation is not triggered when the file of the current month is already present' => [false, RotatingFileHandler::FILE_PER_MONTH, $monthCallback],

            'Rotation is triggered when the file of the current year is not present' => [true, RotatingFileHandler::FILE_PER_YEAR, $yearCallback],
            'Rotation is not triggered when the file of the current year is already present' => [false, RotatingFileHandler::FILE_PER_YEAR, $yearCallback],
        ];
    }

    /**
     * @dataProvider dateFormatProvider
     */
    public function test_allow_only_fixed_defined_date_formats($dateFormat, $valid)
    {
        $handler = new RotatingFileHandler(__DIR__.'/Fixtures/foo.rot', 2);
        $handler->setFilenameFormat('{filename}-{date}', $dateFormat);
        if (! $valid) {
            $this->assertErrorWasTriggered(
                E_USER_DEPRECATED,
                'Invalid date format - format must be one of RotatingFileHandler::FILE_PER_DAY ("Y-m-d"), '.
                'RotatingFileHandler::FILE_PER_MONTH ("Y-m") or RotatingFileHandler::FILE_PER_YEAR ("Y"), '.
                'or you can set one of the date formats using slashes, underscores and/or dots instead of dashes.'
            );
        }
    }

    public function dateFormatProvider()
    {
        return [
            [RotatingFileHandler::FILE_PER_DAY, true],
            [RotatingFileHandler::FILE_PER_MONTH, true],
            [RotatingFileHandler::FILE_PER_YEAR, true],
            ['m-d-Y', false],
            ['Y-m-d-h-i', false],
        ];
    }

    /**
     * @dataProvider filenameFormatProvider
     */
    public function test_disallow_filename_formats_without_date($filenameFormat, $valid)
    {
        $handler = new RotatingFileHandler(__DIR__.'/Fixtures/foo.rot', 2);
        $handler->setFilenameFormat($filenameFormat, RotatingFileHandler::FILE_PER_DAY);
        if (! $valid) {
            $this->assertErrorWasTriggered(
                E_USER_DEPRECATED,
                'Invalid filename format - format should contain at least `{date}`, because otherwise rotating is impossible.'
            );
        }
    }

    public function filenameFormatProvider()
    {
        return [
            ['{filename}', false],
            ['{filename}-{date}', true],
            ['{date}', true],
            ['foobar-{date}', true],
            ['foo-{date}-bar', true],
            ['{date}-foobar', true],
            ['foobar', false],
        ];
    }

    public function test_reuse_current_file()
    {
        $log = __DIR__.'/Fixtures/foo-'.date('Y-m-d').'.rot';
        file_put_contents($log, 'foo');
        $handler = new RotatingFileHandler(__DIR__.'/Fixtures/foo.rot');
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord());
        $this->assertEquals('footest', file_get_contents($log));
    }

    protected function tearDown()
    {
        foreach (glob(__DIR__.'/Fixtures/*.rot') as $file) {
            unlink($file);
        }
        restore_error_handler();
    }
}
