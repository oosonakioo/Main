<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DomCrawler\Field;

/**
 * InputFormField represents an input form field (an HTML input tag).
 *
 * For inputs with type of file, checkbox, or radio, there are other more
 * specialized classes (cf. FileFormField and ChoiceFormField).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class InputFormField extends FormField
{
    /**
     * Initializes the form field.
     *
     * @throws \LogicException When node type is incorrect
     */
    protected function initialize()
    {
        if ($this->node->nodeName !== 'input' && $this->node->nodeName !== 'button') {
            throw new \LogicException(sprintf('An InputFormField can only be created from an input or button tag (%s given).', $this->node->nodeName));
        }

        if (strtolower($this->node->getAttribute('type')) === 'checkbox') {
            throw new \LogicException('Checkboxes should be instances of ChoiceFormField.');
        }

        if (strtolower($this->node->getAttribute('type')) === 'file') {
            throw new \LogicException('File inputs should be instances of FileFormField.');
        }

        $this->value = $this->node->getAttribute('value');
    }
}
