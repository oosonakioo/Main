<?php

class Swift_Mime_ContentEncoder_QpContentEncoderAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    private $_samplesDir;

    private $_factory;

    protected function setUp()
    {
        $this->_samplesDir = realpath(__DIR__.'/../../../../_samples/charsets');
        $this->_factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory;
    }

    protected function tearDown()
    {
        Swift_Preferences::getInstance()->setQPDotEscape(false);
    }

    public function test_encoding_and_decoding_samples()
    {
        $sampleFp = opendir($this->_samplesDir);
        while (false !== $encodingDir = readdir($sampleFp)) {
            if (substr($encodingDir, 0, 1) == '.') {
                continue;
            }

            $encoding = $encodingDir;
            $charStream = new Swift_CharacterStream_NgCharacterStream(
                $this->_factory, $encoding);
            $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);

            $sampleDir = $this->_samplesDir.'/'.$encodingDir;

            if (is_dir($sampleDir)) {
                $fileFp = opendir($sampleDir);
                while (false !== $sampleFile = readdir($fileFp)) {
                    if (substr($sampleFile, 0, 1) == '.') {
                        continue;
                    }

                    $text = file_get_contents($sampleDir.'/'.$sampleFile);

                    $os = new Swift_ByteStream_ArrayByteStream;
                    $os->write($text);

                    $is = new Swift_ByteStream_ArrayByteStream;
                    $encoder->encodeByteStream($os, $is);

                    $encoded = '';
                    while (false !== $bytes = $is->read(8192)) {
                        $encoded .= $bytes;
                    }

                    $this->assertEquals(
                        quoted_printable_decode($encoded), $text,
                        '%s: Encoded string should decode back to original string for sample '.
                        $sampleDir.'/'.$sampleFile
                    );
                }
                closedir($fileFp);
            }
        }
        closedir($sampleFp);
    }

    public function test_encoding_and_decoding_samples_from_di_configured_instance()
    {
        $sampleFp = opendir($this->_samplesDir);
        while (false !== $encodingDir = readdir($sampleFp)) {
            if (substr($encodingDir, 0, 1) == '.') {
                continue;
            }

            $encoding = $encodingDir;
            $encoder = $this->_createEncoderFromContainer();

            $sampleDir = $this->_samplesDir.'/'.$encodingDir;

            if (is_dir($sampleDir)) {
                $fileFp = opendir($sampleDir);
                while (false !== $sampleFile = readdir($fileFp)) {
                    if (substr($sampleFile, 0, 1) == '.') {
                        continue;
                    }

                    $text = file_get_contents($sampleDir.'/'.$sampleFile);

                    $os = new Swift_ByteStream_ArrayByteStream;
                    $os->write($text);

                    $is = new Swift_ByteStream_ArrayByteStream;
                    $encoder->encodeByteStream($os, $is);

                    $encoded = '';
                    while (false !== $bytes = $is->read(8192)) {
                        $encoded .= $bytes;
                    }

                    $this->assertEquals(
                        str_replace("\r\n", "\n", quoted_printable_decode($encoded)), str_replace("\r\n", "\n", $text),
                        '%s: Encoded string should decode back to original string for sample '.
                        $sampleDir.'/'.$sampleFile
                    );
                }
                closedir($fileFp);
            }
        }
        closedir($sampleFp);
    }

    public function test_encoding_lf_text_with_di_configured_instance()
    {
        $encoder = $this->_createEncoderFromContainer();
        $this->assertEquals("a\r\nb\r\nc", $encoder->encodeString("a\nb\nc"));
    }

    public function test_encoding_cr_text_with_di_configured_instance()
    {
        $encoder = $this->_createEncoderFromContainer();
        $this->assertEquals("a\r\nb\r\nc", $encoder->encodeString("a\rb\rc"));
    }

    public function test_encoding_lfcr_text_with_di_configured_instance()
    {
        $encoder = $this->_createEncoderFromContainer();
        $this->assertEquals("a\r\n\r\nb\r\n\r\nc", $encoder->encodeString("a\n\rb\n\rc"));
    }

    public function test_encoding_crlf_text_with_di_configured_instance()
    {
        $encoder = $this->_createEncoderFromContainer();
        $this->assertEquals("a\r\nb\r\nc", $encoder->encodeString("a\r\nb\r\nc"));
    }

    public function test_encoding_dot_stuffing_with_di_configured_instance()
    {
        // Enable DotEscaping
        Swift_Preferences::getInstance()->setQPDotEscape(true);
        $encoder = $this->_createEncoderFromContainer();
        $this->assertEquals("a=2E\r\n=2E\r\n=2Eb\r\nc", $encoder->encodeString("a.\r\n.\r\n.b\r\nc"));
        // Return to default
        Swift_Preferences::getInstance()->setQPDotEscape(false);
        $encoder = $this->_createEncoderFromContainer();
        $this->assertEquals("a.\r\n.\r\n.b\r\nc", $encoder->encodeString("a.\r\n.\r\n.b\r\nc"));
    }

    public function test_dot_stuffing_encoding_and_decoding_samples_from_di_configured_instance()
    {
        // Enable DotEscaping
        Swift_Preferences::getInstance()->setQPDotEscape(true);
        $this->testEncodingAndDecodingSamplesFromDiConfiguredInstance();
    }

    private function _createEncoderFromContainer()
    {
        return Swift_DependencyContainer::getInstance()
            ->lookup('mime.qpcontentencoder');
    }
}
