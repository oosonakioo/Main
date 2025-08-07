<?php

class Swift_Mime_SimpleHeaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_factory;

    protected function setUp()
    {
        $this->_factory = $this->_createFactory();
    }

    public function test_mailbox_header_is_correct_type()
    {
        $header = $this->_factory->createMailboxHeader('X-Foo');
        $this->assertInstanceOf('Swift_Mime_Headers_MailboxHeader', $header);
    }

    public function test_mailbox_header_has_correct_name()
    {
        $header = $this->_factory->createMailboxHeader('X-Foo');
        $this->assertEquals('X-Foo', $header->getFieldName());
    }

    public function test_mailbox_header_has_correct_model()
    {
        $header = $this->_factory->createMailboxHeader('X-Foo',
            ['foo@bar' => 'FooBar']
        );
        $this->assertEquals(['foo@bar' => 'FooBar'], $header->getFieldBodyModel());
    }

    public function test_date_header_has_correct_type()
    {
        $header = $this->_factory->createDateHeader('X-Date');
        $this->assertInstanceOf('Swift_Mime_Headers_DateHeader', $header);
    }

    public function test_date_header_has_correct_name()
    {
        $header = $this->_factory->createDateHeader('X-Date');
        $this->assertEquals('X-Date', $header->getFieldName());
    }

    public function test_date_header_has_correct_model()
    {
        $header = $this->_factory->createDateHeader('X-Date', 123);
        $this->assertEquals(123, $header->getFieldBodyModel());
    }

    public function test_text_header_has_correct_type()
    {
        $header = $this->_factory->createTextHeader('X-Foo');
        $this->assertInstanceOf('Swift_Mime_Headers_UnstructuredHeader', $header);
    }

    public function test_text_header_has_correct_name()
    {
        $header = $this->_factory->createTextHeader('X-Foo');
        $this->assertEquals('X-Foo', $header->getFieldName());
    }

    public function test_text_header_has_correct_model()
    {
        $header = $this->_factory->createTextHeader('X-Foo', 'bar');
        $this->assertEquals('bar', $header->getFieldBodyModel());
    }

    public function test_parameterized_header_has_correct_type()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo');
        $this->assertInstanceOf('Swift_Mime_Headers_ParameterizedHeader', $header);
    }

    public function test_parameterized_header_has_correct_name()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo');
        $this->assertEquals('X-Foo', $header->getFieldName());
    }

    public function test_parameterized_header_has_correct_model()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo', 'bar');
        $this->assertEquals('bar', $header->getFieldBodyModel());
    }

    public function test_parameterized_header_has_correct_params()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo', 'bar',
            ['zip' => 'button']
        );
        $this->assertEquals(['zip' => 'button'], $header->getParameters());
    }

    public function test_id_header_has_correct_type()
    {
        $header = $this->_factory->createIdHeader('X-ID');
        $this->assertInstanceOf('Swift_Mime_Headers_IdentificationHeader', $header);
    }

    public function test_id_header_has_correct_name()
    {
        $header = $this->_factory->createIdHeader('X-ID');
        $this->assertEquals('X-ID', $header->getFieldName());
    }

    public function test_id_header_has_correct_model()
    {
        $header = $this->_factory->createIdHeader('X-ID', 'xyz@abc');
        $this->assertEquals(['xyz@abc'], $header->getFieldBodyModel());
    }

    public function test_path_header_has_correct_type()
    {
        $header = $this->_factory->createPathHeader('X-Path');
        $this->assertInstanceOf('Swift_Mime_Headers_PathHeader', $header);
    }

    public function test_path_header_has_correct_name()
    {
        $header = $this->_factory->createPathHeader('X-Path');
        $this->assertEquals('X-Path', $header->getFieldName());
    }

    public function test_path_header_has_correct_model()
    {
        $header = $this->_factory->createPathHeader('X-Path', 'foo@bar');
        $this->assertEquals('foo@bar', $header->getFieldBodyModel());
    }

    public function test_charset_change_notification_notifies_encoders()
    {
        $encoder = $this->_createHeaderEncoder();
        $encoder->expects($this->once())
            ->method('charsetChanged')
            ->with('utf-8');
        $paramEncoder = $this->_createParamEncoder();
        $paramEncoder->expects($this->once())
            ->method('charsetChanged')
            ->with('utf-8');

        $factory = $this->_createFactory($encoder, $paramEncoder);

        $factory->charsetChanged('utf-8');
    }

    private function _createFactory($encoder = null, $paramEncoder = null)
    {
        return new Swift_Mime_SimpleHeaderFactory(
            $encoder
                ? $encoder : $this->_createHeaderEncoder(),
            $paramEncoder
                ? $paramEncoder : $this->_createParamEncoder(),
            new Swift_Mime_Grammar
        );
    }

    private function _createHeaderEncoder()
    {
        return $this->getMockBuilder('Swift_Mime_HeaderEncoder')->getMock();
    }

    private function _createParamEncoder()
    {
        return $this->getMockBuilder('Swift_Encoder')->getMock();
    }
}
