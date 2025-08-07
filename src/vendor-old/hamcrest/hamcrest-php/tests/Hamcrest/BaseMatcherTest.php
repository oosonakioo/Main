<?php

namespace Hamcrest;

/* Test-specific subclass only */
class BaseMatcherTest extends \Hamcrest\BaseMatcher
{
    public function matches($item)
    {
        throw new \RuntimeException;
    }

    public function describeTo(\Hamcrest\Description $description)
    {
        $description->appendText('SOME DESCRIPTION');
    }

    public function test_describes_itself_with_to_string_method()
    {
        $someMatcher = new \Hamcrest\SomeMatcher;
        $this->assertEquals('SOME DESCRIPTION', (string) $someMatcher);
    }
}
