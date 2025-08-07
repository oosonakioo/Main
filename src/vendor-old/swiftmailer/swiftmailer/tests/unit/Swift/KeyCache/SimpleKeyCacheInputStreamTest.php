<?php

class Swift_KeyCache_SimpleKeyCacheInputStreamTest extends \PHPUnit_Framework_TestCase
{
    private $_nsKey = 'ns1';

    public function test_stream_writes_to_cache_in_append_mode()
    {
        $cache = $this->getMockBuilder('Swift_KeyCache')->getMock();
        $cache->expects($this->at(0))
            ->method('setString')
            ->with($this->_nsKey, 'foo', 'a', Swift_KeyCache::MODE_APPEND);
        $cache->expects($this->at(1))
            ->method('setString')
            ->with($this->_nsKey, 'foo', 'b', Swift_KeyCache::MODE_APPEND);
        $cache->expects($this->at(2))
            ->method('setString')
            ->with($this->_nsKey, 'foo', 'c', Swift_KeyCache::MODE_APPEND);

        $stream = new Swift_KeyCache_SimpleKeyCacheInputStream;
        $stream->setKeyCache($cache);
        $stream->setNsKey($this->_nsKey);
        $stream->setItemKey('foo');

        $stream->write('a');
        $stream->write('b');
        $stream->write('c');
    }

    public function test_flush_content_clears_key()
    {
        $cache = $this->getMockBuilder('Swift_KeyCache')->getMock();
        $cache->expects($this->once())
            ->method('clearKey')
            ->with($this->_nsKey, 'foo');

        $stream = new Swift_KeyCache_SimpleKeyCacheInputStream;
        $stream->setKeyCache($cache);
        $stream->setNsKey($this->_nsKey);
        $stream->setItemKey('foo');

        $stream->flushBuffers();
    }

    public function test_cloned_stream_still_references_same_cache()
    {
        $cache = $this->getMockBuilder('Swift_KeyCache')->getMock();
        $cache->expects($this->at(0))
            ->method('setString')
            ->with($this->_nsKey, 'foo', 'a', Swift_KeyCache::MODE_APPEND);
        $cache->expects($this->at(1))
            ->method('setString')
            ->with($this->_nsKey, 'foo', 'b', Swift_KeyCache::MODE_APPEND);
        $cache->expects($this->at(2))
            ->method('setString')
            ->with('test', 'bar', 'x', Swift_KeyCache::MODE_APPEND);

        $stream = new Swift_KeyCache_SimpleKeyCacheInputStream;
        $stream->setKeyCache($cache);
        $stream->setNsKey($this->_nsKey);
        $stream->setItemKey('foo');

        $stream->write('a');
        $stream->write('b');

        $newStream = clone $stream;
        $newStream->setKeyCache($cache);
        $newStream->setNsKey('test');
        $newStream->setItemKey('bar');

        $newStream->write('x');
    }
}
