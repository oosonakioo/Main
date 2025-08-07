<?php

class Swift_Plugins_Loggers_EchoLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function test_adding_entry_dumps_single_line_without_html()
    {
        $logger = new Swift_Plugins_Loggers_EchoLogger(false);
        ob_start();
        $logger->add('>> Foo');
        $data = ob_get_clean();

        $this->assertEquals('>> Foo'.PHP_EOL, $data);
    }

    public function test_adding_entry_dumps_escaped_line_with_html()
    {
        $logger = new Swift_Plugins_Loggers_EchoLogger(true);
        ob_start();
        $logger->add('>> Foo');
        $data = ob_get_clean();

        $this->assertEquals('&gt;&gt; Foo<br />'.PHP_EOL, $data);
    }
}
