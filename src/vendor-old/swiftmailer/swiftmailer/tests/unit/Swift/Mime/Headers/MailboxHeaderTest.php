<?php

class Swift_Mime_Headers_MailboxHeaderTest extends \SwiftMailerTestCase
{
    /* -- RFC 2822, 3.6.2 for all tests.
     */

    private $_charset = 'utf-8';

    public function test_type_is_mailbox_header()
    {
        $header = $this->_getHeader('To', $this->_getEncoder('Q', true));
        $this->assertEquals(Swift_Mime_Header::TYPE_MAILBOX, $header->getFieldType());
    }

    public function test_mailbox_is_set_for_address()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setAddresses('chris@swiftmailer.org');
        $this->assertEquals(['chris@swiftmailer.org'],
            $header->getNameAddressStrings()
        );
    }

    public function test_mailbox_is_rendered_for_name_address()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses(['chris@swiftmailer.org' => 'Chris Corbyn']);
        $this->assertEquals(
            ['Chris Corbyn <chris@swiftmailer.org>'], $header->getNameAddressStrings()
        );
    }

    public function test_address_can_be_returned_for_address()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setAddresses('chris@swiftmailer.org');
        $this->assertEquals(['chris@swiftmailer.org'], $header->getAddresses());
    }

    public function test_address_can_be_returned_for_name_address()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses(['chris@swiftmailer.org' => 'Chris Corbyn']);
        $this->assertEquals(['chris@swiftmailer.org'], $header->getAddresses());
    }

    public function test_quotes_in_name_are_quoted()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn, "DHE"',
        ]);
        $this->assertEquals(
            ['"Chris Corbyn, \"DHE\"" <chris@swiftmailer.org>'],
            $header->getNameAddressStrings()
        );
    }

    public function test_escape_chars_in_name_are_quoted()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn, \\escaped\\',
        ]);
        $this->assertEquals(
            ['"Chris Corbyn, \\\\escaped\\\\" <chris@swiftmailer.org>'],
            $header->getNameAddressStrings()
        );
    }

    public function test_get_mailboxes_returns_name_value_pairs()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn, DHE',
        ]);
        $this->assertEquals(
            ['chris@swiftmailer.org' => 'Chris Corbyn, DHE'], $header->getNameAddresses()
        );
    }

    public function test_multiple_addresses_can_be_set_and_fetched()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setAddresses([
            'chris@swiftmailer.org', 'mark@swiftmailer.org',
        ]);
        $this->assertEquals(
            ['chris@swiftmailer.org', 'mark@swiftmailer.org'],
            $header->getAddresses()
        );
    }

    public function test_multiple_addresses_as_mailboxes()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setAddresses([
            'chris@swiftmailer.org', 'mark@swiftmailer.org',
        ]);
        $this->assertEquals(
            ['chris@swiftmailer.org' => null, 'mark@swiftmailer.org' => null],
            $header->getNameAddresses()
        );
    }

    public function test_multiple_addresses_as_mailbox_strings()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setAddresses([
            'chris@swiftmailer.org', 'mark@swiftmailer.org',
        ]);
        $this->assertEquals(
            ['chris@swiftmailer.org', 'mark@swiftmailer.org'],
            $header->getNameAddressStrings()
        );
    }

    public function test_multiple_named_mailboxes_returns_multiple_addresses()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ]);
        $this->assertEquals(
            ['chris@swiftmailer.org', 'mark@swiftmailer.org'],
            $header->getAddresses()
        );
    }

    public function test_multiple_named_mailboxes_returns_multiple_mailboxes()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ]);
        $this->assertEquals([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ],
            $header->getNameAddresses()
        );
    }

    public function test_multiple_mailboxes_produces_multiple_mailbox_strings()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ]);
        $this->assertEquals([
            'Chris Corbyn <chris@swiftmailer.org>',
            'Mark Corbyn <mark@swiftmailer.org>',
        ],
            $header->getNameAddressStrings()
        );
    }

    public function test_set_addresses_overwrites_any_mailboxes()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ]);
        $this->assertEquals(
            ['chris@swiftmailer.org' => 'Chris Corbyn',
                'mark@swiftmailer.org' => 'Mark Corbyn', ],
            $header->getNameAddresses()
        );
        $this->assertEquals(
            ['chris@swiftmailer.org', 'mark@swiftmailer.org'],
            $header->getAddresses()
        );

        $header->setAddresses(['chris@swiftmailer.org', 'mark@swiftmailer.org']);

        $this->assertEquals(
            ['chris@swiftmailer.org' => null, 'mark@swiftmailer.org' => null],
            $header->getNameAddresses()
        );
        $this->assertEquals(
            ['chris@swiftmailer.org', 'mark@swiftmailer.org'],
            $header->getAddresses()
        );
    }

    public function test_name_is_encoded_if_non_ascii()
    {
        $name = 'C'.pack('C', 0x8F).'rbyn';

        $encoder = $this->_getEncoder('Q');
        $encoder->shouldReceive('encodeString')
            ->once()
            ->with($name, \Mockery::any(), \Mockery::any(), \Mockery::any())
            ->andReturn('C=8Frbyn');

        $header = $this->_getHeader('From', $encoder);
        $header->setNameAddresses(['chris@swiftmailer.org' => 'Chris '.$name]);

        $addresses = $header->getNameAddressStrings();
        $this->assertEquals(
            'Chris =?'.$this->_charset.'?Q?C=8Frbyn?= <chris@swiftmailer.org>',
            array_shift($addresses)
        );
    }

    public function test_encoding_line_length_calculations()
    {
        /* -- RFC 2047, 2.
        An 'encoded-word' may not be more than 75 characters long, including
        'charset', 'encoding', 'encoded-text', and delimiters.
        */

        $name = 'C'.pack('C', 0x8F).'rbyn';

        $encoder = $this->_getEncoder('Q');
        $encoder->shouldReceive('encodeString')
            ->once()
            ->with($name, \Mockery::any(), \Mockery::any(), \Mockery::any())
            ->andReturn('C=8Frbyn');

        $header = $this->_getHeader('From', $encoder);
        $header->setNameAddresses(['chris@swiftmailer.org' => 'Chris '.$name]);

        $header->getNameAddressStrings();
    }

    public function test_get_value_returns_mailbox_string_value()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
        ]);
        $this->assertEquals(
            'Chris Corbyn <chris@swiftmailer.org>', $header->getFieldBody()
        );
    }

    public function test_get_value_returns_mailbox_string_value_for_multiple_mailboxes()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ]);
        $this->assertEquals(
            'Chris Corbyn <chris@swiftmailer.org>, Mark Corbyn <mark@swiftmailer.org>',
            $header->getFieldBody()
        );
    }

    public function test_remove_addresses_with_single_value()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ]);
        $header->removeAddresses('chris@swiftmailer.org');
        $this->assertEquals(['mark@swiftmailer.org'],
            $header->getAddresses()
        );
    }

    public function test_remove_addresses_with_list()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ]);
        $header->removeAddresses(
            ['chris@swiftmailer.org', 'mark@swiftmailer.org']
        );
        $this->assertEquals([], $header->getAddresses());
    }

    public function test_set_body_model()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setFieldBodyModel('chris@swiftmailer.org');
        $this->assertEquals(['chris@swiftmailer.org' => null], $header->getNameAddresses());
    }

    public function test_get_body_model()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setAddresses(['chris@swiftmailer.org']);
        $this->assertEquals(['chris@swiftmailer.org' => null], $header->getFieldBodyModel());
    }

    public function test_to_string()
    {
        $header = $this->_getHeader('From', $this->_getEncoder('Q', true));
        $header->setNameAddresses([
            'chris@swiftmailer.org' => 'Chris Corbyn',
            'mark@swiftmailer.org' => 'Mark Corbyn',
        ]);
        $this->assertEquals(
            'From: Chris Corbyn <chris@swiftmailer.org>, '.
            'Mark Corbyn <mark@swiftmailer.org>'."\r\n",
            $header->toString()
        );
    }

    private function _getHeader($name, $encoder)
    {
        $header = new Swift_Mime_Headers_MailboxHeader($name, $encoder, new Swift_Mime_Grammar);
        $header->setCharset($this->_charset);

        return $header;
    }

    private function _getEncoder($type, $stub = false)
    {
        $encoder = $this->getMockery('Swift_Mime_HeaderEncoder')->shouldIgnoreMissing();
        $encoder->shouldReceive('getName')
            ->zeroOrMoreTimes()
            ->andReturn($type);

        return $encoder;
    }
}
