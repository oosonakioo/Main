<?php

namespace Fixtures\Prophecy;

class WithArguments
{
    public function methodWithArgs(array $arg_1, \ArrayAccess $arg_2, ?\ArrayAccess $arg_3 = null) {}

    public function methodWithoutTypeHints($arg) {}
}
