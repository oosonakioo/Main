<?php

if ($neverHappens) {
    // @codeCoverageIgnoreStart
    echo '*';
    // @codeCoverageIgnoreEnd
}

/**
 * @codeCoverageIgnore
 */
class Foo
{
    public function bar() {}
}

class Bar
{
    /**
     * @codeCoverageIgnore
     */
    public function foo() {}
}

function baz()
{
    echo '*'; // @codeCoverageIgnore
}

interface Bor
{
    public function foo();
}
