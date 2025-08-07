<?php

/** Docblock */
interface Foo
{
    public function bar();
}

class Foo
{
    public function bar() {}
}

function baz()
{
    // a one-line comment
    echo '*'; // a one-line comment

    /* a one-line comment */
    echo '*'; /* a one-line comment */

    /* a one-line comment
     */
    echo '*'; /* a one-line comment
    */

    echo '*'; // @codeCoverageIgnore

    echo '*'; // @codeCoverageIgnoreStart
    echo '*';
    echo '*'; // @codeCoverageIgnoreEnd

    echo '*';
}
