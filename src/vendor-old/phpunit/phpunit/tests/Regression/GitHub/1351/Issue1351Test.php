<?php

class Issue1351Test extends PHPUnit_Framework_TestCase
{
    protected $instance;

    /**
     * @runInSeparateProcess
     */
    public function test_failure_pre()
    {
        $this->instance = new ChildProcessClass1351;
        $this->assertFalse(true, 'Expected failure.');
    }

    public function test_failure_post()
    {
        $this->assertNull($this->instance);
        $this->assertFalse(class_exists('ChildProcessClass1351', false), 'ChildProcessClass1351 is not loaded.');
    }

    /**
     * @runInSeparateProcess
     */
    public function test_exception_pre()
    {
        $this->instance = new ChildProcessClass1351;
        try {
            throw new LogicException('Expected exception.');
        } catch (LogicException $e) {
            throw new RuntimeException('Expected rethrown exception.', 0, $e);
        }
    }

    public function test_exception_post()
    {
        $this->assertNull($this->instance);
        $this->assertFalse(class_exists('ChildProcessClass1351', false), 'ChildProcessClass1351 is not loaded.');
    }

    public function test_php_core_language_exception()
    {
        // User-space code cannot instantiate a PDOException with a string code,
        // so trigger a real one.
        $connection = new PDO('sqlite::memory:');
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->query("DELETE FROM php_wtf WHERE exception_code = 'STRING'");
    }
}
