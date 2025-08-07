<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Dumper;

use Symfony\Component\Translation\Dumper\PhpFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class PhpFileDumperTest extends \PHPUnit_Framework_TestCase
{
    public function test_format_catalogue()
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => 'bar']);

        $dumper = new PhpFileDumper;

        $this->assertStringEqualsFile(__DIR__.'/../fixtures/resources.php', $dumper->formatCatalogue($catalogue, 'messages'));
    }
}
