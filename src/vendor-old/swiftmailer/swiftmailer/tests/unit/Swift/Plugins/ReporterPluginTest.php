<?php

class Swift_Plugins_ReporterPluginTest extends \SwiftMailerTestCase
{
    public function test_reporting_passes()
    {
        $message = $this->_createMessage();
        $evt = $this->_createSendEvent();
        $reporter = $this->_createReporter();

        $message->shouldReceive('getTo')->zeroOrMoreTimes()->andReturn(['foo@bar.tld' => 'Foo']);
        $evt->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn($message);
        $evt->shouldReceive('getFailedRecipients')->zeroOrMoreTimes()->andReturn([]);
        $reporter->shouldReceive('notify')->once()->with($message, 'foo@bar.tld', Swift_Plugins_Reporter::RESULT_PASS);

        $plugin = new Swift_Plugins_ReporterPlugin($reporter);
        $plugin->sendPerformed($evt);
    }

    public function test_reporting_failed_to()
    {
        $message = $this->_createMessage();
        $evt = $this->_createSendEvent();
        $reporter = $this->_createReporter();

        $message->shouldReceive('getTo')->zeroOrMoreTimes()->andReturn(['foo@bar.tld' => 'Foo', 'zip@button' => 'Zip']);
        $evt->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn($message);
        $evt->shouldReceive('getFailedRecipients')->zeroOrMoreTimes()->andReturn(['zip@button']);
        $reporter->shouldReceive('notify')->once()->with($message, 'foo@bar.tld', Swift_Plugins_Reporter::RESULT_PASS);
        $reporter->shouldReceive('notify')->once()->with($message, 'zip@button', Swift_Plugins_Reporter::RESULT_FAIL);

        $plugin = new Swift_Plugins_ReporterPlugin($reporter);
        $plugin->sendPerformed($evt);
    }

    public function test_reporting_failed_cc()
    {
        $message = $this->_createMessage();
        $evt = $this->_createSendEvent();
        $reporter = $this->_createReporter();

        $message->shouldReceive('getTo')->zeroOrMoreTimes()->andReturn(['foo@bar.tld' => 'Foo']);
        $message->shouldReceive('getCc')->zeroOrMoreTimes()->andReturn(['zip@button' => 'Zip', 'test@test.com' => 'Test']);
        $evt->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn($message);
        $evt->shouldReceive('getFailedRecipients')->zeroOrMoreTimes()->andReturn(['zip@button']);
        $reporter->shouldReceive('notify')->once()->with($message, 'foo@bar.tld', Swift_Plugins_Reporter::RESULT_PASS);
        $reporter->shouldReceive('notify')->once()->with($message, 'zip@button', Swift_Plugins_Reporter::RESULT_FAIL);
        $reporter->shouldReceive('notify')->once()->with($message, 'test@test.com', Swift_Plugins_Reporter::RESULT_PASS);

        $plugin = new Swift_Plugins_ReporterPlugin($reporter);
        $plugin->sendPerformed($evt);
    }

    public function test_reporting_failed_bcc()
    {
        $message = $this->_createMessage();
        $evt = $this->_createSendEvent();
        $reporter = $this->_createReporter();

        $message->shouldReceive('getTo')->zeroOrMoreTimes()->andReturn(['foo@bar.tld' => 'Foo']);
        $message->shouldReceive('getBcc')->zeroOrMoreTimes()->andReturn(['zip@button' => 'Zip', 'test@test.com' => 'Test']);
        $evt->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn($message);
        $evt->shouldReceive('getFailedRecipients')->zeroOrMoreTimes()->andReturn(['zip@button']);
        $reporter->shouldReceive('notify')->once()->with($message, 'foo@bar.tld', Swift_Plugins_Reporter::RESULT_PASS);
        $reporter->shouldReceive('notify')->once()->with($message, 'zip@button', Swift_Plugins_Reporter::RESULT_FAIL);
        $reporter->shouldReceive('notify')->once()->with($message, 'test@test.com', Swift_Plugins_Reporter::RESULT_PASS);

        $plugin = new Swift_Plugins_ReporterPlugin($reporter);
        $plugin->sendPerformed($evt);
    }

    private function _createMessage()
    {
        return $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
    }

    private function _createSendEvent()
    {
        return $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
    }

    private function _createReporter()
    {
        return $this->getMockery('Swift_Plugins_Reporter')->shouldIgnoreMissing();
    }
}
