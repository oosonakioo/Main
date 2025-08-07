<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Config;

use Symfony\Component\HttpKernel\Config\EnvParametersResource;

class EnvParametersResourceTest extends \PHPUnit_Framework_TestCase
{
    protected $prefix = '__DUMMY_';

    protected $initialEnv;

    protected $resource;

    protected function setUp()
    {
        $this->initialEnv = [
            $this->prefix.'1' => 'foo',
            $this->prefix.'2' => 'bar',
        ];

        foreach ($this->initialEnv as $key => $value) {
            $_SERVER[$key] = $value;
        }

        $this->resource = new EnvParametersResource($this->prefix);
    }

    protected function tearDown()
    {
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, $this->prefix) === 0) {
                unset($_SERVER[$key]);
            }
        }
    }

    public function test_get_resource()
    {
        $this->assertSame(
            ['prefix' => $this->prefix, 'variables' => $this->initialEnv],
            $this->resource->getResource(),
            '->getResource() returns the resource'
        );
    }

    public function test_to_string()
    {
        $this->assertSame(
            serialize(['prefix' => $this->prefix, 'variables' => $this->initialEnv]),
            (string) $this->resource
        );
    }

    public function test_is_fresh_not_changed()
    {
        $this->assertTrue(
            $this->resource->isFresh(time()),
            '->isFresh() returns true if the variables have not changed'
        );
    }

    public function test_is_fresh_value_changed()
    {
        reset($this->initialEnv);
        $_SERVER[key($this->initialEnv)] = 'baz';

        $this->assertFalse(
            $this->resource->isFresh(time()),
            '->isFresh() returns false if a variable has been changed'
        );
    }

    public function test_is_fresh_value_removed()
    {
        reset($this->initialEnv);
        unset($_SERVER[key($this->initialEnv)]);

        $this->assertFalse(
            $this->resource->isFresh(time()),
            '->isFresh() returns false if a variable has been removed'
        );
    }

    public function test_is_fresh_value_added()
    {
        $_SERVER[$this->prefix.'3'] = 'foo';

        $this->assertFalse(
            $this->resource->isFresh(time()),
            '->isFresh() returns false if a variable has been added'
        );
    }

    public function test_serialize_unserialize()
    {
        $this->assertEquals($this->resource, unserialize(serialize($this->resource)));
    }
}
