<?php

class Swift_Transport_SendmailTransportTest extends Swift_Transport_AbstractSmtpEventSupportTest
{
    protected function _getTransport($buf, $dispatcher = null, $command = '/usr/sbin/sendmail -bs')
    {
        if (! $dispatcher) {
            $dispatcher = $this->_createEventDispatcher();
        }
        $transport = new Swift_Transport_SendmailTransport($buf, $dispatcher);
        $transport->setCommand($command);

        return $transport;
    }

    protected function _getSendmail($buf, $dispatcher = null)
    {
        if (! $dispatcher) {
            $dispatcher = $this->_createEventDispatcher();
        }
        $sendmail = new Swift_Transport_SendmailTransport($buf, $dispatcher);

        return $sendmail;
    }

    public function test_command_can_be_set_and_fetched()
    {
        $buf = $this->_getBuffer();
        $sendmail = $this->_getSendmail($buf);

        $sendmail->setCommand('/usr/sbin/sendmail -bs');
        $this->assertEquals('/usr/sbin/sendmail -bs', $sendmail->getCommand());
        $sendmail->setCommand('/usr/sbin/sendmail -oi -t');
        $this->assertEquals('/usr/sbin/sendmail -oi -t', $sendmail->getCommand());
    }

    public function test_sending_message_in_t_mode_uses_simple_pipe()
    {
        $buf = $this->_getBuffer();
        $sendmail = $this->_getSendmail($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['foo@bar' => 'Foobar', 'zip@button' => 'Zippy']);
        $message->shouldReceive('toByteStream')
            ->once()
            ->with($buf);
        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('terminate')
            ->once();
        $buf->shouldReceive('setWriteTranslations')
            ->once()
            ->with(["\r\n" => "\n", "\n." => "\n.."]);
        $buf->shouldReceive('setWriteTranslations')
            ->once()
            ->with([]);

        $sendmail->setCommand('/usr/sbin/sendmail -t');
        $this->assertEquals(2, $sendmail->send($message));
    }

    public function test_sending_in_t_mode_with_i_flag_doesnt_escape_dot()
    {
        $buf = $this->_getBuffer();
        $sendmail = $this->_getSendmail($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['foo@bar' => 'Foobar', 'zip@button' => 'Zippy']);
        $message->shouldReceive('toByteStream')
            ->once()
            ->with($buf);
        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('terminate')
            ->once();
        $buf->shouldReceive('setWriteTranslations')
            ->once()
            ->with(["\r\n" => "\n"]);
        $buf->shouldReceive('setWriteTranslations')
            ->once()
            ->with([]);

        $sendmail->setCommand('/usr/sbin/sendmail -i -t');
        $this->assertEquals(2, $sendmail->send($message));
    }

    public function test_sending_in_t_mode_with_oi_flag_doesnt_escape_dot()
    {
        $buf = $this->_getBuffer();
        $sendmail = $this->_getSendmail($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['foo@bar' => 'Foobar', 'zip@button' => 'Zippy']);
        $message->shouldReceive('toByteStream')
            ->once()
            ->with($buf);
        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('terminate')
            ->once();
        $buf->shouldReceive('setWriteTranslations')
            ->once()
            ->with(["\r\n" => "\n"]);
        $buf->shouldReceive('setWriteTranslations')
            ->once()
            ->with([]);

        $sendmail->setCommand('/usr/sbin/sendmail -oi -t');
        $this->assertEquals(2, $sendmail->send($message));
    }

    public function test_sending_message_regenerates_id()
    {
        $buf = $this->_getBuffer();
        $sendmail = $this->_getSendmail($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getTo')
            ->zeroOrMoreTimes()
            ->andReturn(['foo@bar' => 'Foobar', 'zip@button' => 'Zippy']);
        $message->shouldReceive('generateId');
        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('terminate')
            ->once();
        $buf->shouldReceive('setWriteTranslations')
            ->once()
            ->with(["\r\n" => "\n", "\n." => "\n.."]);
        $buf->shouldReceive('setWriteTranslations')
            ->once()
            ->with([]);

        $sendmail->setCommand('/usr/sbin/sendmail -t');
        $this->assertEquals(2, $sendmail->send($message));
    }

    public function test_fluid_interface()
    {
        $buf = $this->_getBuffer();
        $sendmail = $this->_getTransport($buf);

        $ref = $sendmail->setCommand('/foo');
        $this->assertEquals($ref, $sendmail);
    }
}
