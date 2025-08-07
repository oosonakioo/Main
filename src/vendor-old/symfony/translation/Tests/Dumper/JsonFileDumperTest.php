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

use Symfony\Component\Translation\Dumper\JsonFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class JsonFileDumperTest extends \PHPUnit_Framework_TestCase
{
    public function test_format_catalogue()
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => 'bar']);

        $dumper = new JsonFileDumper;

        $this->assertStringEqualsFile(__DIR__.'/../fixtures/resources.json', $dumper->formatCatalogue($catalogue, 'messages'));
    }

    public function test_dump_with_custom_encoding()
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => '"bar"']);

        $dumper = new JsonFileDumper;

        $this->assertStringEqualsFile(__DIR__.'/../fixtures/resources.dump.json', $dumper->formatCatalogue($catalogue, 'messages', ['json_encoding' => JSON_HEX_QUOT]));
    }
}
