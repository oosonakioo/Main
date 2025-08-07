<?php

class Swift_CharacterReaderFactory_SimpleCharacterReaderFactoryAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    private $_factory;

    private $_prefix = 'Swift_CharacterReader_';

    protected function setUp()
    {
        $this->_factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory;
    }

    public function test_creating_utf8_reader()
    {
        foreach (['utf8', 'utf-8', 'UTF-8', 'UTF8'] as $utf8) {
            $reader = $this->_factory->getReaderFor($utf8);
            $this->assertInstanceOf($this->_prefix.'Utf8Reader', $reader);
        }
    }

    public function test_creating_iso8859_x_readers()
    {
        $charsets = [];
        foreach (range(1, 16) as $number) {
            foreach (['iso', 'iec'] as $body) {
                $charsets[] = $body.'-8859-'.$number;
                $charsets[] = $body.'8859-'.$number;
                $charsets[] = strtoupper($body).'-8859-'.$number;
                $charsets[] = strtoupper($body).'8859-'.$number;
            }
        }

        foreach ($charsets as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_windows125_x_readers()
    {
        $charsets = [];
        foreach (range(0, 8) as $number) {
            $charsets[] = 'windows-125'.$number;
            $charsets[] = 'windows125'.$number;
            $charsets[] = 'WINDOWS-125'.$number;
            $charsets[] = 'WINDOWS125'.$number;
        }

        foreach ($charsets as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_code_page_readers()
    {
        $charsets = [];
        foreach (range(0, 8) as $number) {
            $charsets[] = 'cp-125'.$number;
            $charsets[] = 'cp125'.$number;
            $charsets[] = 'CP-125'.$number;
            $charsets[] = 'CP125'.$number;
        }

        foreach ([437, 737, 850, 855, 857, 858, 860,
            861, 863, 865, 866, 869, ] as $number) {
            $charsets[] = 'cp-'.$number;
            $charsets[] = 'cp'.$number;
            $charsets[] = 'CP-'.$number;
            $charsets[] = 'CP'.$number;
        }

        foreach ($charsets as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_ansi_reader()
    {
        foreach (['ansi', 'ANSI'] as $ansi) {
            $reader = $this->_factory->getReaderFor($ansi);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_macintosh_reader()
    {
        foreach (['macintosh', 'MACINTOSH'] as $mac) {
            $reader = $this->_factory->getReaderFor($mac);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_koi_readers()
    {
        $charsets = [];
        foreach (['7', '8-r', '8-u', '8u', '8r'] as $end) {
            $charsets[] = 'koi-'.$end;
            $charsets[] = 'koi'.$end;
            $charsets[] = 'KOI-'.$end;
            $charsets[] = 'KOI'.$end;
        }

        foreach ($charsets as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_iscii_readers()
    {
        foreach (['iscii', 'ISCII', 'viscii', 'VISCII'] as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_mik_reader()
    {
        foreach (['mik', 'MIK'] as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_cork_reader()
    {
        foreach (['cork', 'CORK', 't1', 'T1'] as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function test_creating_ucs2_reader()
    {
        foreach (['ucs-2', 'UCS-2', 'ucs2', 'UCS2'] as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(2, $reader->getInitialByteSize());
        }
    }

    public function test_creating_utf16_reader()
    {
        foreach (['utf-16', 'UTF-16', 'utf16', 'UTF16'] as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(2, $reader->getInitialByteSize());
        }
    }

    public function test_creating_ucs4_reader()
    {
        foreach (['ucs-4', 'UCS-4', 'ucs4', 'UCS4'] as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(4, $reader->getInitialByteSize());
        }
    }

    public function test_creating_utf32_reader()
    {
        foreach (['utf-32', 'UTF-32', 'utf32', 'UTF32'] as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceOf($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(4, $reader->getInitialByteSize());
        }
    }
}
