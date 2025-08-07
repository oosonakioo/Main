<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Trait_ extends ClassLike
{
    /**
     * Constructs a trait node.
     *
     * @param  string  $name  Name
     * @param  Node[]  $stmts  Statements
     * @param  array  $attributes  Additional attributes
     */
    public function __construct($name, array $stmts = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name = $name;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames()
    {
        return ['name', 'stmts'];
    }
}
