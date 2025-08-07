<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Yaml\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class YamlTest extends TestCase
{
    public function test_parse_and_dump()
    {
        $data = ['lorem' => 'ipsum', 'dolor' => 'sit'];
        $yml = Yaml::dump($data);
        $parsed = Yaml::parse($yml);
        $this->assertEquals($data, $parsed);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The indentation must be greater than zero
     */
    public function test_zero_indentation_throws_exception()
    {
        Yaml::dump(['lorem' => 'ipsum', 'dolor' => 'sit'], 2, 0);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The indentation must be greater than zero
     */
    public function test_negative_indentation_throws_exception()
    {
        Yaml::dump(['lorem' => 'ipsum', 'dolor' => 'sit'], 2, -4);
    }
}
