<?php

class Swift_Mime_SimpleMessageTest extends Swift_Mime_MimePartTest
{
    public function test_nesting_level_is_subpart()
    {
        // Overridden
    }

    public function test_nesting_level_is_top()
    {
        $message = $this->_createMessage($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(
            Swift_Mime_MimeEntity::LEVEL_TOP, $message->getNestingLevel()
        );
    }

    public function test_date_is_returned_from_header()
    {
        $date = $this->_createHeader('Date', 123);
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Date' => $date]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(123, $message->getDate());
    }

    public function test_date_is_set_in_header()
    {
        $date = $this->_createHeader('Date', 123, [], false);
        $date->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(1234);
        $date->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Date' => $date]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setDate(1234);
    }

    public function test_date_header_is_created_if_none_present()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addDateHeader')
            ->once()
            ->with('Date', 1234);
        $headers->shouldReceive('addDateHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setDate(1234);
    }

    public function test_date_header_is_added_during_construction()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addDateHeader')
            ->once()
            ->with('Date', '/^[0-9]+$/D');

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
    }

    public function test_id_is_returned_from_header()
    {
        /* -- RFC 2045, 7.
        In constructing a high-level user agent, it may be desirable to allow
        one body to make reference to another.  Accordingly, bodies may be
        labelled using the "Content-ID" header field, which is syntactically
        identical to the "Message-ID" header field
        */

        $messageId = $this->_createHeader('Message-ID', 'a@b');
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Message-ID' => $messageId]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals('a@b', $message->getId());
    }

    public function test_id_is_set_in_header()
    {
        $messageId = $this->_createHeader('Message-ID', 'a@b', [], false);
        $messageId->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('x@y');
        $messageId->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Message-ID' => $messageId]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setId('x@y');
    }

    public function test_id_is_auto_generated()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addIdHeader')
            ->once()
            ->with('Message-ID', '/^.*?@.*?$/D');

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
    }

    public function test_subject_is_returned_from_header()
    {
        /* -- RFC 2822, 3.6.5.
     */

        $subject = $this->_createHeader('Subject', 'example subject');
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Subject' => $subject]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals('example subject', $message->getSubject());
    }

    public function test_subject_is_set_in_header()
    {
        $subject = $this->_createHeader('Subject', '', [], false);
        $subject->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('foo');

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Subject' => $subject]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setSubject('foo');
    }

    public function test_subject_header_is_created_if_not_present()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addTextHeader')
            ->once()
            ->with('Subject', 'example subject');
        $headers->shouldReceive('addTextHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setSubject('example subject');
    }

    public function test_return_path_is_returned_from_header()
    {
        /* -- RFC 2822, 3.6.7.
     */

        $path = $this->_createHeader('Return-Path', 'bounces@domain');
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Return-Path' => $path]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals('bounces@domain', $message->getReturnPath());
    }

    public function test_return_path_is_set_in_header()
    {
        $path = $this->_createHeader('Return-Path', '', [], false);
        $path->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('bounces@domain');

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Return-Path' => $path]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setReturnPath('bounces@domain');
    }

    public function test_return_path_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addPathHeader')
            ->once()
            ->with('Return-Path', 'bounces@domain');

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setReturnPath('bounces@domain');
    }

    public function test_sender_is_returned_from_header()
    {
        /* -- RFC 2822, 3.6.2.
     */

        $sender = $this->_createHeader('Sender', ['sender@domain' => 'Name']);
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Sender' => $sender]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(['sender@domain' => 'Name'], $message->getSender());
    }

    public function test_sender_is_set_in_header()
    {
        $sender = $this->_createHeader('Sender', ['sender@domain' => 'Name'],
            [], false
        );
        $sender->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Sender' => $sender]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setSender(['other@domain' => 'Other']);
    }

    public function test_sender_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Sender', (array) 'sender@domain');
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setSender('sender@domain');
    }

    public function test_name_can_be_used_in_sender_header()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Sender', ['sender@domain' => 'Name']);
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setSender('sender@domain', 'Name');
    }

    public function test_from_is_returned_from_header()
    {
        /* -- RFC 2822, 3.6.2.
     */

        $from = $this->_createHeader('From', ['from@domain' => 'Name']);
        $message = $this->_createMessage(
            $this->_createHeaderSet(['From' => $from]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(['from@domain' => 'Name'], $message->getFrom());
    }

    public function test_from_is_set_in_header()
    {
        $from = $this->_createHeader('From', ['from@domain' => 'Name'],
            [], false
        );
        $from->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['From' => $from]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setFrom(['other@domain' => 'Other']);
    }

    public function test_from_is_added_to_headers_during_add_from()
    {
        $from = $this->_createHeader('From', ['from@domain' => 'Name'],
            [], false
        );
        $from->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['from@domain' => 'Name', 'other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['From' => $from]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->addFrom('other@domain', 'Other');
    }

    public function test_from_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('From', (array) 'from@domain');
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setFrom('from@domain');
    }

    public function test_personal_name_can_be_used_in_from_address()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('From', ['from@domain' => 'Name']);
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setFrom('from@domain', 'Name');
    }

    public function test_reply_to_is_returned_from_header()
    {
        /* -- RFC 2822, 3.6.2.
     */

        $reply = $this->_createHeader('Reply-To', ['reply@domain' => 'Name']);
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Reply-To' => $reply]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(['reply@domain' => 'Name'], $message->getReplyTo());
    }

    public function test_reply_to_is_set_in_header()
    {
        $reply = $this->_createHeader('Reply-To', ['reply@domain' => 'Name'],
            [], false
        );
        $reply->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Reply-To' => $reply]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setReplyTo(['other@domain' => 'Other']);
    }

    public function test_reply_to_is_added_to_headers_during_add_reply_to()
    {
        $replyTo = $this->_createHeader('Reply-To', ['from@domain' => 'Name'],
            [], false
        );
        $replyTo->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['from@domain' => 'Name', 'other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Reply-To' => $replyTo]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->addReplyTo('other@domain', 'Other');
    }

    public function test_reply_to_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Reply-To', (array) 'reply@domain');
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setReplyTo('reply@domain');
    }

    public function test_name_can_be_used_in_reply_to()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Reply-To', ['reply@domain' => 'Name']);
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setReplyTo('reply@domain', 'Name');
    }

    public function test_to_is_returned_from_header()
    {
        /* -- RFC 2822, 3.6.3.
     */

        $to = $this->_createHeader('To', ['to@domain' => 'Name']);
        $message = $this->_createMessage(
            $this->_createHeaderSet(['To' => $to]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(['to@domain' => 'Name'], $message->getTo());
    }

    public function test_to_is_set_in_header()
    {
        $to = $this->_createHeader('To', ['to@domain' => 'Name'],
            [], false
        );
        $to->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['To' => $to]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setTo(['other@domain' => 'Other']);
    }

    public function test_to_is_added_to_headers_during_add_to()
    {
        $to = $this->_createHeader('To', ['from@domain' => 'Name'],
            [], false
        );
        $to->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['from@domain' => 'Name', 'other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['To' => $to]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->addTo('other@domain', 'Other');
    }

    public function test_to_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('To', (array) 'to@domain');
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setTo('to@domain');
    }

    public function test_name_can_be_used_in_to_header()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('To', ['to@domain' => 'Name']);
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setTo('to@domain', 'Name');
    }

    public function test_cc_is_returned_from_header()
    {
        /* -- RFC 2822, 3.6.3.
     */

        $cc = $this->_createHeader('Cc', ['cc@domain' => 'Name']);
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Cc' => $cc]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(['cc@domain' => 'Name'], $message->getCc());
    }

    public function test_cc_is_set_in_header()
    {
        $cc = $this->_createHeader('Cc', ['cc@domain' => 'Name'],
            [], false
        );
        $cc->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Cc' => $cc]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setCc(['other@domain' => 'Other']);
    }

    public function test_cc_is_added_to_headers_during_add_cc()
    {
        $cc = $this->_createHeader('Cc', ['from@domain' => 'Name'],
            [], false
        );
        $cc->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['from@domain' => 'Name', 'other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Cc' => $cc]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->addCc('other@domain', 'Other');
    }

    public function test_cc_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Cc', (array) 'cc@domain');
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setCc('cc@domain');
    }

    public function test_name_can_be_used_in_cc_header()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Cc', ['cc@domain' => 'Name']);
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setCc('cc@domain', 'Name');
    }

    public function test_bcc_is_returned_from_header()
    {
        /* -- RFC 2822, 3.6.3.
     */

        $bcc = $this->_createHeader('Bcc', ['bcc@domain' => 'Name']);
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Bcc' => $bcc]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(['bcc@domain' => 'Name'], $message->getBcc());
    }

    public function test_bcc_is_set_in_header()
    {
        $bcc = $this->_createHeader('Bcc', ['bcc@domain' => 'Name'],
            [], false
        );
        $bcc->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Bcc' => $bcc]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setBcc(['other@domain' => 'Other']);
    }

    public function test_bcc_is_added_to_headers_during_add_bcc()
    {
        $bcc = $this->_createHeader('Bcc', ['from@domain' => 'Name'],
            [], false
        );
        $bcc->shouldReceive('setFieldBodyModel')
            ->once()
            ->with(['from@domain' => 'Name', 'other@domain' => 'Other']);

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Bcc' => $bcc]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->addBcc('other@domain', 'Other');
    }

    public function test_bcc_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Bcc', (array) 'bcc@domain');
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setBcc('bcc@domain');
    }

    public function test_name_can_be_used_in_bcc()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Bcc', ['bcc@domain' => 'Name']);
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setBcc('bcc@domain', 'Name');
    }

    public function test_priority_is_read_from_header()
    {
        $prio = $this->_createHeader('X-Priority', '2 (High)');
        $message = $this->_createMessage(
            $this->_createHeaderSet(['X-Priority' => $prio]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(2, $message->getPriority());
    }

    public function test_priority_is_set_in_header()
    {
        $prio = $this->_createHeader('X-Priority', '2 (High)', [], false);
        $prio->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('5 (Lowest)');

        $message = $this->_createMessage(
            $this->_createHeaderSet(['X-Priority' => $prio]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setPriority($message::PRIORITY_LOWEST);
    }

    public function test_priority_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addTextHeader')
            ->once()
            ->with('X-Priority', '4 (Low)');
        $headers->shouldReceive('addTextHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setPriority($message::PRIORITY_LOW);
    }

    public function test_read_receipt_address_read_from_header()
    {
        $rcpt = $this->_createHeader('Disposition-Notification-To',
            ['chris@swiftmailer.org' => 'Chris']
        );
        $message = $this->_createMessage(
            $this->_createHeaderSet(['Disposition-Notification-To' => $rcpt]),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertEquals(['chris@swiftmailer.org' => 'Chris'],
            $message->getReadReceiptTo()
        );
    }

    public function test_read_receipt_is_set_in_header()
    {
        $rcpt = $this->_createHeader('Disposition-Notification-To', [], [], false);
        $rcpt->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('mark@swiftmailer.org');

        $message = $this->_createMessage(
            $this->_createHeaderSet(['Disposition-Notification-To' => $rcpt]),
            $this->_createEncoder(), $this->_createCache()
        );
        $message->setReadReceiptTo('mark@swiftmailer.org');
    }

    public function test_read_receipt_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addMailboxHeader')
            ->once()
            ->with('Disposition-Notification-To', 'mark@swiftmailer.org');
        $headers->shouldReceive('addMailboxHeader')
            ->zeroOrMoreTimes();

        $message = $this->_createMessage($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $message->setReadReceiptTo('mark@swiftmailer.org');
    }

    public function test_children_can_be_attached()
    {
        $child1 = $this->_createChild();
        $child2 = $this->_createChild();

        $message = $this->_createMessage($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );

        $message->attach($child1);
        $message->attach($child2);

        $this->assertEquals([$child1, $child2], $message->getChildren());
    }

    public function test_children_can_be_detached()
    {
        $child1 = $this->_createChild();
        $child2 = $this->_createChild();

        $message = $this->_createMessage($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );

        $message->attach($child1);
        $message->attach($child2);

        $message->detach($child1);

        $this->assertEquals([$child2], $message->getChildren());
    }

    public function test_embed_attaches_child()
    {
        $child = $this->_createChild();

        $message = $this->_createMessage($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );

        $message->embed($child);

        $this->assertEquals([$child], $message->getChildren());
    }

    public function test_embed_returns_valid_cid()
    {
        $child = $this->_createChild(Swift_Mime_MimeEntity::LEVEL_RELATED, '',
            false
        );
        $child->shouldReceive('getId')
            ->zeroOrMoreTimes()
            ->andReturn('foo@bar');

        $message = $this->_createMessage($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );

        $this->assertEquals('cid:foo@bar', $message->embed($child));
    }

    public function test_fluid_interface()
    {
        $child = $this->_createChild();
        $message = $this->_createMessage($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertSame($message,
            $message
                ->setContentType('text/plain')
                ->setEncoder($this->_createEncoder())
                ->setId('foo@bar')
                ->setDescription('my description')
                ->setMaxLineLength(998)
                ->setBody('xx')
                ->setBoundary('xyz')
                ->setChildren([])
                ->setCharset('iso-8859-1')
                ->setFormat('flowed')
                ->setDelSp(false)
                ->setSubject('subj')
                ->setDate(123)
                ->setReturnPath('foo@bar')
                ->setSender('foo@bar')
                ->setFrom(['x@y' => 'XY'])
                ->setReplyTo(['ab@cd' => 'ABCD'])
                ->setTo(['chris@site.tld', 'mark@site.tld'])
                ->setCc('john@somewhere.tld')
                ->setBcc(['one@site', 'two@site' => 'Two'])
                ->setPriority($message::PRIORITY_LOW)
                ->setReadReceiptTo('a@b')
                ->attach($child)
                ->detach($child)
        );
    }

    // abstract
    protected function _createEntity($headers, $encoder, $cache)
    {
        return $this->_createMessage($headers, $encoder, $cache);
    }

    protected function _createMimePart($headers, $encoder, $cache)
    {
        return $this->_createMessage($headers, $encoder, $cache);
    }

    private function _createMessage($headers, $encoder, $cache)
    {
        return new Swift_Mime_SimpleMessage($headers, $encoder, $cache, new Swift_Mime_Grammar);
    }
}
