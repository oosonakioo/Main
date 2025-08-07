<?php

class Swift_MailerTest extends \SwiftMailerTestCase
{
    public function test_transport_is_started_when_sending()
    {
        $transport = $this->_createTransport();
        $message = $this->_createMessage();

        $started = false;
        $transport->shouldReceive('isStarted')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function () use (&$started) {
                return $started;
            });
        $transport->shouldReceive('start')
            ->once()
            ->andReturnUsing(function () use (&$started) {
                $started = true;

            });

        $mailer = $this->_createMailer($transport);
        $mailer->send($message);
    }

    public function test_transport_is_only_started_once()
    {
        $transport = $this->_createTransport();
        $message = $this->_createMessage();

        $started = false;
        $transport->shouldReceive('isStarted')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function () use (&$started) {
                return $started;
            });
        $transport->shouldReceive('start')
            ->once()
            ->andReturnUsing(function () use (&$started) {
                $started = true;

            });

        $mailer = $this->_createMailer($transport);
        for ($i = 0; $i < 10; $i++) {
            $mailer->send($message);
        }
    }

    public function test_message_is_passed_to_transport()
    {
        $transport = $this->_createTransport();
        $message = $this->_createMessage();
        $transport->shouldReceive('send')
            ->once()
            ->with($message, \Mockery::any());

        $mailer = $this->_createMailer($transport);
        $mailer->send($message);
    }

    public function test_send_returns_count_from_transport()
    {
        $transport = $this->_createTransport();
        $message = $this->_createMessage();
        $transport->shouldReceive('send')
            ->once()
            ->with($message, \Mockery::any())
            ->andReturn(57);

        $mailer = $this->_createMailer($transport);
        $this->assertEquals(57, $mailer->send($message));
    }

    public function test_failed_recipient_reference_is_passed_to_transport()
    {
        $failures = [];

        $transport = $this->_createTransport();
        $message = $this->_createMessage();
        $transport->shouldReceive('send')
            ->once()
            ->with($message, $failures)
            ->andReturn(57);

        $mailer = $this->_createMailer($transport);
        $mailer->send($message, $failures);
    }

    public function test_send_records_rfc_compliance_exception_as_entire_send_failure()
    {
        $failures = [];

        $rfcException = new Swift_RfcComplianceException('test');
        $transport = $this->_createTransport();
        $message = $this->_createMessage();
        $message->shouldReceive('getTo')
            ->once()
            ->andReturn(['foo&invalid' => 'Foo', 'bar@valid.tld' => 'Bar']);
        $transport->shouldReceive('send')
            ->once()
            ->with($message, $failures)
            ->andThrow($rfcException);

        $mailer = $this->_createMailer($transport);
        $this->assertEquals(0, $mailer->send($message, $failures), '%s: Should return 0');
        $this->assertEquals(['foo&invalid', 'bar@valid.tld'], $failures, '%s: Failures should contain all addresses since the entire message failed to compile');
    }

    public function test_register_plugin_delegates_to_transport()
    {
        $plugin = $this->_createPlugin();
        $transport = $this->_createTransport();
        $mailer = $this->_createMailer($transport);

        $transport->shouldReceive('registerPlugin')
            ->once()
            ->with($plugin);

        $mailer->registerPlugin($plugin);
    }

    private function _createPlugin()
    {
        return $this->getMockery('Swift_Events_EventListener')->shouldIgnoreMissing();
    }

    private function _createTransport()
    {
        return $this->getMockery('Swift_Transport')->shouldIgnoreMissing();
    }

    private function _createMessage()
    {
        return $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
    }

    private function _createMailer(Swift_Transport $transport)
    {
        return new Swift_Mailer($transport);
    }
}
