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

use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class XliffFileDumperTest extends \PHPUnit_Framework_TestCase
{
    public function test_format_catalogue()
    {
        $catalogue = new MessageCatalogue('en_US');
        $catalogue->add([
            'foo' => 'bar',
            'key' => '',
            'key.with.cdata' => '<source> & <target>',
        ]);
        $catalogue->setMetadata('foo', ['notes' => [['priority' => 1, 'from' => 'bar', 'content' => 'baz']]]);
        $catalogue->setMetadata('key', ['notes' => [['content' => 'baz'], ['content' => 'qux']]]);

        $dumper = new XliffFileDumper;

        $this->assertStringEqualsFile(
            __DIR__.'/../fixtures/resources-clean.xlf',
            $dumper->formatCatalogue($catalogue, 'messages', ['default_locale' => 'fr_FR'])
        );
    }

    public function test_format_catalogue_xliff2()
    {
        $catalogue = new MessageCatalogue('en_US');
        $catalogue->add([
            'foo' => 'bar',
            'key' => '',
            'key.with.cdata' => '<source> & <target>',
        ]);
        $catalogue->setMetadata('key', ['target-attributes' => ['order' => 1]]);

        $dumper = new XliffFileDumper;

        $this->assertStringEqualsFile(
            __DIR__.'/../fixtures/resources-2.0-clean.xlf',
            $dumper->formatCatalogue($catalogue, 'messages', ['default_locale' => 'fr_FR', 'xliff_version' => '2.0'])
        );
    }

    public function test_format_catalogue_with_custom_tool_info()
    {
        $options = [
            'default_locale' => 'en_US',
            'tool_info' => ['tool-id' => 'foo', 'tool-name' => 'foo', 'tool-version' => '0.0', 'tool-company' => 'Foo'],
        ];

        $catalogue = new MessageCatalogue('en_US');
        $catalogue->add(['foo' => 'bar']);

        $dumper = new XliffFileDumper;

        $this->assertStringEqualsFile(
            __DIR__.'/../fixtures/resources-tool-info.xlf',
            $dumper->formatCatalogue($catalogue, 'messages', $options)
        );
    }

    public function test_format_catalogue_with_target_attributes_metadata()
    {
        $catalogue = new MessageCatalogue('en_US');
        $catalogue->add([
            'foo' => 'bar',
        ]);
        $catalogue->setMetadata('foo', ['target-attributes' => ['state' => 'needs-translation']]);

        $dumper = new XliffFileDumper;

        $this->assertStringEqualsFile(
            __DIR__.'/../fixtures/resources-target-attributes.xlf',
            $dumper->formatCatalogue($catalogue, 'messages', ['default_locale' => 'fr_FR'])
        );
    }
}
