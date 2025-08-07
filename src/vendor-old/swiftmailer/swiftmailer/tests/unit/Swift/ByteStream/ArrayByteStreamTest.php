<?php

class Swift_ByteStream_ArrayByteStreamTest extends \PHPUnit_Framework_TestCase
{
    public function test_reading_single_bytes_from_base_input()
    {
        $input = ['a', 'b', 'c'];
        $bs = $this->_createArrayStream($input);
        $output = [];
        while (false !== $bytes = $bs->read(1)) {
            $output[] = $bytes;
        }
        $this->assertEquals($input, $output,
            '%s: Bytes read from stream should be the same as bytes in constructor'
        );
    }

    public function test_reading_multiple_bytes_from_base_input()
    {
        $input = ['a', 'b', 'c', 'd'];
        $bs = $this->_createArrayStream($input);
        $output = [];
        while (false !== $bytes = $bs->read(2)) {
            $output[] = $bytes;
        }
        $this->assertEquals(['ab', 'cd'], $output,
            '%s: Bytes read from stream should be in pairs'
        );
    }

    public function test_reading_odd_offset_on_last_byte()
    {
        $input = ['a', 'b', 'c', 'd', 'e'];
        $bs = $this->_createArrayStream($input);
        $output = [];
        while (false !== $bytes = $bs->read(2)) {
            $output[] = $bytes;
        }
        $this->assertEquals(['ab', 'cd', 'e'], $output,
            '%s: Bytes read from stream should be in pairs except final read'
        );
    }

    public function test_setting_pointer_partway()
    {
        $input = ['a', 'b', 'c'];
        $bs = $this->_createArrayStream($input);
        $bs->setReadPointer(1);
        $this->assertEquals('b', $bs->read(1),
            '%s: Byte should be second byte since pointer as at offset 1'
        );
    }

    public function test_resetting_pointer_after_exhaustion()
    {
        $input = ['a', 'b', 'c'];
        $bs = $this->_createArrayStream($input);
        while ($bs->read(1) !== false);

        $bs->setReadPointer(0);
        $this->assertEquals('a', $bs->read(1),
            '%s: Byte should be first byte since pointer as at offset 0'
        );
    }

    public function test_pointer_never_sets_below_zero()
    {
        $input = ['a', 'b', 'c'];
        $bs = $this->_createArrayStream($input);

        $bs->setReadPointer(-1);
        $this->assertEquals('a', $bs->read(1),
            '%s: Byte should be first byte since pointer should be at offset 0'
        );
    }

    public function test_pointer_never_sets_above_stack_size()
    {
        $input = ['a', 'b', 'c'];
        $bs = $this->_createArrayStream($input);

        $bs->setReadPointer(3);
        $this->assertFalse($bs->read(1),
            '%s: Stream should be at end and thus return false'
        );
    }

    public function test_bytes_can_be_written_to_stream()
    {
        $input = ['a', 'b', 'c'];
        $bs = $this->_createArrayStream($input);

        $bs->write('de');

        $output = [];
        while (false !== $bytes = $bs->read(1)) {
            $output[] = $bytes;
        }
        $this->assertEquals(['a', 'b', 'c', 'd', 'e'], $output,
            '%s: Bytes read from stream should be from initial stack + written'
        );
    }

    public function test_contents_can_be_flushed()
    {
        $input = ['a', 'b', 'c'];
        $bs = $this->_createArrayStream($input);

        $bs->flushBuffers();

        $this->assertFalse($bs->read(1),
            '%s: Contents have been flushed so read() should return false'
        );
    }

    public function test_constructor_can_take_string_argument()
    {
        $bs = $this->_createArrayStream('abc');
        $output = [];
        while (false !== $bytes = $bs->read(1)) {
            $output[] = $bytes;
        }
        $this->assertEquals(['a', 'b', 'c'], $output,
            '%s: Bytes read from stream should be the same as bytes in constructor'
        );
    }

    public function test_binding_other_streams_mirrors_write_operations()
    {
        $bs = $this->_createArrayStream('');
        $is1 = $this->getMockBuilder('Swift_InputByteStream')->getMock();
        $is2 = $this->getMockBuilder('Swift_InputByteStream')->getMock();

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

        $bs->bind($is1);
        $bs->bind($is2);

        $bs->write('x');
        $bs->write('y');
    }

    public function test_binding_other_streams_mirrors_flush_operations()
    {
        $bs = $this->_createArrayStream('');
        $is1 = $this->getMockBuilder('Swift_InputByteStream')->getMock();
        $is2 = $this->getMockBuilder('Swift_InputByteStream')->getMock();

        $is1->expects($this->once())
            ->method('flushBuffers');
        $is2->expects($this->once())
            ->method('flushBuffers');

        $bs->bind($is1);
        $bs->bind($is2);

        $bs->flushBuffers();
    }

    public function test_unbinding_stream_prevents_further_writes()
    {
        $bs = $this->_createArrayStream('');
        $is1 = $this->getMockBuilder('Swift_InputByteStream')->getMock();
        $is2 = $this->getMockBuilder('Swift_InputByteStream')->getMock();

        $is1->expects($this->at(0))
            ->method('write')
            ->with('x');
        $is1->expects($this->at(1))
            ->method('write')
            ->with('y');
        $is2->expects($this->once())
            ->method('write')
            ->with('x');

        $bs->bind($is1);
        $bs->bind($is2);

        $bs->write('x');

        $bs->unbind($is2);

        $bs->write('y');
    }

    private function _createArrayStream($input)
    {
        return new Swift_ByteStream_ArrayByteStream($input);
    }
}
