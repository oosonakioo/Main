<?php

namespace Fixtures\Prophecy;

class SpecialMethods
{
    public function __construct() {}

    public function __destruct() {}

    public function __call($name, $arguments) {}

    public function __sleep() {}

    public function __wakeup() {}

    public function __toString()
    {
        return '';
    }

    public function __invoke() {}
}
