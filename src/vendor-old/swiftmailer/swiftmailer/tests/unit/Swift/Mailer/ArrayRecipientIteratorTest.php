<?php

class Swift_Mailer_ArrayRecipientIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function test_has_next_returns_false_for_empty_array()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator([]);
        $this->assertFalse($it->hasNext());
    }

    public function test_has_next_returns_true_if_items_left()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator(['foo@bar' => 'Foo']);
        $this->assertTrue($it->hasNext());
    }

    public function test_reading_to_end_of_list_causes_has_next_to_return_false()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator(['foo@bar' => 'Foo']);
        $this->assertTrue($it->hasNext());
        $it->nextRecipient();
        $this->assertFalse($it->hasNext());
    }

    public function test_returned_value_has_preserved_key_value_pair()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator(['foo@bar' => 'Foo']);
        $this->assertEquals(['foo@bar' => 'Foo'], $it->nextRecipient());
    }

    public function test_iterator_moves_next_after_each_iteration()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator([
            'foo@bar' => 'Foo',
            'zip@button' => 'Zip thing',
            'test@test' => null,
        ]);
        $this->assertEquals(['foo@bar' => 'Foo'], $it->nextRecipient());
        $this->assertEquals(['zip@button' => 'Zip thing'], $it->nextRecipient());
        $this->assertEquals(['test@test' => null], $it->nextRecipient());
    }
}
