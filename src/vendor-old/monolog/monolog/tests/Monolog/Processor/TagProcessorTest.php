<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\TestCase;

class TagProcessorTest extends TestCase
{
    /**
     * @covers Monolog\Processor\TagProcessor::__invoke
     */
    public function test_processor()
    {
        $tags = [1, 2, 3];
        $processor = new TagProcessor($tags);
        $record = $processor($this->getRecord());

        $this->assertEquals($tags, $record['extra']['tags']);
    }

    /**
     * @covers Monolog\Processor\TagProcessor::__invoke
     */
    public function test_processor_tag_modification()
    {
        $tags = [1, 2, 3];
        $processor = new TagProcessor($tags);

        $record = $processor($this->getRecord());
        $this->assertEquals($tags, $record['extra']['tags']);

        $processor->setTags(['a', 'b']);
        $record = $processor($this->getRecord());
        $this->assertEquals(['a', 'b'], $record['extra']['tags']);

        $processor->addTags(['a', 'c', 'foo' => 'bar']);
        $record = $processor($this->getRecord());
        $this->assertEquals(['a', 'b', 'a', 'c', 'foo' => 'bar'], $record['extra']['tags']);
    }
}
