<?php

class Issue523Test extends PHPUnit_Framework_TestCase
{
    public function test_attribute_equals()
    {
        $this->assertAttributeEquals('foo', 'field', new Issue523);
    }
}

class Issue523 extends ArrayIterator
{
    protected $field = 'foo';
}
