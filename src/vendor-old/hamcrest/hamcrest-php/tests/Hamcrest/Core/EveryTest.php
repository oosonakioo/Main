<?php

namespace Hamcrest\Core;

class EveryTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\Every::everyItem(anything());
    }

    public function test_is_true_when_every_value_matches()
    {
        assertThat(['AaA', 'BaB', 'CaC'], everyItem(containsString('a')));
        assertThat(['AbA', 'BbB', 'CbC'], not(everyItem(containsString('a'))));
    }

    public function test_is_always_true_for_empty_lists()
    {
        assertThat([], everyItem(containsString('a')));
    }

    public function test_describes_itself()
    {
        $each = everyItem(containsString('a'));
        $this->assertEquals('every item is a string containing "a"', (string) $each);

        $this->assertMismatchDescription('an item was "BbB"', $each, ['BbB']);
    }
}
