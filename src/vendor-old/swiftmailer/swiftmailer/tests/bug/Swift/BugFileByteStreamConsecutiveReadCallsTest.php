<?php

class Swift_FileByteStreamConsecutiveReadCalls extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @expectedException \Swift_IoException
     */
    public function should_throw_exception_on_consecutive_read()
    {
        $fbs = new \Swift_ByteStream_FileByteStream('does not exist');
        try {
            $fbs->read(100);
        } catch (\Swift_IoException $exc) {
            $fbs->read(100);
        }
    }
}
