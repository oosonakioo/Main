<?php

class Swift_Mime_Headers_IdentificationHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_type_is_id_header()
    {
        $header = $this->_getHeader('Message-ID');
        $this->assertEquals(Swift_Mime_Header::TYPE_ID, $header->getFieldType());
    }

    public function test_value_matches_msg_id_spec()
    {
        /* -- RFC 2822, 3.6.4.
     message-id      =       "Message-ID:" msg-id CRLF

     in-reply-to     =       "In-Reply-To:" 1*msg-id CRLF

     references      =       "References:" 1*msg-id CRLF

     msg-id          =       [CFWS] "<" id-left "@" id-right ">" [CFWS]

     id-left         =       dot-atom-text / no-fold-quote / obs-id-left

     id-right        =       dot-atom-text / no-fold-literal / obs-id-right

     no-fold-quote   =       DQUOTE *(qtext / quoted-pair) DQUOTE

     no-fold-literal =       "[" *(dtext / quoted-pair) "]"
     */

        $header = $this->_getHeader('Message-ID');
        $header->setId('id-left@id-right');
        $this->assertEquals('<id-left@id-right>', $header->getFieldBody());
    }

    public function test_id_can_be_retrieved_verbatim()
    {
        $header = $this->_getHeader('Message-ID');
        $header->setId('id-left@id-right');
        $this->assertEquals('id-left@id-right', $header->getId());
    }

    public function test_multiple_ids_can_be_set()
    {
        $header = $this->_getHeader('References');
        $header->setIds(['a@b', 'x@y']);
        $this->assertEquals(['a@b', 'x@y'], $header->getIds());
    }

    public function test_setting_multiple_ids_produces_a_list_value()
    {
        /* -- RFC 2822, 3.6.4.
     The "References:" and "In-Reply-To:" field each contain one or more
     unique message identifiers, optionally separated by CFWS.

     .. SNIP ..

     in-reply-to     =       "In-Reply-To:" 1*msg-id CRLF

     references      =       "References:" 1*msg-id CRLF
     */

        $header = $this->_getHeader('References');
        $header->setIds(['a@b', 'x@y']);
        $this->assertEquals('<a@b> <x@y>', $header->getFieldBody());
    }

    public function test_id_left_can_be_quoted()
    {
        /* -- RFC 2822, 3.6.4.
     id-left         =       dot-atom-text / no-fold-quote / obs-id-left
     */

        $header = $this->_getHeader('References');
        $header->setId('"ab"@c');
        $this->assertEquals('"ab"@c', $header->getId());
        $this->assertEquals('<"ab"@c>', $header->getFieldBody());
    }

    public function test_id_left_can_contain_angles_as_quoted_pairs()
    {
        /* -- RFC 2822, 3.6.4.
     no-fold-quote   =       DQUOTE *(qtext / quoted-pair) DQUOTE
     */

        $header = $this->_getHeader('References');
        $header->setId('"a\\<\\>b"@c');
        $this->assertEquals('"a\\<\\>b"@c', $header->getId());
        $this->assertEquals('<"a\\<\\>b"@c>', $header->getFieldBody());
    }

    public function test_id_left_can_be_dot_atom()
    {
        $header = $this->_getHeader('References');
        $header->setId('a.b+&%$.c@d');
        $this->assertEquals('a.b+&%$.c@d', $header->getId());
        $this->assertEquals('<a.b+&%$.c@d>', $header->getFieldBody());
    }

    public function test_invalid_id_left_throws_exception()
    {
        try {
            $header = $this->_getHeader('References');
            $header->setId('a b c@d');
            $this->fail(
                'Exception should be thrown since "a b c" is not valid id-left.'
            );
        } catch (Exception $e) {
        }
    }

    public function test_id_right_can_be_dot_atom()
    {
        /* -- RFC 2822, 3.6.4.
     id-right        =       dot-atom-text / no-fold-literal / obs-id-right
     */

        $header = $this->_getHeader('References');
        $header->setId('a@b.c+&%$.d');
        $this->assertEquals('a@b.c+&%$.d', $header->getId());
        $this->assertEquals('<a@b.c+&%$.d>', $header->getFieldBody());
    }

    public function test_id_right_can_be_literal()
    {
        /* -- RFC 2822, 3.6.4.
     no-fold-literal =       "[" *(dtext / quoted-pair) "]"
     */

        $header = $this->_getHeader('References');
        $header->setId('a@[1.2.3.4]');
        $this->assertEquals('a@[1.2.3.4]', $header->getId());
        $this->assertEquals('<a@[1.2.3.4]>', $header->getFieldBody());
    }

    public function test_invalid_id_right_throws_exception()
    {
        try {
            $header = $this->_getHeader('References');
            $header->setId('a@b c d');
            $this->fail(
                'Exception should be thrown since "b c d" is not valid id-right.'
            );
        } catch (Exception $e) {
        }
    }

    public function test_missing_at_sign_throws_exception()
    {
        /* -- RFC 2822, 3.6.4.
     msg-id          =       [CFWS] "<" id-left "@" id-right ">" [CFWS]
     */

        try {
            $header = $this->_getHeader('References');
            $header->setId('abc');
            $this->fail(
                'Exception should be thrown since "abc" is does not contain @.'
            );
        } catch (Exception $e) {
        }
    }

    public function test_set_body_model()
    {
        $header = $this->_getHeader('Message-ID');
        $header->setFieldBodyModel('a@b');
        $this->assertEquals(['a@b'], $header->getIds());
    }

    public function test_get_body_model()
    {
        $header = $this->_getHeader('Message-ID');
        $header->setId('a@b');
        $this->assertEquals(['a@b'], $header->getFieldBodyModel());
    }

    public function test_string_value()
    {
        $header = $this->_getHeader('References');
        $header->setIds(['a@b', 'x@y']);
        $this->assertEquals('References: <a@b> <x@y>'."\r\n", $header->toString());
    }

    private function _getHeader($name)
    {
        return new Swift_Mime_Headers_IdentificationHeader($name, new Swift_Mime_Grammar);
    }
}
