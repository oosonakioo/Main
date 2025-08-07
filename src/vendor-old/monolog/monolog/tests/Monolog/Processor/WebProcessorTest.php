<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\TestCase;

class WebProcessorTest extends TestCase
{
    public function test_processor()
    {
        $server = [
            'REQUEST_URI' => 'A',
            'REMOTE_ADDR' => 'B',
            'REQUEST_METHOD' => 'C',
            'HTTP_REFERER' => 'D',
            'SERVER_NAME' => 'F',
            'UNIQUE_ID' => 'G',
        ];

        $processor = new WebProcessor($server);
        $record = $processor($this->getRecord());
        $this->assertEquals($server['REQUEST_URI'], $record['extra']['url']);
        $this->assertEquals($server['REMOTE_ADDR'], $record['extra']['ip']);
        $this->assertEquals($server['REQUEST_METHOD'], $record['extra']['http_method']);
        $this->assertEquals($server['HTTP_REFERER'], $record['extra']['referrer']);
        $this->assertEquals($server['SERVER_NAME'], $record['extra']['server']);
        $this->assertEquals($server['UNIQUE_ID'], $record['extra']['unique_id']);
    }

    public function test_processor_do_nothing_if_no_request_uri()
    {
        $server = [
            'REMOTE_ADDR' => 'B',
            'REQUEST_METHOD' => 'C',
        ];
        $processor = new WebProcessor($server);
        $record = $processor($this->getRecord());
        $this->assertEmpty($record['extra']);
    }

    public function test_processor_return_null_if_no_http_referer()
    {
        $server = [
            'REQUEST_URI' => 'A',
            'REMOTE_ADDR' => 'B',
            'REQUEST_METHOD' => 'C',
            'SERVER_NAME' => 'F',
        ];
        $processor = new WebProcessor($server);
        $record = $processor($this->getRecord());
        $this->assertNull($record['extra']['referrer']);
    }

    public function test_processor_does_not_add_unique_id_if_not_present()
    {
        $server = [
            'REQUEST_URI' => 'A',
            'REMOTE_ADDR' => 'B',
            'REQUEST_METHOD' => 'C',
            'SERVER_NAME' => 'F',
        ];
        $processor = new WebProcessor($server);
        $record = $processor($this->getRecord());
        $this->assertFalse(isset($record['extra']['unique_id']));
    }

    public function test_processor_adds_only_requested_extra_fields()
    {
        $server = [
            'REQUEST_URI' => 'A',
            'REMOTE_ADDR' => 'B',
            'REQUEST_METHOD' => 'C',
            'SERVER_NAME' => 'F',
        ];

        $processor = new WebProcessor($server, ['url', 'http_method']);
        $record = $processor($this->getRecord());

        $this->assertSame(['url' => 'A', 'http_method' => 'C'], $record['extra']);
    }

    public function test_processor_configuring_of_extra_fields()
    {
        $server = [
            'REQUEST_URI' => 'A',
            'REMOTE_ADDR' => 'B',
            'REQUEST_METHOD' => 'C',
            'SERVER_NAME' => 'F',
        ];

        $processor = new WebProcessor($server, ['url' => 'REMOTE_ADDR']);
        $record = $processor($this->getRecord());

        $this->assertSame(['url' => 'B'], $record['extra']);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function test_invalid_data()
    {
        new WebProcessor(new \stdClass);
    }
}
