<?php

namespace Hamcrest;

class SampleSelfDescriber implements \Hamcrest\SelfDescribing
{
    private $_text;

    public function __construct($text)
    {
        $this->_text = $text;
    }

    public function describeTo(\Hamcrest\Description $description)
    {
        $description->appendText($this->_text);
    }
}

class StringDescriptionTest extends \PhpUnit_Framework_TestCase
{
    private $_description;

    protected function setUp()
    {
        $this->_description = new \Hamcrest\StringDescription;
    }

    public function test_append_text_appends_text_information()
    {
        $this->_description->appendText('foo')->appendText('bar');
        $this->assertEquals('foobar', (string) $this->_description);
    }

    public function test_append_value_can_append_text_types()
    {
        $this->_description->appendValue('foo');
        $this->assertEquals('"foo"', (string) $this->_description);
    }

    public function test_special_characters_are_escaped_for_string_types()
    {
        $this->_description->appendValue("foo\\bar\"zip\r\n");
        $this->assertEquals('"foo\\bar\\"zip\r\n"', (string) $this->_description);
    }

    public function test_integer_values_can_be_appended()
    {
        $this->_description->appendValue(42);
        $this->assertEquals('<42>', (string) $this->_description);
    }

    public function test_float_values_can_be_appended()
    {
        $this->_description->appendValue(42.78);
        $this->assertEquals('<42.78F>', (string) $this->_description);
    }

    public function test_null_values_can_be_appended()
    {
        $this->_description->appendValue(null);
        $this->assertEquals('null', (string) $this->_description);
    }

    public function test_arrays_can_be_appended()
    {
        $this->_description->appendValue(['foo', 42.78]);
        $this->assertEquals('["foo", <42.78F>]', (string) $this->_description);
    }

    public function test_objects_can_be_appended()
    {
        $this->_description->appendValue(new \stdClass);
        $this->assertEquals('<stdClass>', (string) $this->_description);
    }

    public function test_boolean_values_can_be_appended()
    {
        $this->_description->appendValue(false);
        $this->assertEquals('<false>', (string) $this->_description);
    }

    public function test_lists_ofvalues_can_be_appended()
    {
        $this->_description->appendValue(['foo', 42.78]);
        $this->assertEquals('["foo", <42.78F>]', (string) $this->_description);
    }

    public function test_iterable_ofvalues_can_be_appended()
    {
        $items = new \ArrayObject(['foo', 42.78]);
        $this->_description->appendValue($items);
        $this->assertEquals('["foo", <42.78F>]', (string) $this->_description);
    }

    public function test_iterator_ofvalues_can_be_appended()
    {
        $items = new \ArrayObject(['foo', 42.78]);
        $this->_description->appendValue($items->getIterator());
        $this->assertEquals('["foo", <42.78F>]', (string) $this->_description);
    }

    public function test_lists_ofvalues_can_be_appended_manually()
    {
        $this->_description->appendValueList('@start@', '@sep@ ', '@end@', ['foo', 42.78]);
        $this->assertEquals('@start@"foo"@sep@ <42.78F>@end@', (string) $this->_description);
    }

    public function test_iterable_ofvalues_can_be_appended_manually()
    {
        $items = new \ArrayObject(['foo', 42.78]);
        $this->_description->appendValueList('@start@', '@sep@ ', '@end@', $items);
        $this->assertEquals('@start@"foo"@sep@ <42.78F>@end@', (string) $this->_description);
    }

    public function test_iterator_ofvalues_can_be_appended_manually()
    {
        $items = new \ArrayObject(['foo', 42.78]);
        $this->_description->appendValueList('@start@', '@sep@ ', '@end@', $items->getIterator());
        $this->assertEquals('@start@"foo"@sep@ <42.78F>@end@', (string) $this->_description);
    }

    public function test_self_describing_objects_can_be_appended()
    {
        $this->_description
            ->appendDescriptionOf(new \Hamcrest\SampleSelfDescriber('foo'))
            ->appendDescriptionOf(new \Hamcrest\SampleSelfDescriber('bar'));
        $this->assertEquals('foobar', (string) $this->_description);
    }

    public function test_self_describing_objects_can_be_appended_as_lists()
    {
        $this->_description->appendList('@start@', '@sep@ ', '@end@', [
            new \Hamcrest\SampleSelfDescriber('foo'),
            new \Hamcrest\SampleSelfDescriber('bar'),
        ]);
        $this->assertEquals('@start@foo@sep@ bar@end@', (string) $this->_description);
    }

    public function test_self_describing_objects_can_be_appended_as_iterated_lists()
    {
        $items = new \ArrayObject([
            new \Hamcrest\SampleSelfDescriber('foo'),
            new \Hamcrest\SampleSelfDescriber('bar'),
        ]);
        $this->_description->appendList('@start@', '@sep@ ', '@end@', $items);
        $this->assertEquals('@start@foo@sep@ bar@end@', (string) $this->_description);
    }

    public function test_self_describing_objects_can_be_appended_as_iterators()
    {
        $items = new \ArrayObject([
            new \Hamcrest\SampleSelfDescriber('foo'),
            new \Hamcrest\SampleSelfDescriber('bar'),
        ]);
        $this->_description->appendList('@start@', '@sep@ ', '@end@', $items->getIterator());
        $this->assertEquals('@start@foo@sep@ bar@end@', (string) $this->_description);
    }
}
