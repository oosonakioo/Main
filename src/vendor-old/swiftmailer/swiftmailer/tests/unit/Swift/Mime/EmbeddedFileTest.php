<?php

class Swift_Mime_EmbeddedFileTest extends Swift_Mime_AttachmentTest
{
    public function test_nesting_level_is_attachment()
    {
        // Overridden
    }

    public function test_nesting_level_is_embedded()
    {
        $file = $this->_createEmbeddedFile($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(
            Swift_Mime_MimeEntity::LEVEL_RELATED, $file->getNestingLevel()
        );
    }

    public function test_id_is_auto_generated()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addIdHeader')
            ->once()
            ->with('Content-ID', '/^.*?@.*?$/D');

        $file = $this->_createEmbeddedFile($headers, $this->_createEncoder(),
            $this->_createCache()
        );
    }

    public function test_default_disposition_is_inline()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addParameterizedHeader')
            ->once()
            ->with('Content-Disposition', 'inline');
        $headers->shouldReceive('addParameterizedHeader')
            ->zeroOrMoreTimes();

        $file = $this->_createEmbeddedFile($headers, $this->_createEncoder(),
            $this->_createCache()
        );
    }

    protected function _createAttachment($headers, $encoder, $cache, $mimeTypes = [])
    {
        return $this->_createEmbeddedFile($headers, $encoder, $cache, $mimeTypes);
    }

    private function _createEmbeddedFile($headers, $encoder, $cache)
    {
        return new Swift_Mime_EmbeddedFile($headers, $encoder, $cache, new Swift_Mime_Grammar);
    }
}
