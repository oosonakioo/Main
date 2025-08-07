<?php

class Swift_Transport_StreamBufferTest extends \PHPUnit_Framework_TestCase
{
    public function test_setting_write_translations_creates_filters()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createFilter')
            ->with('a', 'b')
            ->will($this->returnCallback([$this, '_createFilter']));

        $buffer = $this->_createBuffer($factory);
        $buffer->setWriteTranslations(['a' => 'b']);
    }

    public function test_overriding_translations_only_adds_needed_filters()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->exactly(2))
            ->method('createFilter')
            ->will($this->returnCallback([$this, '_createFilter']));

        $buffer = $this->_createBuffer($factory);
        $buffer->setWriteTranslations(['a' => 'b']);
        $buffer->setWriteTranslations(['x' => 'y', 'a' => 'b']);
    }

    private function _createBuffer($replacementFactory)
    {
        return new Swift_Transport_StreamBuffer($replacementFactory);
    }

    private function _createFactory()
    {
        return $this->getMockBuilder('Swift_ReplacementFilterFactory')->getMock();
    }

    public function _createFilter()
    {
        return $this->getMockBuilder('Swift_StreamFilter')->getMock();
    }
}
