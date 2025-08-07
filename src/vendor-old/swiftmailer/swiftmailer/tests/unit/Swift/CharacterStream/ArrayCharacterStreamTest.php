<?php

class Swift_CharacterStream_ArrayCharacterStreamTest extends \SwiftMailerTestCase
{
    public function test_validator_algorithm_on_import_string()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);

        $stream->importString(pack('C*',
            0xD0, 0x94,
            0xD0, 0xB6,
            0xD0, 0xBE,
            0xD1, 0x8D,
            0xD0, 0xBB,
            0xD0, 0xB0
        )
        );
    }

    public function test_characters_written_use_validator()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);

        $stream->importString(pack('C*', 0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE));

        $stream->write(pack('C*',
            0xD0, 0xBB,
            0xD1, 0x8E,
            0xD0, 0xB1,
            0xD1, 0x8B,
            0xD1, 0x85
        )
        );
    }

    public function test_read_characters_are_in_tact()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        // String
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        // Stream
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);

        $stream->importString(pack('C*', 0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE));

        $stream->write(pack('C*',
            0xD0, 0xBB,
            0xD1, 0x8E,
            0xD0, 0xB1,
            0xD1, 0x8B,
            0xD1, 0x85
        )
        );

        $this->assertIdenticalBinary(pack('C*', 0xD0, 0x94), $stream->read(1));
        $this->assertIdenticalBinary(
            pack('C*', 0xD0, 0xB6, 0xD0, 0xBE), $stream->read(2)
        );
        $this->assertIdenticalBinary(pack('C*', 0xD0, 0xBB), $stream->read(1));
        $this->assertIdenticalBinary(
            pack('C*', 0xD1, 0x8E, 0xD0, 0xB1, 0xD1, 0x8B), $stream->read(3)
        );
        $this->assertIdenticalBinary(pack('C*', 0xD1, 0x85), $stream->read(1));

        $this->assertFalse($stream->read(1));
    }

    public function test_characters_can_be_read_as_byte_arrays()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        // String
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        // Stream
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1], 1)->andReturn(1);

        $stream->importString(pack('C*', 0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE));

        $stream->write(pack('C*',
            0xD0, 0xBB,
            0xD1, 0x8E,
            0xD0, 0xB1,
            0xD1, 0x8B,
            0xD1, 0x85
        )
        );

        $this->assertEquals([0xD0, 0x94], $stream->readBytes(1));
        $this->assertEquals([0xD0, 0xB6, 0xD0, 0xBE], $stream->readBytes(2));
        $this->assertEquals([0xD0, 0xBB], $stream->readBytes(1));
        $this->assertEquals(
            [0xD1, 0x8E, 0xD0, 0xB1, 0xD1, 0x8B], $stream->readBytes(3)
        );
        $this->assertEquals([0xD1, 0x85], $stream->readBytes(1));

        $this->assertFalse($stream->readBytes(1));
    }

    public function test_requesting_large_char_count_past_end_of_stream()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);

        $stream->importString(pack('C*', 0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE));

        $this->assertIdenticalBinary(pack('C*', 0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE),
            $stream->read(100)
        );

        $this->assertFalse($stream->read(1));
    }

    public function test_requesting_byte_array_count_past_end_of_stream()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);

        $stream->importString(pack('C*', 0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE));

        $this->assertEquals([0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE],
            $stream->readBytes(100)
        );

        $this->assertFalse($stream->readBytes(1));
    }

    public function test_pointer_offset_can_be_set()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);

        $stream->importString(pack('C*', 0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE));

        $this->assertIdenticalBinary(pack('C*', 0xD0, 0x94), $stream->read(1));

        $stream->setPointer(0);

        $this->assertIdenticalBinary(pack('C*', 0xD0, 0x94), $stream->read(1));

        $stream->setPointer(2);

        $this->assertIdenticalBinary(pack('C*', 0xD0, 0xBE), $stream->read(1));
    }

    public function test_contents_can_be_flushed()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);

        $stream->importString(pack('C*', 0xD0, 0x94, 0xD0, 0xB6, 0xD0, 0xBE));

        $stream->flushContents();

        $this->assertFalse($stream->read(1));
    }

    public function test_byte_stream_can_be_importing_uses_validator()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);
        $os = $this->_getByteStream();

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $os->shouldReceive('setReadPointer')
            ->between(0, 1)
            ->with(0);
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xD0));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0x94));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xD0));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xB6));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xD0));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xBE));
        $os->shouldReceive('read')
            ->zeroOrMoreTimes()
            ->andReturn(false);

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);

        $stream->importByteStream($os);
    }

    public function test_importing_stream_produces_correct_char_array()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);
        $os = $this->_getByteStream();

        $stream = new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8');

        $os->shouldReceive('setReadPointer')
            ->between(0, 1)
            ->with(0);
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xD0));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0x94));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xD0));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xB6));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xD0));
        $os->shouldReceive('read')->once()->andReturn(pack('C*', 0xBE));
        $os->shouldReceive('read')
            ->zeroOrMoreTimes()
            ->andReturn(false);

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0], 1)->andReturn(1);

        $stream->importByteStream($os);

        $this->assertIdenticalBinary(pack('C*', 0xD0, 0x94), $stream->read(1));
        $this->assertIdenticalBinary(pack('C*', 0xD0, 0xB6), $stream->read(1));
        $this->assertIdenticalBinary(pack('C*', 0xD0, 0xBE), $stream->read(1));

        $this->assertFalse($stream->read(1));
    }

    public function test_algorithm_with_fixed_width_charsets()
    {
        $reader = $this->_getReader();
        $factory = $this->_getFactory($reader);

        $reader->shouldReceive('getInitialByteSize')
            ->zeroOrMoreTimes()
            ->andReturn(2);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD1, 0x8D], 2);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0, 0xBB], 2);
        $reader->shouldReceive('validateByteSequence')->once()->with([0xD0, 0xB0], 2);

        $stream = new Swift_CharacterStream_ArrayCharacterStream(
            $factory, 'utf-8'
        );
        $stream->importString(pack('C*', 0xD1, 0x8D, 0xD0, 0xBB, 0xD0, 0xB0));

        $this->assertIdenticalBinary(pack('C*', 0xD1, 0x8D), $stream->read(1));
        $this->assertIdenticalBinary(pack('C*', 0xD0, 0xBB), $stream->read(1));
        $this->assertIdenticalBinary(pack('C*', 0xD0, 0xB0), $stream->read(1));

        $this->assertFalse($stream->read(1));
    }

    private function _getReader()
    {
        return $this->getMockery('Swift_CharacterReader');
    }

    private function _getFactory($reader)
    {
        $factory = $this->getMockery('Swift_CharacterReaderFactory');
        $factory->shouldReceive('getReaderFor')
            ->zeroOrMoreTimes()
            ->with('utf-8')
            ->andReturn($reader);

        return $factory;
    }

    private function _getByteStream()
    {
        return $this->getMockery('Swift_OutputByteStream');
    }
}
