<?php

/**
 * Some comment
 */
class Foo
{
    public function foo() {}

    public function bar(Baz $baz) {}

    public static function foobar(Foobar $foobar) {}

    public function barfoo(Barfoo $barfoo) {}

    /**
     * This docblock does not belong to the baz function
     */
    public function baz() {}

    public function blaz($x, $y) {}
}
