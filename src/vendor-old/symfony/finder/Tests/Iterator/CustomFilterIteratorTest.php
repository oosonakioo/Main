<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests\Iterator;

use Symfony\Component\Finder\Iterator\CustomFilterIterator;

class CustomFilterIteratorTest extends IteratorTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_with_invalid_filter()
    {
        new CustomFilterIterator(new Iterator, ['foo']);
    }

    /**
     * @dataProvider getAcceptData
     */
    public function test_accept($filters, $expected)
    {
        $inner = new Iterator(['test.php', 'test.py', 'foo.php']);

        $iterator = new CustomFilterIterator($inner, $filters);

        $this->assertIterator($expected, $iterator);
    }

    public function getAcceptData()
    {
        return [
            [[function (\SplFileInfo $fileinfo) {
                return false;
            }], []],
            [[function (\SplFileInfo $fileinfo) {
                return strpos($fileinfo, 'test') === 0;
            }], ['test.php', 'test.py']],
            [['is_dir'], []],
        ];
    }
}
