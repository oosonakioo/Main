<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DomCrawler\Tests\Field;

use Symfony\Component\DomCrawler\Field\InputFormField;

class FormFieldTest extends FormFieldTestCase
{
    public function test_get_name()
    {
        $node = $this->createNode('input', '', ['type' => 'text', 'name' => 'name', 'value' => 'value']);
        $field = new InputFormField($node);

        $this->assertEquals('name', $field->getName(), '->getName() returns the name of the field');
    }

    public function test_get_set_has_value()
    {
        $node = $this->createNode('input', '', ['type' => 'text', 'name' => 'name', 'value' => 'value']);
        $field = new InputFormField($node);

        $this->assertEquals('value', $field->getValue(), '->getValue() returns the value of the field');

        $field->setValue('foo');
        $this->assertEquals('foo', $field->getValue(), '->setValue() sets the value of the field');

        $this->assertTrue($field->hasValue(), '->hasValue() always returns true');
    }
}
