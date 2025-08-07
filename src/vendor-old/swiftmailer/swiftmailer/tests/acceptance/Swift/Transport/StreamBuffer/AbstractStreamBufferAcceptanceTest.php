<?php

abstract class Swift_Transport_StreamBuffer_AbstractStreamBufferAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    protected $_buffer;

    abstract protected function _initializeBuffer();

    protected function setUp()
    {
        if (getenv('TRAVIS') == true) {
            $this->markTestSkipped(
                'Will fail on travis-ci if not skipped due to travis blocking '.
                'socket mailing tcp connections.'
            );
        }

        $this->_buffer = new Swift_Transport_StreamBuffer(
            $this->getMockBuilder('Swift_ReplacementFilterFactory')->getMock()
        );
    }

    public function test_read_line()
    {
        $this->_initializeBuffer();

        $line = $this->_buffer->readLine(0);
        $this->assertRegExp('/^[0-9]{3}.*?\r\n$/D', $line);
        $seq = $this->_buffer->write("QUIT\r\n");
        $this->assertTrue((bool) $seq);
        $line = $this->_buffer->readLine($seq);
        $this->assertRegExp('/^[0-9]{3}.*?\r\n$/D', $line);
        $this->_buffer->terminate();
    }

    public function test_write()
    {
        $this->_initializeBuffer();

        $line = $this->_buffer->readLine(0);
        $this->assertRegExp('/^[0-9]{3}.*?\r\n$/D', $line);

        $seq = $this->_buffer->write("HELO foo\r\n");
        $this->assertTrue((bool) $seq);
        $line = $this->_buffer->readLine($seq);
        $this->assertRegExp('/^[0-9]{3}.*?\r\n$/D', $line);

        $seq = $this->_buffer->write("QUIT\r\n");
        $this->assertTrue((bool) $seq);
        $line = $this->_buffer->readLine($seq);
        $this->assertRegExp('/^[0-9]{3}.*?\r\n$/D', $line);
        $this->_buffer->terminate();
    }

    public function test_binding_other_streams_mirrors_write_operations()
    {
        $this->_initializeBuffer();

        $is1 = $this->_createMockInputStream();
        $is2 = $this->_createMockInputStream();

        $is1->expects($this->at(0))
            ->method('write')
            ->with('x');
        $is1->expects($this->at(1))
            ->method('write')
            ->with('y');
        $is2->expects($this->at(0))
            ->method('write')
            ->with('x');
        $is2->expects($this->at(1))
            ->method('write')
            ->with('y');

        $this->_buffer->bind($is1);
        $this->_buffer->bind($is2);

        $this->_buffer->write('x');
        $this->_buffer->write('y');
    }

    public function test_binding_other_streams_mirrors_flush_operations()
    {
        $this->_initializeBuffer();

        $is1 = $this->_createMockInputStream();
        $is2 = $this->_createMockInputStream();

        $is1->expects($this->once())
            ->method('flushBuffers');
        $is2->expects($this->once())
            ->method('flushBuffers');

        $this->_buffer->bind($is1);
        $this->_buffer->bind($is2);

        $this->_buffer->flushBuffers();
    }

    public function test_unbinding_stream_prevents_further_writes()
    {
        $this->_initializeBuffer();

        $is1 = $this->_createMockInputStream();
        $is2 = $this->_createMockInputStream();

        $is1->expects($this->at(0))
            ->method('write')
            ->with('x');
        $is1->expects($this->at(1))
            ->method('write')
            ->with('y');
        $is2->expects($this->once())
            ->method('write')
            ->with('x');

        $this->_buffer->bind($is1);
        $this->_buffer->bind($is2);

        $this->_buffer->write('x');

        $this->_buffer->unbind($is2);

        $this->_buffer->write('y');
    }

    private function _createMockInputStream()
    {
        return $this->getMockBuilder('Swift_InputByteStream')->getMock();
    }
}
