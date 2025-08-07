<?php

class Swift_Mime_EmbeddedFileAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    private $_contentEncoder;

    private $_cache;

    private $_grammar;

    private $_headers;

    protected function setUp()
    {
        $this->_cache = new Swift_KeyCache_ArrayKeyCache(
            new Swift_KeyCache_SimpleKeyCacheInputStream
        );
        $factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory;
        $this->_contentEncoder = new Swift_Mime_ContentEncoder_Base64ContentEncoder;

        $headerEncoder = new Swift_Mime_HeaderEncoder_QpHeaderEncoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
        );
        $paramEncoder = new Swift_Encoder_Rfc2231Encoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
        );
        $this->_grammar = new Swift_Mime_Grammar;
        $this->_headers = new Swift_Mime_SimpleHeaderSet(
            new Swift_Mime_SimpleHeaderFactory($headerEncoder, $paramEncoder, $this->_grammar)
        );
    }

    public function test_content_id_is_set_in_header()
    {
        $file = $this->_createEmbeddedFile();
        $file->setContentType('application/pdf');
        $file->setId('foo@bar');
        $this->assertEquals(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <foo@bar>'."\r\n".
            'Content-Disposition: inline'."\r\n",
            $file->toString()
        );
    }

    public function test_disposition_is_set_in_header()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setDisposition('attachment');
        $this->assertEquals(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: attachment'."\r\n",
            $file->toString()
        );
    }

    public function test_filename_is_set_in_header()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setFilename('foo.pdf');
        $this->assertEquals(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: inline; filename=foo.pdf'."\r\n",
            $file->toString()
        );
    }

    public function test_size_is_set_in_header()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setSize(12340);
        $this->assertEquals(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: inline; size=12340'."\r\n",
            $file->toString()
        );
    }

    public function test_multiple_parameters_in_header()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setFilename('foo.pdf');
        $file->setSize(12340);

        $this->assertEquals(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: inline; filename=foo.pdf; size=12340'."\r\n",
            $file->toString()
        );
    }

    public function test_end_to_end()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setFilename('foo.pdf');
        $file->setSize(12340);
        $file->setBody('abcd');
        $this->assertEquals(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: inline; filename=foo.pdf; size=12340'."\r\n".
            "\r\n".
            base64_encode('abcd'),
            $file->toString()
        );
    }

    protected function _createEmbeddedFile()
    {
        $entity = new Swift_Mime_EmbeddedFile(
            $this->_headers,
            $this->_contentEncoder,
            $this->_cache,
            $this->_grammar
        );

        return $entity;
    }
}
