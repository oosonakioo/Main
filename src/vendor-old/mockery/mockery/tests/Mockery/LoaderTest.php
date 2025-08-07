<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 *
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */
class Mockery_LoaderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        spl_autoload_unregister('\Mockery\Loader::loadClass');
    }

    public function test_calling_register_registers_self_as_spl_autoloader_function()
    {
        require_once 'Mockery/Loader.php';
        $loader = new \Mockery\Loader;
        $loader->register();
        $expected = [$loader, 'loadClass'];
        $this->assertTrue(in_array($expected, spl_autoload_functions()));
    }

    protected function tearDown()
    {
        spl_autoload_unregister('\Mockery\Loader::loadClass');
        $loader = new \Mockery\Loader;
        $loader->register();
    }
}
