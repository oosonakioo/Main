<?php

/**
 * @group legacy
 */
class Swift_Transport_MailTransportTest extends \SwiftMailerTestCase
{
    public function test_transport_invokes_mail_once_per_message()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessageWithRecipient($headers);

        $invoker->shouldReceive('mail')
            ->once();

        $transport->send($message);
    }

    public function test_transport_uses_to_field_body_in_sending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders([
            'To' => $to,
        ]);
        $message = $this->_createMessageWithRecipient($headers);

        $to->shouldReceive('getFieldBody')
            ->zeroOrMoreTimes()
            ->andReturn('Foo <foo@bar>');
        $invoker->shouldReceive('mail')
            ->once()
            ->with('Foo <foo@bar>', \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function test_transport_uses_subject_field_body_in_sending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $subj = $this->_createHeader();
        $headers = $this->_createHeaders([
            'Subject' => $subj,
        ]);
        $message = $this->_createMessageWithRecipient($headers);

        $subj->shouldReceive('getFieldBody')
            ->zeroOrMoreTimes()
            ->andReturn('Thing');
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), 'Thing', \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function test_transport_uses_body_of_message()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessageWithRecipient($headers);

        $message->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn(
                "To: Foo <foo@bar>\r\n".
                "\r\n".
                'This body'
            );
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), 'This body', \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function test_transport_setting_using_return_path_for_extra_params()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessageWithRecipient($headers);

        $message->shouldReceive('getReturnPath')
            ->zeroOrMoreTimes()
            ->andReturn(
                'foo@bar'
            );
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), '-ffoo@bar');

        $transport->send($message);
    }

    public function test_transport_setting_empty_extra_params()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessageWithRecipient($headers);

        $message->shouldReceive('getReturnPath')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $message->shouldReceive('getSender')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), null);

        $transport->send($message);
    }

    public function test_transport_setting_setting_extra_params_with_f()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);
        $transport->setExtraParams('-x\'foo\' -f%s');

        $headers = $this->_createHeaders();
        $message = $this->_createMessageWithRecipient($headers);

        $message->shouldReceive('getReturnPath')
            ->zeroOrMoreTimes()
            ->andReturn(
                'foo@bar'
            );
        $message->shouldReceive('getSender')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), '-x\'foo\' -ffoo@bar');

        $transport->send($message);
    }

    public function test_transport_setting_setting_extra_params_without_f()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);
        $transport->setExtraParams('-x\'foo\'');

        $headers = $this->_createHeaders();
        $message = $this->_createMessageWithRecipient($headers);

        $message->shouldReceive('getReturnPath')
            ->zeroOrMoreTimes()
            ->andReturn(
                'foo@bar'
            );
        $message->shouldReceive('getSender')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), '-x\'foo\'');

        $transport->send($message);
    }

    public function test_transport_setting_invalid_from_email()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessageWithRecipient($headers);

        $message->shouldReceive('getReturnPath')
            ->zeroOrMoreTimes()
            ->andReturn(
                '"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com'
            );
        $message->shouldReceive('getSender')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn(null);
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), null);

        $transport->send($message);
    }

    public function test_transport_uses_headers_from_message()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessageWithRecipient($headers);

        $message->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn(
                "Subject: Stuff\r\n".
                "\r\n".
                'This body'
            );
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), 'Subject: Stuff'.PHP_EOL, \Mockery::any());

        $transport->send($message);
    }

    public function test_transport_returns_count_of_all_recipients_if_invoker_returns_true()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['foo@bar' => null, 'zip@button' => null]);
        $message->shouldReceive('getCc')
            ->zeroOrMoreTimes()
            ->andReturn(['test@test' => null]);
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any())
            ->andReturn(true);

        $this->assertEquals(3, $transport->send($message));
    }

    public function test_transport_returns_zero_if_invoker_returns_false()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['foo@bar' => null, 'zip@button' => null]);
        $message->shouldReceive('getCc')
            ->zeroOrMoreTimes()
            ->andReturn(['test@test' => null]);
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any())
            ->andReturn(false);

        $this->assertEquals(0, $transport->send($message));
    }

    public function test_to_header_is_removed_from_header_set_during_sending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders([
            'To' => $to,
        ]);
        $message = $this->_createMessageWithRecipient($headers);

        $headers->shouldReceive('remove')
            ->once()
            ->with('To');
        $headers->shouldReceive('remove')
            ->zeroOrMoreTimes();
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function test_subject_header_is_removed_from_header_set_during_sending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $subject = $this->_createHeader();
        $headers = $this->_createHeaders([
            'Subject' => $subject,
        ]);
        $message = $this->_createMessageWithRecipient($headers);

        $headers->shouldReceive('remove')
            ->once()
            ->with('Subject');
        $headers->shouldReceive('remove')
            ->zeroOrMoreTimes();
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function test_to_header_is_put_back_after_sending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders([
            'To' => $to,
        ]);
        $message = $this->_createMessageWithRecipient($headers);

        $headers->shouldReceive('set')
            ->once()
            ->with($to);
        $headers->shouldReceive('set')
            ->zeroOrMoreTimes();
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function test_subject_header_is_put_back_after_sending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $subject = $this->_createHeader();
        $headers = $this->_createHeaders([
            'Subject' => $subject,
        ]);
        $message = $this->_createMessageWithRecipient($headers);

        $headers->shouldReceive('set')
            ->once()
            ->with($subject);
        $headers->shouldReceive('set')
            ->zeroOrMoreTimes();
        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function test_message_headers_only_have_php_eols_during_sending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $subject = $this->_createHeader();
        $subject->shouldReceive('getFieldBody')->andReturn("Foo\r\nBar");

        $headers = $this->_createHeaders([
            'Subject' => $subject,
        ]);
        $message = $this->_createMessageWithRecipient($headers);
        $message->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn(
                "From: Foo\r\n<foo@bar>\r\n".
                "\r\n".
                "This\r\n".
                'body'
            );

        if ("\r\n" != PHP_EOL) {
            $expectedHeaders = "From: Foo\n<foo@bar>\n";
            $expectedSubject = "Foo\nBar";
            $expectedBody = "This\nbody";
        } else {
            $expectedHeaders = "From: Foo\r\n<foo@bar>\r\n";
            $expectedSubject = "Foo\r\nBar";
            $expectedBody = "This\r\nbody";
        }

        $invoker->shouldReceive('mail')
            ->once()
            ->with(\Mockery::any(), $expectedSubject, $expectedBody, $expectedHeaders, \Mockery::any());

        $transport->send($message);
    }

    /**
     * @expectedException \Swift_TransportException
     *
     * @expectedExceptionMessage Cannot send message without a recipient
     */
    public function test_exception_when_no_recipients()
    {
        $invoker = $this->_createInvoker();
        $invoker->shouldReceive('mail');
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $transport->send($message);
    }

    public function noExceptionWhenRecipientsExistProvider()
    {
        return [
            ['To'],
            ['Cc'],
            ['Bcc'],
        ];
    }

    /**
     * @dataProvider noExceptionWhenRecipientsExistProvider
     *
     * @param  string  $header
     */
    public function test_no_exception_when_recipients_exist($header)
    {
        $invoker = $this->_createInvoker();
        $invoker->shouldReceive('mail');
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);
        $message->shouldReceive(sprintf('get%s', $header))->andReturn(['foo@bar' => 'Foo']);

        $transport->send($message);
    }

    private function _createTransport($invoker, $dispatcher)
    {
        return new Swift_Transport_MailTransport($invoker, $dispatcher);
    }

    private function _createEventDispatcher()
    {
        return $this->getMockery('Swift_Events_EventDispatcher')->shouldIgnoreMissing();
    }

    private function _createInvoker()
    {
        return $this->getMockery('Swift_Transport_MailInvoker');
    }

    private function _createMessage($headers)
    {
        $message = $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
        $message->shouldReceive('getHeaders')
            ->zeroOrMoreTimes()
            ->andReturn($headers);

        return $message;
    }

    private function _createMessageWithRecipient($headers, $recipient = ['foo@bar' => 'Foo'])
    {
        $message = $this->_createMessage($headers);
        $message->shouldReceive('getTo')->andReturn($recipient);

        return $message;
    }

    private function _createHeaders($headers = [])
    {
        $set = $this->getMockery('Swift_Mime_HeaderSet')->shouldIgnoreMissing();

        if (count($headers) > 0) {
            foreach ($headers as $name => $header) {
                $set->shouldReceive('get')
                    ->zeroOrMoreTimes()
                    ->with($name)
                    ->andReturn($header);
                $set->shouldReceive('has')
                    ->zeroOrMoreTimes()
                    ->with($name)
                    ->andReturn(true);
            }
        }

        $header = $this->_createHeader();
        $set->shouldReceive('get')
            ->zeroOrMoreTimes()
            ->andReturn($header);
        $set->shouldReceive('has')
            ->zeroOrMoreTimes()
            ->andReturn(true);

        return $set;
    }

    private function _createHeader()
    {
        return $this->getMockery('Swift_Mime_Header')->shouldIgnoreMissing();
    }
}
