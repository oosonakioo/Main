<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests;

use Symfony\Component\Routing\CompiledRoute;

class CompiledRouteTest extends \PHPUnit_Framework_TestCase
{
    public function test_accessors()
    {
        $compiled = new CompiledRoute('prefix', 'regex', ['tokens'], [], [], [], [], ['variables']);
        $this->assertEquals('prefix', $compiled->getStaticPrefix(), '__construct() takes a static prefix as its second argument');
        $this->assertEquals('regex', $compiled->getRegex(), '__construct() takes a regexp as its third argument');
        $this->assertEquals(['tokens'], $compiled->getTokens(), '__construct() takes an array of tokens as its fourth argument');
        $this->assertEquals(['variables'], $compiled->getVariables(), '__construct() takes an array of variables as its ninth argument');
    }
}
