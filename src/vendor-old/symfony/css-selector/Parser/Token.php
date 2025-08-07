<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Parser;

/**
 * CSS selector token.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class Token
{
    const TYPE_FILE_END = 'eof';

    const TYPE_DELIMITER = 'delimiter';

    const TYPE_WHITESPACE = 'whitespace';

    const TYPE_IDENTIFIER = 'identifier';

    const TYPE_HASH = 'hash';

    const TYPE_NUMBER = 'number';

    const TYPE_STRING = 'string';

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $position;

    /**
     * @param  int  $type
     * @param  string  $value
     * @param  int  $position
     */
    public function __construct($type, $value, $position)
    {
        $this->type = $type;
        $this->value = $value;
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isFileEnd()
    {
        return $this->type === self::TYPE_FILE_END;
    }

    /**
     * @return bool
     */
    public function isDelimiter(array $values = [])
    {
        if ($this->type !== self::TYPE_DELIMITER) {
            return false;
        }

        if (empty($values)) {
            return true;
        }

        return in_array($this->value, $values);
    }

    /**
     * @return bool
     */
    public function isWhitespace()
    {
        return $this->type === self::TYPE_WHITESPACE;
    }

    /**
     * @return bool
     */
    public function isIdentifier()
    {
        return $this->type === self::TYPE_IDENTIFIER;
    }

    /**
     * @return bool
     */
    public function isHash()
    {
        return $this->type === self::TYPE_HASH;
    }

    /**
     * @return bool
     */
    public function isNumber()
    {
        return $this->type === self::TYPE_NUMBER;
    }

    /**
     * @return bool
     */
    public function isString()
    {
        return $this->type === self::TYPE_STRING;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->value) {
            return sprintf('<%s "%s" at %s>', $this->type, $this->value, $this->position);
        }

        return sprintf('<%s at %s>', $this->type, $this->position);
    }
}
