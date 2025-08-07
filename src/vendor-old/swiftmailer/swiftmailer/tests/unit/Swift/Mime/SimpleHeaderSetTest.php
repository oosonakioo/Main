<?php

class Swift_Mime_SimpleHeaderSetTest extends \PHPUnit_Framework_TestCase
{
    public function test_add_mailbox_header_delegates_to_factory()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createMailboxHeader')
            ->with('From', ['person@domain' => 'Person'])
            ->will($this->returnValue($this->_createHeader('From')));

        $set = $this->_createSet($factory);
        $set->addMailboxHeader('From', ['person@domain' => 'Person']);
    }

    public function test_add_date_header_delegates_to_factory()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createDateHeader')
            ->with('Date', 1234)
            ->will($this->returnValue($this->_createHeader('Date')));

        $set = $this->_createSet($factory);
        $set->addDateHeader('Date', 1234);
    }

    public function test_add_text_header_delegates_to_factory()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createTextHeader')
            ->with('Subject', 'some text')
            ->will($this->returnValue($this->_createHeader('Subject')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Subject', 'some text');
    }

    public function test_add_parameterized_header_delegates_to_factory()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createParameterizedHeader')
            ->with('Content-Type', 'text/plain', ['charset' => 'utf-8'])
            ->will($this->returnValue($this->_createHeader('Content-Type')));

        $set = $this->_createSet($factory);
        $set->addParameterizedHeader('Content-Type', 'text/plain',
            ['charset' => 'utf-8']
        );
    }

    public function test_add_id_header_delegates_to_factory()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
    }

    public function test_add_path_header_delegates_to_factory()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createPathHeader')
            ->with('Return-Path', 'some@path')
            ->will($this->returnValue($this->_createHeader('Return-Path')));

        $set = $this->_createSet($factory);
        $set->addPathHeader('Return-Path', 'some@path');
    }

    public function test_has_returns_false_when_no_headers()
    {
        $set = $this->_createSet($this->_createFactory());
        $this->assertFalse($set->has('Some-Header'));
    }

    public function test_added_mailbox_header_is_seen_by_has()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createMailboxHeader')
            ->with('From', ['person@domain' => 'Person'])
            ->will($this->returnValue($this->_createHeader('From')));

        $set = $this->_createSet($factory);
        $set->addMailboxHeader('From', ['person@domain' => 'Person']);
        $this->assertTrue($set->has('From'));
    }

    public function test_added_date_header_is_seen_by_has()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createDateHeader')
            ->with('Date', 1234)
            ->will($this->returnValue($this->_createHeader('Date')));

        $set = $this->_createSet($factory);
        $set->addDateHeader('Date', 1234);
        $this->assertTrue($set->has('Date'));
    }

    public function test_added_text_header_is_seen_by_has()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createTextHeader')
            ->with('Subject', 'some text')
            ->will($this->returnValue($this->_createHeader('Subject')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Subject', 'some text');
        $this->assertTrue($set->has('Subject'));
    }

    public function test_added_parameterized_header_is_seen_by_has()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createParameterizedHeader')
            ->with('Content-Type', 'text/plain', ['charset' => 'utf-8'])
            ->will($this->returnValue($this->_createHeader('Content-Type')));

        $set = $this->_createSet($factory);
        $set->addParameterizedHeader('Content-Type', 'text/plain',
            ['charset' => 'utf-8']
        );
        $this->assertTrue($set->has('Content-Type'));
    }

    public function test_added_id_header_is_seen_by_has()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $this->assertTrue($set->has('Message-ID'));
    }

    public function test_added_path_header_is_seen_by_has()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createPathHeader')
            ->with('Return-Path', 'some@path')
            ->will($this->returnValue($this->_createHeader('Return-Path')));

        $set = $this->_createSet($factory);
        $set->addPathHeader('Return-Path', 'some@path');
        $this->assertTrue($set->has('Return-Path'));
    }

    public function test_newly_set_header_is_seen_by_has()
    {
        $factory = $this->_createFactory();
        $header = $this->_createHeader('X-Foo', 'bar');
        $set = $this->_createSet($factory);
        $set->set($header);
        $this->assertTrue($set->has('X-Foo'));
    }

    public function test_has_can_accept_offset()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $this->assertTrue($set->has('Message-ID', 0));
    }

    public function test_has_with_illegal_offset_returns_false()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $this->assertFalse($set->has('Message-ID', 1));
    }

    public function test_has_can_distinguish_multiple_headers()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($this->_createHeader('Message-ID')));
        $factory->expects($this->at(1))
            ->method('createIdHeader')
            ->with('Message-ID', 'other@id')
            ->will($this->returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $this->assertTrue($set->has('Message-ID', 1));
    }

    public function test_get_with_unspecified_offset()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $this->assertSame($header, $set->get('Message-ID'));
    }

    public function test_get_with_speicied_offset()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $header2 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header0));
        $factory->expects($this->at(1))
            ->method('createIdHeader')
            ->with('Message-ID', 'other@id')
            ->will($this->returnValue($header1));
        $factory->expects($this->at(2))
            ->method('createIdHeader')
            ->with('Message-ID', 'more@id')
            ->will($this->returnValue($header2));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->addIdHeader('Message-ID', 'more@id');
        $this->assertSame($header1, $set->get('Message-ID', 1));
    }

    public function test_get_returns_null_if_header_not_set()
    {
        $set = $this->_createSet($this->_createFactory());
        $this->assertNull($set->get('Message-ID', 99));
    }

    public function test_get_all_returns_all_headers_matching_name()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $header2 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header0));
        $factory->expects($this->at(1))
            ->method('createIdHeader')
            ->with('Message-ID', 'other@id')
            ->will($this->returnValue($header1));
        $factory->expects($this->at(2))
            ->method('createIdHeader')
            ->with('Message-ID', 'more@id')
            ->will($this->returnValue($header2));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->addIdHeader('Message-ID', 'more@id');

        $this->assertEquals([$header0, $header1, $header2],
            $set->getAll('Message-ID')
        );
    }

    public function test_get_all_returns_all_headers_if_no_arguments()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Subject');
        $header2 = $this->_createHeader('To');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header0));
        $factory->expects($this->at(1))
            ->method('createIdHeader')
            ->with('Subject', 'thing')
            ->will($this->returnValue($header1));
        $factory->expects($this->at(2))
            ->method('createIdHeader')
            ->with('To', 'person@example.org')
            ->will($this->returnValue($header2));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Subject', 'thing');
        $set->addIdHeader('To', 'person@example.org');

        $this->assertEquals([$header0, $header1, $header2],
            $set->getAll()
        );
    }

    public function test_get_all_returns_empty_array_if_none_set()
    {
        $set = $this->_createSet($this->_createFactory());
        $this->assertEquals([], $set->getAll('Received'));
    }

    public function test_remove_with_unspecified_offset()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->remove('Message-ID');
        $this->assertFalse($set->has('Message-ID'));
    }

    public function test_remove_with_specified_index_removes_header()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header0));
        $factory->expects($this->at(1))
            ->method('createIdHeader')
            ->with('Message-ID', 'other@id')
            ->will($this->returnValue($header1));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->remove('Message-ID', 0);
        $this->assertFalse($set->has('Message-ID', 0));
        $this->assertTrue($set->has('Message-ID', 1));
        $this->assertTrue($set->has('Message-ID'));
        $set->remove('Message-ID', 1);
        $this->assertFalse($set->has('Message-ID', 1));
        $this->assertFalse($set->has('Message-ID'));
    }

    public function test_remove_with_specified_index_leaves_other_headers()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header0));
        $factory->expects($this->at(1))
            ->method('createIdHeader')
            ->with('Message-ID', 'other@id')
            ->will($this->returnValue($header1));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->remove('Message-ID', 1);
        $this->assertTrue($set->has('Message-ID', 0));
    }

    public function test_remove_with_invalid_offset_does_nothing()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->remove('Message-ID', 50);
        $this->assertTrue($set->has('Message-ID'));
    }

    public function test_remove_all_removes_all_headers_with_name()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header0));
        $factory->expects($this->at(1))
            ->method('createIdHeader')
            ->with('Message-ID', 'other@id')
            ->will($this->returnValue($header1));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->removeAll('Message-ID');
        $this->assertFalse($set->has('Message-ID', 0));
        $this->assertFalse($set->has('Message-ID', 1));
    }

    public function test_has_is_not_case_sensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $this->assertTrue($set->has('message-id'));
    }

    public function test_get_is_not_case_sensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $this->assertSame($header, $set->get('message-id'));
    }

    public function test_get_all_is_not_case_sensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $this->assertEquals([$header], $set->getAll('message-id'));
    }

    public function test_remove_is_not_case_sensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->remove('message-id');
        $this->assertFalse($set->has('Message-ID'));
    }

    public function test_remove_all_is_not_case_sensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createIdHeader')
            ->with('Message-ID', 'some@id')
            ->will($this->returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->removeAll('message-id');
        $this->assertFalse($set->has('Message-ID'));
    }

    public function test_new_instance()
    {
        $set = $this->_createSet($this->_createFactory());
        $instance = $set->newInstance();
        $this->assertInstanceOf('Swift_Mime_HeaderSet', $instance);
    }

    public function test_to_string_joins_headers_together()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createTextHeader')
            ->with('Foo', 'bar')
            ->will($this->returnValue($this->_createHeader('Foo', 'bar')));
        $factory->expects($this->at(1))
            ->method('createTextHeader')
            ->with('Zip', 'buttons')
            ->will($this->returnValue($this->_createHeader('Zip', 'buttons')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Foo', 'bar');
        $set->addTextHeader('Zip', 'buttons');
        $this->assertEquals(
            "Foo: bar\r\n".
            "Zip: buttons\r\n",
            $set->toString()
        );
    }

    public function test_headers_without_bodies_are_not_displayed()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createTextHeader')
            ->with('Foo', 'bar')
            ->will($this->returnValue($this->_createHeader('Foo', 'bar')));
        $factory->expects($this->at(1))
            ->method('createTextHeader')
            ->with('Zip', '')
            ->will($this->returnValue($this->_createHeader('Zip', '')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Foo', 'bar');
        $set->addTextHeader('Zip', '');
        $this->assertEquals(
            "Foo: bar\r\n",
            $set->toString()
        );
    }

    public function test_headers_without_bodies_can_be_forced_to_display()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createTextHeader')
            ->with('Foo', '')
            ->will($this->returnValue($this->_createHeader('Foo', '')));
        $factory->expects($this->at(1))
            ->method('createTextHeader')
            ->with('Zip', '')
            ->will($this->returnValue($this->_createHeader('Zip', '')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Foo', '');
        $set->addTextHeader('Zip', '');
        $set->setAlwaysDisplayed(['Foo', 'Zip']);
        $this->assertEquals(
            "Foo: \r\n".
            "Zip: \r\n",
            $set->toString()
        );
    }

    public function test_header_sequences_can_be_specified()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createTextHeader')
            ->with('Third', 'three')
            ->will($this->returnValue($this->_createHeader('Third', 'three')));
        $factory->expects($this->at(1))
            ->method('createTextHeader')
            ->with('First', 'one')
            ->will($this->returnValue($this->_createHeader('First', 'one')));
        $factory->expects($this->at(2))
            ->method('createTextHeader')
            ->with('Second', 'two')
            ->will($this->returnValue($this->_createHeader('Second', 'two')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Third', 'three');
        $set->addTextHeader('First', 'one');
        $set->addTextHeader('Second', 'two');

        $set->defineOrdering(['First', 'Second', 'Third']);

        $this->assertEquals(
            "First: one\r\n".
            "Second: two\r\n".
            "Third: three\r\n",
            $set->toString()
        );
    }

    public function test_unsorted_headers_appear_at_end()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createTextHeader')
            ->with('Fourth', 'four')
            ->will($this->returnValue($this->_createHeader('Fourth', 'four')));
        $factory->expects($this->at(1))
            ->method('createTextHeader')
            ->with('Fifth', 'five')
            ->will($this->returnValue($this->_createHeader('Fifth', 'five')));
        $factory->expects($this->at(2))
            ->method('createTextHeader')
            ->with('Third', 'three')
            ->will($this->returnValue($this->_createHeader('Third', 'three')));
        $factory->expects($this->at(3))
            ->method('createTextHeader')
            ->with('First', 'one')
            ->will($this->returnValue($this->_createHeader('First', 'one')));
        $factory->expects($this->at(4))
            ->method('createTextHeader')
            ->with('Second', 'two')
            ->will($this->returnValue($this->_createHeader('Second', 'two')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Fourth', 'four');
        $set->addTextHeader('Fifth', 'five');
        $set->addTextHeader('Third', 'three');
        $set->addTextHeader('First', 'one');
        $set->addTextHeader('Second', 'two');

        $set->defineOrdering(['First', 'Second', 'Third']);

        $this->assertEquals(
            "First: one\r\n".
            "Second: two\r\n".
            "Third: three\r\n".
            "Fourth: four\r\n".
            "Fifth: five\r\n",
            $set->toString()
        );
    }

    public function test_setting_charset_notifies_already_existing_headers()
    {
        $subject = $this->_createHeader('Subject', 'some text');
        $xHeader = $this->_createHeader('X-Header', 'some text');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createTextHeader')
            ->with('Subject', 'some text')
            ->will($this->returnValue($subject));
        $factory->expects($this->at(1))
            ->method('createTextHeader')
            ->with('X-Header', 'some text')
            ->will($this->returnValue($xHeader));
        $subject->expects($this->once())
            ->method('setCharset')
            ->with('utf-8');
        $xHeader->expects($this->once())
            ->method('setCharset')
            ->with('utf-8');

        $set = $this->_createSet($factory);
        $set->addTextHeader('Subject', 'some text');
        $set->addTextHeader('X-Header', 'some text');

        $set->setCharset('utf-8');
    }

    public function test_charset_change_notifies_already_existing_headers()
    {
        $subject = $this->_createHeader('Subject', 'some text');
        $xHeader = $this->_createHeader('X-Header', 'some text');
        $factory = $this->_createFactory();
        $factory->expects($this->at(0))
            ->method('createTextHeader')
            ->with('Subject', 'some text')
            ->will($this->returnValue($subject));
        $factory->expects($this->at(1))
            ->method('createTextHeader')
            ->with('X-Header', 'some text')
            ->will($this->returnValue($xHeader));
        $subject->expects($this->once())
            ->method('setCharset')
            ->with('utf-8');
        $xHeader->expects($this->once())
            ->method('setCharset')
            ->with('utf-8');

        $set = $this->_createSet($factory);
        $set->addTextHeader('Subject', 'some text');
        $set->addTextHeader('X-Header', 'some text');

        $set->charsetChanged('utf-8');
    }

    public function test_charset_change_notifies_factory()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('charsetChanged')
            ->with('utf-8');

        $set = $this->_createSet($factory);

        $set->setCharset('utf-8');
    }

    private function _createSet($factory)
    {
        return new Swift_Mime_SimpleHeaderSet($factory);
    }

    private function _createFactory()
    {
        return $this->getMockBuilder('Swift_Mime_HeaderFactory')->getMock();
    }

    private function _createHeader($name, $body = '')
    {
        $header = $this->getMockBuilder('Swift_Mime_Header')->getMock();
        $header->expects($this->any())
            ->method('getFieldName')
            ->will($this->returnValue($name));
        $header->expects($this->any())
            ->method('toString')
            ->will($this->returnValue(sprintf("%s: %s\r\n", $name, $body)));
        $header->expects($this->any())
            ->method('getFieldBody')
            ->will($this->returnValue($body));

        return $header;
    }
}
