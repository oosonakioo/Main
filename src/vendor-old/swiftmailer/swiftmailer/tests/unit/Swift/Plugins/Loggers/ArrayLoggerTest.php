<?php

class Swift_Plugins_Loggers_ArrayLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function test_adding_single_entry_dumps_single_line()
    {
        $logger = new Swift_Plugins_Loggers_ArrayLogger;
        $logger->add(">> Foo\r\n");
        $this->assertEquals(">> Foo\r\n", $logger->dump());
    }

    public function test_adding_multiple_entries_dumps_multiple_lines()
    {
        $logger = new Swift_Plugins_Loggers_ArrayLogger;
        $logger->add(">> FOO\r\n");
        $logger->add("<< 502 That makes no sense\r\n");
        $logger->add(">> RSET\r\n");
        $logger->add("<< 250 OK\r\n");

        $this->assertEquals(
            ">> FOO\r\n".PHP_EOL.
            "<< 502 That makes no sense\r\n".PHP_EOL.
            ">> RSET\r\n".PHP_EOL.
            "<< 250 OK\r\n",
            $logger->dump()
        );
    }

    public function test_log_can_be_cleared()
    {
        $logger = new Swift_Plugins_Loggers_ArrayLogger;
        $logger->add(">> FOO\r\n");
        $logger->add("<< 502 That makes no sense\r\n");
        $logger->add(">> RSET\r\n");
        $logger->add("<< 250 OK\r\n");

        $this->assertEquals(
            ">> FOO\r\n".PHP_EOL.
            "<< 502 That makes no sense\r\n".PHP_EOL.
            ">> RSET\r\n".PHP_EOL.
            "<< 250 OK\r\n",
            $logger->dump()
        );

        $logger->clear();

        $this->assertEquals('', $logger->dump());
    }

    public function test_length_can_be_truncated()
    {
        $logger = new Swift_Plugins_Loggers_ArrayLogger(2);
        $logger->add(">> FOO\r\n");
        $logger->add("<< 502 That makes no sense\r\n");
        $logger->add(">> RSET\r\n");
        $logger->add("<< 250 OK\r\n");

        $this->assertEquals(
            ">> RSET\r\n".PHP_EOL.
            "<< 250 OK\r\n",
            $logger->dump(),
            '%s: Log should be truncated to last 2 entries'
        );
    }
}
