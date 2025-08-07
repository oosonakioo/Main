<?php

class Swift_Mime_AttachmentTest extends Swift_Mime_AbstractMimeEntityTest
{
    public function test_nesting_level_is_attachment()
    {
        $attachment = $this->_createAttachment($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(
            Swift_Mime_MimeEntity::LEVEL_MIXED, $attachment->getNestingLevel()
        );
    }

    public function test_disposition_is_returned_from_header()
    {
        /* -- RFC 2183, 2.1, 2.2.
     */

        $disposition = $this->_createHeader('Content-Disposition', 'attachment');
        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Disposition' => $disposition, ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals('attachment', $attachment->getDisposition());
    }

    public function test_disposition_is_set_in_header()
    {
        $disposition = $this->_createHeader('Content-Disposition', 'attachment',
            [], false
        );
        $disposition->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('inline');
        $disposition->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Disposition' => $disposition, ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $attachment->setDisposition('inline');
    }

    public function test_disposition_is_added_if_none_present()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addParameterizedHeader')
            ->once()
            ->with('Content-Disposition', 'inline');
        $headers->shouldReceive('addParameterizedHeader')
            ->zeroOrMoreTimes();

        $attachment = $this->_createAttachment($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $attachment->setDisposition('inline');
    }

    public function test_disposition_is_auto_defaulted_to_attachment()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addParameterizedHeader')
            ->once()
            ->with('Content-Disposition', 'attachment');
        $headers->shouldReceive('addParameterizedHeader')
            ->zeroOrMoreTimes();

        $attachment = $this->_createAttachment($headers, $this->_createEncoder(),
            $this->_createCache()
        );
    }

    public function test_default_content_type_initialized_to_octet_stream()
    {
        $cType = $this->_createHeader('Content-Type', '',
            [], false
        );
        $cType->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('application/octet-stream');
        $cType->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Type' => $cType, ]),
            $this->_createEncoder(), $this->_createCache()
        );
    }

    public function test_filename_is_returned_from_header()
    {
        /* -- RFC 2183, 2.3.
     */

        $disposition = $this->_createHeader('Content-Disposition', 'attachment',
            ['filename' => 'foo.txt']
        );
        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Disposition' => $disposition, ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals('foo.txt', $attachment->getFilename());
    }

    public function test_filename_is_set_in_header()
    {
        $disposition = $this->_createHeader('Content-Disposition', 'attachment',
            ['filename' => 'foo.txt'], false
        );
        $disposition->shouldReceive('setParameter')
            ->once()
            ->with('filename', 'bar.txt');
        $disposition->shouldReceive('setParameter')
            ->zeroOrMoreTimes();

        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Disposition' => $disposition, ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $attachment->setFilename('bar.txt');
    }

    public function test_setting_filename_sets_name_in_content_type()
    {
        /*
     This is a legacy requirement which isn't covered by up-to-date RFCs.
     */

        $cType = $this->_createHeader('Content-Type', 'text/plain',
            [], false
        );
        $cType->shouldReceive('setParameter')
            ->once()
            ->with('name', 'bar.txt');
        $cType->shouldReceive('setParameter')
            ->zeroOrMoreTimes();

        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Type' => $cType, ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $attachment->setFilename('bar.txt');
    }

    public function test_size_is_returned_from_header()
    {
        /* -- RFC 2183, 2.7.
     */

        $disposition = $this->_createHeader('Content-Disposition', 'attachment',
            ['size' => 1234]
        );
        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Disposition' => $disposition, ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(1234, $attachment->getSize());
    }

    public function test_size_is_set_in_header()
    {
        $disposition = $this->_createHeader('Content-Disposition', 'attachment',
            [], false
        );
        $disposition->shouldReceive('setParameter')
            ->once()
            ->with('size', 12345);
        $disposition->shouldReceive('setParameter')
            ->zeroOrMoreTimes();

        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Disposition' => $disposition, ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $attachment->setSize(12345);
    }

    public function test_filname_can_be_read_from_file_stream()
    {
        $file = $this->_createFileStream('/bar/file.ext', '');
        $disposition = $this->_createHeader('Content-Disposition', 'attachment',
            ['filename' => 'foo.txt'], false
        );
        $disposition->shouldReceive('setParameter')
            ->once()
            ->with('filename', 'file.ext');

        $attachment = $this->_createAttachment($this->_createHeaderSet([
            'Content-Disposition' => $disposition, ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $attachment->setFile($file);
    }

    public function test_content_type_can_be_set_via_set_file()
    {
        $file = $this->_createFileStream('/bar/file.ext', '');
        $disposition = $this->_createHeader('Content-Disposition', 'attachment',
            ['filename' => 'foo.txt'], false
        );
        $disposition->shouldReceive('setParameter')
            ->once()
            ->with('filename', 'file.ext');

        $ctype = $this->_createHeader('Content-Type', 'text/plain', [], false);
        $ctype->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('text/html');
        $ctype->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $headers = $this->_createHeaderSet([
            'Content-Disposition' => $disposition,
            'Content-Type' => $ctype,
        ]);

        $attachment = $this->_createAttachment($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $attachment->setFile($file, 'text/html');
    }

    public function XtestContentTypeCanBeLookedUpFromCommonListIfNotProvided()
    {
        $file = $this->_createFileStream('/bar/file.zip', '');
        $disposition = $this->_createHeader('Content-Disposition', 'attachment',
            ['filename' => 'foo.zip'], false
        );
        $disposition->shouldReceive('setParameter')
            ->once()
            ->with('filename', 'file.zip');

        $ctype = $this->_createHeader('Content-Type', 'text/plain', [], false);
        $ctype->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('application/zip');
        $ctype->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $headers = $this->_createHeaderSet([
            'Content-Disposition' => $disposition,
            'Content-Type' => $ctype,
        ]);

        $attachment = $this->_createAttachment($headers, $this->_createEncoder(),
            $this->_createCache(), ['zip' => 'application/zip', 'txt' => 'text/plain']
        );
        $attachment->setFile($file);
    }

    public function test_data_can_be_read_from_file()
    {
        $file = $this->_createFileStream('/foo/file.ext', '<some data>');
        $attachment = $this->_createAttachment($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $attachment->setFile($file);
        $this->assertEquals('<some data>', $attachment->getBody());
    }

    public function test_fluid_interface()
    {
        $attachment = $this->_createAttachment($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertSame($attachment,
            $attachment
                ->setContentType('application/pdf')
                ->setEncoder($this->_createEncoder())
                ->setId('foo@bar')
                ->setDescription('my pdf')
                ->setMaxLineLength(998)
                ->setBody('xx')
                ->setBoundary('xyz')
                ->setChildren([])
                ->setDisposition('inline')
                ->setFilename('afile.txt')
                ->setSize(123)
                ->setFile($this->_createFileStream('foo.txt', ''))
        );
    }

    protected function _createEntity($headers, $encoder, $cache)
    {
        return $this->_createAttachment($headers, $encoder, $cache);
    }

    protected function _createAttachment($headers, $encoder, $cache, $mimeTypes = [])
    {
        return new Swift_Mime_Attachment($headers, $encoder, $cache, new Swift_Mime_Grammar, $mimeTypes);
    }

    protected function _createFileStream($path, $data, $stub = true)
    {
        $file = $this->getMockery('Swift_FileStream');
        $file->shouldReceive('getPath')
            ->zeroOrMoreTimes()
            ->andReturn($path);
        $file->shouldReceive('read')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function () use ($data) {
                static $first = true;
                if (! $first) {
                    return false;
                }

                $first = false;

                return $data;
            });
        $file->shouldReceive('setReadPointer')
            ->zeroOrMoreTimes();

        return $file;
    }
}
