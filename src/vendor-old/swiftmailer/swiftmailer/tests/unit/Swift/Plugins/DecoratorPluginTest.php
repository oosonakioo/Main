<?php

class Swift_Plugins_DecoratorPluginTest extends \SwiftMailerTestCase
{
    public function test_message_body_receives_replacements()
    {
        $message = $this->_createMessage(
            $this->_createHeaders(),
            ['zip@button.tld' => 'Zipathon'],
            ['chris.corbyn@swiftmailer.org' => 'Chris'],
            'Subject',
            'Hello {name}, you are customer #{id}'
        );
        $message->shouldReceive('setBody')
            ->once()
            ->with('Hello Zip, you are customer #456');
        $message->shouldReceive('setBody')
            ->zeroOrMoreTimes();

        $plugin = $this->_createPlugin(
            ['zip@button.tld' => ['{name}' => 'Zip', '{id}' => '456']]
        );

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);
        $plugin->sendPerformed($evt);
    }

    public function test_replacements_can_be_applied_to_same_message_multiple_times()
    {
        $message = $this->_createMessage(
            $this->_createHeaders(),
            ['zip@button.tld' => 'Zipathon', 'foo@bar.tld' => 'Foo'],
            ['chris.corbyn@swiftmailer.org' => 'Chris'],
            'Subject',
            'Hello {name}, you are customer #{id}'
        );
        $message->shouldReceive('setBody')
            ->once()
            ->with('Hello Zip, you are customer #456');
        $message->shouldReceive('setBody')
            ->once()
            ->with('Hello {name}, you are customer #{id}');
        $message->shouldReceive('setBody')
            ->once()
            ->with('Hello Foo, you are customer #123');
        $message->shouldReceive('setBody')
            ->zeroOrMoreTimes();

        $plugin = $this->_createPlugin(
            [
                'foo@bar.tld' => ['{name}' => 'Foo', '{id}' => '123'],
                'zip@button.tld' => ['{name}' => 'Zip', '{id}' => '456'],
            ]
        );

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);
        $plugin->sendPerformed($evt);
        $plugin->beforeSendPerformed($evt);
        $plugin->sendPerformed($evt);
    }

    public function test_replacements_can_be_made_in_headers()
    {
        $headers = $this->_createHeaders([
            $returnPathHeader = $this->_createHeader('Return-Path', 'foo-{id}@swiftmailer.org'),
            $toHeader = $this->_createHeader('Subject', 'A message for {name}!'),
        ]);

        $message = $this->_createMessage(
            $headers,
            ['zip@button.tld' => 'Zipathon'],
            ['chris.corbyn@swiftmailer.org' => 'Chris'],
            'A message for {name}!',
            'Hello {name}, you are customer #{id}'
        );

        $message->shouldReceive('setBody')
            ->once()
            ->with('Hello Zip, you are customer #456');
        $toHeader->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('A message for Zip!');
        $returnPathHeader->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('foo-456@swiftmailer.org');
        $message->shouldReceive('setBody')
            ->zeroOrMoreTimes();
        $toHeader->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();
        $returnPathHeader->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $plugin = $this->_createPlugin(
            ['zip@button.tld' => ['{name}' => 'Zip', '{id}' => '456']]
        );
        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);
        $plugin->sendPerformed($evt);
    }

    public function test_replacements_are_made_on_subparts()
    {
        $part1 = $this->_createPart('text/plain', 'Your name is {name}?', '1@x');
        $part2 = $this->_createPart('text/html', 'Your <em>name</em> is {name}?', '2@x');
        $message = $this->_createMessage(
            $this->_createHeaders(),
            ['zip@button.tld' => 'Zipathon'],
            ['chris.corbyn@swiftmailer.org' => 'Chris'],
            'A message for {name}!',
            'Subject'
        );
        $message->shouldReceive('getChildren')
            ->zeroOrMoreTimes()
            ->andReturn([$part1, $part2]);
        $part1->shouldReceive('setBody')
            ->once()
            ->with('Your name is Zip?');
        $part2->shouldReceive('setBody')
            ->once()
            ->with('Your <em>name</em> is Zip?');
        $part1->shouldReceive('setBody')
            ->zeroOrMoreTimes();
        $part2->shouldReceive('setBody')
            ->zeroOrMoreTimes();

        $plugin = $this->_createPlugin(
            ['zip@button.tld' => ['{name}' => 'Zip', '{id}' => '456']]
        );

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);
        $plugin->sendPerformed($evt);
    }

    public function test_replacements_can_be_taken_from_custom_replacements_object()
    {
        $message = $this->_createMessage(
            $this->_createHeaders(),
            ['foo@bar' => 'Foobar', 'zip@zap' => 'Zip zap'],
            ['chris.corbyn@swiftmailer.org' => 'Chris'],
            'Subject',
            'Something {a}'
        );

        $replacements = $this->_createReplacements();

        $message->shouldReceive('setBody')
            ->once()
            ->with('Something b');
        $message->shouldReceive('setBody')
            ->once()
            ->with('Something c');
        $message->shouldReceive('setBody')
            ->zeroOrMoreTimes();
        $replacements->shouldReceive('getReplacementsFor')
            ->once()
            ->with('foo@bar')
            ->andReturn(['{a}' => 'b']);
        $replacements->shouldReceive('getReplacementsFor')
            ->once()
            ->with('zip@zap')
            ->andReturn(['{a}' => 'c']);

        $plugin = $this->_createPlugin($replacements);

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);
        $plugin->sendPerformed($evt);
        $plugin->beforeSendPerformed($evt);
        $plugin->sendPerformed($evt);
    }

    private function _createMessage($headers, $to = [], $from = null, $subject = null,
        $body = null)
    {
        $message = $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
        foreach ($to as $addr => $name) {
            $message->shouldReceive('getTo')
                ->once()
                ->andReturn([$addr => $name]);
        }
        $message->shouldReceive('getHeaders')
            ->zeroOrMoreTimes()
            ->andReturn($headers);
        $message->shouldReceive('getFrom')
            ->zeroOrMoreTimes()
            ->andReturn($from);
        $message->shouldReceive('getSubject')
            ->zeroOrMoreTimes()
            ->andReturn($subject);
        $message->shouldReceive('getBody')
            ->zeroOrMoreTimes()
            ->andReturn($body);

        return $message;
    }

    private function _createPlugin($replacements)
    {
        return new Swift_Plugins_DecoratorPlugin($replacements);
    }

    private function _createReplacements()
    {
        return $this->getMockery('Swift_Plugins_Decorator_Replacements')->shouldIgnoreMissing();
    }

    private function _createSendEvent(Swift_Mime_Message $message)
    {
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $evt->shouldReceive('getMessage')
            ->zeroOrMoreTimes()
            ->andReturn($message);

        return $evt;
    }

    private function _createPart($type, $body, $id)
    {
        $part = $this->getMockery('Swift_Mime_MimeEntity')->shouldIgnoreMissing();
        $part->shouldReceive('getContentType')
            ->zeroOrMoreTimes()
            ->andReturn($type);
        $part->shouldReceive('getBody')
            ->zeroOrMoreTimes()
            ->andReturn($body);
        $part->shouldReceive('getId')
            ->zeroOrMoreTimes()
            ->andReturn($id);

        return $part;
    }

    private function _createHeaders($headers = [])
    {
        $set = $this->getMockery('Swift_Mime_HeaderSet')->shouldIgnoreMissing();
        $set->shouldReceive('getAll')
            ->zeroOrMoreTimes()
            ->andReturn($headers);

        foreach ($headers as $header) {
            $set->set($header);
        }

        return $set;
    }

    private function _createHeader($name, $body = '')
    {
        $header = $this->getMockery('Swift_Mime_Header')->shouldIgnoreMissing();
        $header->shouldReceive('getFieldName')
            ->zeroOrMoreTimes()
            ->andReturn($name);
        $header->shouldReceive('getFieldBodyModel')
            ->zeroOrMoreTimes()
            ->andReturn($body);

        return $header;
    }
}
