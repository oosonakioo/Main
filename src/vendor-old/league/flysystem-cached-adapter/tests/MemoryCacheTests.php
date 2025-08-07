<?php

use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Util;

class MemoryCacheTests extends PHPUnit_Framework_TestCase
{
    public function test_autosave()
    {
        $cache = new Memory;
        $cache->setAutosave(true);
        $this->assertTrue($cache->getAutosave());
        $cache->setAutosave(false);
        $this->assertFalse($cache->getAutosave());
    }

    public function test_cache_miss()
    {
        $cache = new Memory;
        $cache->storeMiss('path.txt');
        $this->assertFalse($cache->has('path.txt'));
    }

    public function test_is_complete()
    {
        $cache = new Memory;
        $this->assertFalse($cache->isComplete('dirname', false));
        $cache->setComplete('dirname', false);
        $this->assertFalse($cache->isComplete('dirname', true));
        $cache->setComplete('dirname', true);
        $this->assertTrue($cache->isComplete('dirname', true));
    }

    public function test_clean_contents()
    {
        $cache = new Memory;
        $input = [[
            'path' => 'path.txt',
            'visibility' => 'public',
            'invalid' => 'thing',
        ]];

        $expected = [[
            'path' => 'path.txt',
            'visibility' => 'public',
        ]];

        $output = $cache->cleanContents($input);
        $this->assertEquals($expected, $output);
    }

    public function test_get_for_storage()
    {
        $cache = new Memory;
        $input = [[
            'path' => 'path.txt',
            'visibility' => 'public',
            'type' => 'file',
        ]];

        $cache->storeContents('', $input, true);
        $contents = $cache->listContents('', true);
        $cached = [];
        foreach ($contents as $item) {
            $cached[$item['path']] = $item;
        }

        $this->assertEquals(json_encode([$cached, ['' => 'recursive']]), $cache->getForStorage());
    }

    public function test_parent_complete_is_used_during_has()
    {
        $cache = new Memory;
        $cache->setComplete('dirname', false);
        $this->assertFalse($cache->has('dirname/path.txt'));
    }

    public function test_flush()
    {
        $cache = new Memory;
        $cache->setComplete('dirname', true);
        $cache->updateObject('path.txt', [
            'path' => 'path.txt',
            'visibility' => 'public',
        ]);
        $cache->flush();
        $this->assertFalse($cache->isComplete('dirname', true));
        $this->assertNull($cache->has('path.txt'));
    }

    public function test_set_from_storage()
    {
        $cache = new Memory;
        $json = [[
            'path.txt' => ['path' => 'path.txt', 'type' => 'file'],
        ], ['dirname' => 'recursive']];
        $jsonString = json_encode($json);
        $cache->setFromStorage($jsonString);
        $this->assertTrue($cache->has('path.txt'));
        $this->assertTrue($cache->isComplete('dirname', true));
    }

    public function test_get_metadata_fail()
    {
        $cache = new Memory;
        $this->assertFalse($cache->getMetadata('path.txt'));
    }

    public function metaGetterProvider()
    {
        return [
            ['getTimestamp', 'timestamp', 12344],
            ['getMimetype', 'mimetype', 'text/plain'],
            ['getSize', 'size', 12],
            ['getVisibility', 'visibility', 'private'],
            ['read', 'contents', '__contents__'],
        ];
    }

    /**
     * @dataProvider metaGetterProvider
     */
    public function test_meta_getters($method, $key, $value)
    {
        $cache = new Memory;
        $this->assertFalse($cache->{$method}('path.txt'));
        $cache->updateObject('path.txt', $object = [
            'path' => 'path.txt',
            'type' => 'file',
            $key => $value,
        ] + Util::pathinfo('path.txt'), true);
        $this->assertEquals($object, $cache->{$method}('path.txt'));
        $this->assertEquals($object, $cache->getMetadata('path.txt'));
    }

    public function test_get_derived_mimetype()
    {
        $cache = new Memory;
        $cache->updateObject('path.txt', [
            'contents' => 'something',
        ]);
        $response = $cache->getMimetype('path.txt');
        $this->assertEquals('text/plain', $response['mimetype']);
    }

    public function test_copy_fail()
    {
        $cache = new Memory;
        $cache->copy('one', 'two');
        $this->assertNull($cache->has('two'));
        $this->assertNull($cache->load());
    }

    public function test_store_contents()
    {
        $cache = new Memory;
        $cache->storeContents('dirname', [
            ['path' => 'dirname', 'type' => 'dir'],
            ['path' => 'dirname/nested', 'type' => 'dir'],
            ['path' => 'dirname/nested/deep', 'type' => 'dir'],
            ['path' => 'other/nested/deep', 'type' => 'dir'],
        ], true);

        $this->isTrue($cache->isComplete('other/nested', true));
    }

    public function test_delete()
    {
        $cache = new Memory;
        $cache->updateObject('path.txt', ['type' => 'file']);
        $this->assertTrue($cache->has('path.txt'));
        $cache->delete('path.txt');
        $this->assertFalse($cache->has('path.txt'));
    }

    public function test_delete_dir()
    {
        $cache = new Memory;
        $cache->storeContents('dirname', [
            ['path' => 'dirname/path.txt', 'type' => 'file'],
        ]);
        $this->assertTrue($cache->isComplete('dirname', false));
        $this->assertTrue($cache->has('dirname/path.txt'));
        $cache->deleteDir('dirname');
        $this->assertFalse($cache->isComplete('dirname', false));
        $this->assertNull($cache->has('dirname/path.txt'));
    }

    public function test_read_stream()
    {
        $cache = new Memory;
        $this->assertFalse($cache->readStream('path.txt'));
    }

    public function test_rename()
    {
        $cache = new Memory;
        $cache->updateObject('path.txt', ['type' => 'file']);
        $cache->rename('path.txt', 'newpath.txt');
        $this->assertTrue($cache->has('newpath.txt'));
    }

    public function test_copy()
    {
        $cache = new Memory;
        $cache->updateObject('path.txt', ['type' => 'file']);
        $cache->copy('path.txt', 'newpath.txt');
        $this->assertTrue($cache->has('newpath.txt'));
    }

    public function test_complext_list_contents()
    {
        $cache = new Memory;
        $cache->storeContents('', [
            ['path' => 'dirname', 'type' => 'dir'],
            ['path' => 'dirname/file.txt', 'type' => 'file'],
            ['path' => 'other', 'type' => 'dir'],
            ['path' => 'other/file.txt', 'type' => 'file'],
            ['path' => 'other/nested/file.txt', 'type' => 'file'],
        ]);

        $this->assertCount(3, $cache->listContents('other', true));
    }

    public function test_cache_miss_if_contents_is_false()
    {
        $cache = new Memory;
        $cache->updateObject('path.txt', [
            'path' => 'path.txt',
            'contents' => false,
        ], true);

        $this->assertFalse($cache->read('path.txt'));
    }
}
