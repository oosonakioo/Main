<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests\Node;

use Symfony\Component\CssSelector\Node\NodeInterface;

abstract class AbstractNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider getToStringConversionTestData */
    public function test_to_string_conversion(NodeInterface $node, $representation)
    {
        $this->assertEquals($representation, (string) $node);
    }

    /** @dataProvider getSpecificityValueTestData */
    public function test_specificity_value(NodeInterface $node, $value)
    {
        $this->assertEquals($value, $node->getSpecificity()->getValue());
    }

    abstract public function getToStringConversionTestData();

    abstract public function getSpecificityValueTestData();
}
