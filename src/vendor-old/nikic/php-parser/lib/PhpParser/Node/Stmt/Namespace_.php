<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Error;
use PhpParser\Node;

class Namespace_ extends Node\Stmt
{
    /** @var null|Node\Name Name */
    public $name;

    /** @var Node[] Statements */
    public $stmts;

    protected static $specialNames = [
        'self' => true,
        'parent' => true,
        'static' => true,
    ];

    /**
     * Constructs a namespace node.
     *
     * @param  null|Node\Name  $name  Name
     * @param  null|Node[]  $stmts  Statements
     * @param  array  $attributes  Additional attributes
     */
    public function __construct(?Node\Name $name = null, $stmts = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name = $name;
        $this->stmts = $stmts;

        if (isset(self::$specialNames[strtolower($this->name)])) {
            throw new Error(
                sprintf('Cannot use \'%s\' as namespace name', $this->name),
                $this->name->getAttributes()
            );
        }

        if ($this->stmts !== null) {
            foreach ($this->stmts as $stmt) {
                if ($stmt instanceof self) {
                    throw new Error('Namespace declarations cannot be nested', $stmt->getAttributes());
                }
            }
        }
    }

    public function getSubNodeNames()
    {
        return ['name', 'stmts'];
    }
}
