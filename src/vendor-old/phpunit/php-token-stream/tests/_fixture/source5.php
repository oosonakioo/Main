<?php

function foo($a, array $b, array $c = []) {}
interface i
{
    public function m($a, array $b, array $c = []);
}
abstract class a
{
    abstract public function m($a, array $b, array $c = []);
}
class c
{
    public function m($a, array $b, array $c = []) {}
}
