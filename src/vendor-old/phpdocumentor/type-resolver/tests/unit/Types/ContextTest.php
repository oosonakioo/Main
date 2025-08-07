<?php

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Context
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getNamespace
     */
    public function test_provides_a_normalized_namespace()
    {
        $fixture = new Context('\My\Space');
        $this->assertSame('My\Space', $fixture->getNamespace());
    }

    /**
     * @covers ::__construct
     * @covers ::getNamespace
     */
    public function test_interprets_namespace_named_global_as_root_namespace()
    {
        $fixture = new Context('global');
        $this->assertSame('', $fixture->getNamespace());
    }

    /**
     * @covers ::__construct
     * @covers ::getNamespace
     */
    public function test_interprets_namespace_named_default_as_root_namespace()
    {
        $fixture = new Context('default');
        $this->assertSame('', $fixture->getNamespace());
    }

    /**
     * @covers ::__construct
     * @covers ::getNamespaceAliases
     */
    public function test_provides_normalized_namespace_aliases()
    {
        $fixture = new Context('', ['Space' => '\My\Space']);
        $this->assertSame(['Space' => 'My\Space'], $fixture->getNamespaceAliases());
    }
}
