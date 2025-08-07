<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests;

use Symfony\Component\Finder\Finder;

class FinderTest extends Iterator\RealIteratorTestCase
{
    public function test_create()
    {
        $this->assertInstanceOf('Symfony\Component\Finder\Finder', Finder::create());
    }

    public function test_directories()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->directories());
        $this->assertIterator($this->toAbsolute(['foo', 'toto']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->directories();
        $finder->files();
        $finder->directories();
        $this->assertIterator($this->toAbsolute(['foo', 'toto']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_files()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->files());
        $this->assertIterator($this->toAbsolute(['foo/bar.tmp', 'test.php', 'test.py', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->files();
        $finder->directories();
        $finder->files();
        $this->assertIterator($this->toAbsolute(['foo/bar.tmp', 'test.php', 'test.py', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_depth()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->depth('< 1'));
        $this->assertIterator($this->toAbsolute(['foo', 'test.php', 'test.py', 'toto', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->depth('<= 0'));
        $this->assertIterator($this->toAbsolute(['foo', 'test.php', 'test.py', 'toto', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->depth('>= 1'));
        $this->assertIterator($this->toAbsolute(['foo/bar.tmp']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->depth('< 1')->depth('>= 1');
        $this->assertIterator([], $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_name()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->name('*.php'));
        $this->assertIterator($this->toAbsolute(['test.php']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->name('test.ph*');
        $finder->name('test.py');
        $this->assertIterator($this->toAbsolute(['test.php', 'test.py']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->name('~^test~i');
        $this->assertIterator($this->toAbsolute(['test.php', 'test.py']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->name('~\\.php$~i');
        $this->assertIterator($this->toAbsolute(['test.php']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->name('test.p{hp,y}');
        $this->assertIterator($this->toAbsolute(['test.php', 'test.py']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_not_name()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->notName('*.php'));
        $this->assertIterator($this->toAbsolute(['foo', 'foo/bar.tmp', 'test.py', 'toto', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->notName('*.php');
        $finder->notName('*.py');
        $this->assertIterator($this->toAbsolute(['foo', 'foo/bar.tmp', 'toto', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->name('test.ph*');
        $finder->name('test.py');
        $finder->notName('*.php');
        $finder->notName('*.py');
        $this->assertIterator([], $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->name('test.ph*');
        $finder->name('test.py');
        $finder->notName('*.p{hp,y}');
        $this->assertIterator([], $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getRegexNameTestData
     */
    public function test_regex_name($regex)
    {
        $finder = $this->buildFinder();
        $finder->name($regex);
        $this->assertIterator($this->toAbsolute(['test.py', 'test.php']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_size()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->files()->size('< 1K')->size('> 500'));
        $this->assertIterator($this->toAbsolute(['test.php']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_date()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->files()->date('until last month'));
        $this->assertIterator($this->toAbsolute(['foo/bar.tmp', 'test.php']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_exclude()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->exclude('foo'));
        $this->assertIterator($this->toAbsolute(['test.php', 'test.py', 'toto', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_ignore_vcs()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->ignoreVCS(false)->ignoreDotFiles(false));
        $this->assertIterator($this->toAbsolute(['.git', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'toto/.git', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->ignoreVCS(false)->ignoreVCS(false)->ignoreDotFiles(false);
        $this->assertIterator($this->toAbsolute(['.git', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'toto/.git', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->ignoreVCS(true)->ignoreDotFiles(false));
        $this->assertIterator($this->toAbsolute(['foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_ignore_dot_files()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->ignoreDotFiles(false)->ignoreVCS(false));
        $this->assertIterator($this->toAbsolute(['.git', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'toto/.git', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $finder->ignoreDotFiles(false)->ignoreDotFiles(false)->ignoreVCS(false);
        $this->assertIterator($this->toAbsolute(['.git', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'toto/.git', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->ignoreDotFiles(true)->ignoreVCS(false));
        $this->assertIterator($this->toAbsolute(['foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_sort_by_name()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->sortByName());
        $this->assertIterator($this->toAbsolute(['foo', 'foo bar', 'foo/bar.tmp', 'test.php', 'test.py', 'toto']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_sort_by_type()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->sortByType());
        $this->assertIterator($this->toAbsolute(['foo', 'foo bar', 'toto', 'foo/bar.tmp', 'test.php', 'test.py']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_sort_by_accessed_time()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->sortByAccessedTime());
        $this->assertIterator($this->toAbsolute(['foo/bar.tmp', 'test.php', 'toto', 'test.py', 'foo', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_sort_by_changed_time()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->sortByChangedTime());
        $this->assertIterator($this->toAbsolute(['toto', 'test.py', 'test.php', 'foo/bar.tmp', 'foo', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_sort_by_modified_time()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->sortByModifiedTime());
        $this->assertIterator($this->toAbsolute(['foo/bar.tmp', 'test.php', 'toto', 'test.py', 'foo', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_sort()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
            return strcmp($a->getRealPath(), $b->getRealPath());
        }));
        $this->assertIterator($this->toAbsolute(['foo', 'foo bar', 'foo/bar.tmp', 'test.php', 'test.py', 'toto']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_filter()
    {
        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->filter(function (\SplFileInfo $f) {
            return strpos($f, 'test') !== false;
        }));
        $this->assertIterator($this->toAbsolute(['test.php', 'test.py']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_follow_links()
    {
        if ('\\' == DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('symlinks are not supported on Windows');
        }

        $finder = $this->buildFinder();
        $this->assertSame($finder, $finder->followLinks());
        $this->assertIterator($this->toAbsolute(['foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar']), $finder->in(self::$tmpDir)->getIterator());
    }

    public function test_in()
    {
        $finder = $this->buildFinder();
        $iterator = $finder->files()->name('*.php')->depth('< 1')->in([self::$tmpDir, __DIR__])->getIterator();

        $expected = [
            self::$tmpDir.DIRECTORY_SEPARATOR.'test.php',
            __DIR__.DIRECTORY_SEPARATOR.'FinderTest.php',
            __DIR__.DIRECTORY_SEPARATOR.'GlobTest.php',
        ];

        $this->assertIterator($expected, $iterator);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_in_with_non_existent_directory()
    {
        $finder = new Finder;
        $finder->in('foobar');
    }

    public function test_in_with_glob()
    {
        $finder = $this->buildFinder();
        $finder->in([__DIR__.'/Fixtures/*/B/C', __DIR__.'/Fixtures/*/*/B/C'])->getIterator();

        $this->assertIterator($this->toAbsoluteFixtures(['A/B/C/abc.dat', 'copy/A/B/C/abc.dat.copy']), $finder);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_in_with_non_directory_glob()
    {
        $finder = new Finder;
        $finder->in(__DIR__.'/Fixtures/A/a*');
    }

    public function test_in_with_glob_brace()
    {
        $finder = $this->buildFinder();
        $finder->in([__DIR__.'/Fixtures/{A,copy/A}/B/C'])->getIterator();

        $this->assertIterator($this->toAbsoluteFixtures(['A/B/C/abc.dat', 'copy/A/B/C/abc.dat.copy']), $finder);
    }

    /**
     * @expectedException \LogicException
     */
    public function test_get_iterator_without_in()
    {
        $finder = Finder::create();
        $finder->getIterator();
    }

    public function test_get_iterator()
    {
        $finder = $this->buildFinder();
        $dirs = [];
        foreach ($finder->directories()->in(self::$tmpDir) as $dir) {
            $dirs[] = (string) $dir;
        }

        $expected = $this->toAbsolute(['foo', 'toto']);

        sort($dirs);
        sort($expected);

        $this->assertEquals($expected, $dirs, 'implements the \IteratorAggregate interface');

        $finder = $this->buildFinder();
        $this->assertEquals(2, iterator_count($finder->directories()->in(self::$tmpDir)), 'implements the \IteratorAggregate interface');

        $finder = $this->buildFinder();
        $a = iterator_to_array($finder->directories()->in(self::$tmpDir));
        $a = array_values(array_map(function ($a) {
            return (string) $a;
        }, $a));
        sort($a);
        $this->assertEquals($expected, $a, 'implements the \IteratorAggregate interface');
    }

    public function test_relative_path()
    {
        $finder = $this->buildFinder()->in(self::$tmpDir);

        $paths = [];

        foreach ($finder as $file) {
            $paths[] = $file->getRelativePath();
        }

        $ref = ['', '', '', '', 'foo', ''];

        sort($ref);
        sort($paths);

        $this->assertEquals($ref, $paths);
    }

    public function test_relative_pathname()
    {
        $finder = $this->buildFinder()->in(self::$tmpDir)->sortByName();

        $paths = [];

        foreach ($finder as $file) {
            $paths[] = $file->getRelativePathname();
        }

        $ref = ['test.php', 'toto', 'test.py', 'foo', 'foo'.DIRECTORY_SEPARATOR.'bar.tmp', 'foo bar'];

        sort($paths);
        sort($ref);

        $this->assertEquals($ref, $paths);
    }

    public function test_append_with_a_finder()
    {
        $finder = $this->buildFinder();
        $finder->files()->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder1 = $this->buildFinder();
        $finder1->directories()->in(self::$tmpDir);

        $finder = $finder->append($finder1);

        $this->assertIterator($this->toAbsolute(['foo', 'foo/bar.tmp', 'toto']), $finder->getIterator());
    }

    public function test_append_with_an_array()
    {
        $finder = $this->buildFinder();
        $finder->files()->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder->append($this->toAbsolute(['foo', 'toto']));

        $this->assertIterator($this->toAbsolute(['foo', 'foo/bar.tmp', 'toto']), $finder->getIterator());
    }

    public function test_append_returns_a_finder()
    {
        $this->assertInstanceOf('Symfony\\Component\\Finder\\Finder', Finder::create()->append([]));
    }

    public function test_append_does_not_require_in()
    {
        $finder = $this->buildFinder();
        $finder->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder1 = Finder::create()->append($finder);

        $this->assertIterator(iterator_to_array($finder->getIterator()), $finder1->getIterator());
    }

    public function test_count_directories()
    {
        $directory = Finder::create()->directories()->in(self::$tmpDir);
        $i = 0;

        foreach ($directory as $dir) {
            $i++;
        }

        $this->assertCount($i, $directory);
    }

    public function test_count_files()
    {
        $files = Finder::create()->files()->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures');
        $i = 0;

        foreach ($files as $file) {
            $i++;
        }

        $this->assertCount($i, $files);
    }

    /**
     * @expectedException \LogicException
     */
    public function test_count_without_in()
    {
        $finder = Finder::create()->files();
        count($finder);
    }

    /**
     * @dataProvider getContainsTestData
     */
    public function test_contains($matchPatterns, $noMatchPatterns, $expected)
    {
        $finder = $this->buildFinder();
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures')
            ->name('*.txt')->sortByName()
            ->contains($matchPatterns)
            ->notContains($noMatchPatterns);

        $this->assertIterator($this->toAbsoluteFixtures($expected), $finder);
    }

    public function test_contains_on_directory()
    {
        $finder = $this->buildFinder();
        $finder->in(__DIR__)
            ->directories()
            ->name('Fixtures')
            ->contains('abc');
        $this->assertIterator([], $finder);
    }

    public function test_not_contains_on_directory()
    {
        $finder = $this->buildFinder();
        $finder->in(__DIR__)
            ->directories()
            ->name('Fixtures')
            ->notContains('abc');
        $this->assertIterator([], $finder);
    }

    /**
     * Searching in multiple locations involves AppendIterator which does an unnecessary rewind which leaves FilterIterator
     * with inner FilesystemIterator in an invalid state.
     *
     * @see https://bugs.php.net/68557
     */
    public function test_multiple_locations()
    {
        $locations = [
            self::$tmpDir.'/',
            self::$tmpDir.'/toto/',
        ];

        // it is expected that there are test.py test.php in the tmpDir
        $finder = new Finder;
        $finder->in($locations)
            // the default flag IGNORE_DOT_FILES fixes the problem indirectly
            // so we set it to false for better isolation
            ->ignoreDotFiles(false)
            ->depth('< 1')->name('test.php');

        $this->assertCount(1, $finder);
    }

    /**
     * Searching in multiple locations with sub directories involves
     * AppendIterator which does an unnecessary rewind which leaves
     * FilterIterator with inner FilesystemIterator in an invalid state.
     *
     * @see https://bugs.php.net/68557
     */
    public function test_multiple_locations_with_sub_directories()
    {
        $locations = [
            __DIR__.'/Fixtures/one',
            self::$tmpDir.DIRECTORY_SEPARATOR.'toto',
        ];

        $finder = $this->buildFinder();
        $finder->in($locations)->depth('< 10')->name('*.neon');

        $expected = [
            __DIR__.'/Fixtures/one'.DIRECTORY_SEPARATOR.'b'.DIRECTORY_SEPARATOR.'c.neon',
            __DIR__.'/Fixtures/one'.DIRECTORY_SEPARATOR.'b'.DIRECTORY_SEPARATOR.'d.neon',
        ];

        $this->assertIterator($expected, $finder);
        $this->assertIteratorInForeach($expected, $finder);
    }

    /**
     * Iterator keys must be the file pathname.
     */
    public function test_iterator_keys()
    {
        $finder = $this->buildFinder()->in(self::$tmpDir);
        foreach ($finder as $key => $file) {
            $this->assertEquals($file->getPathname(), $key);
        }
    }

    public function test_regex_special_chars_location_with_path_restriction_containing_start_flag()
    {
        $finder = $this->buildFinder();
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'r+e.gex[c]a(r)s')
            ->path('/^dir/');

        $expected = ['r+e.gex[c]a(r)s'.DIRECTORY_SEPARATOR.'dir', 'r+e.gex[c]a(r)s'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'bar.dat'];
        $this->assertIterator($this->toAbsoluteFixtures($expected), $finder);
    }

    public function getContainsTestData()
    {
        return [
            ['', '', []],
            ['foo', 'bar', []],
            ['', 'foobar', ['dolor.txt', 'ipsum.txt', 'lorem.txt']],
            ['lorem ipsum dolor sit amet', 'foobar', ['lorem.txt']],
            ['sit', 'bar', ['dolor.txt', 'ipsum.txt', 'lorem.txt']],
            ['dolor sit amet', '@^L@m', ['dolor.txt', 'ipsum.txt']],
            ['/^lorem ipsum dolor sit amet$/m', 'foobar', ['lorem.txt']],
            ['lorem', 'foobar', ['lorem.txt']],
            ['', 'lorem', ['dolor.txt', 'ipsum.txt']],
            ['ipsum dolor sit amet', '/^IPSUM/m', ['lorem.txt']],
        ];
    }

    public function getRegexNameTestData()
    {
        return [
            ['~.+\\.p.+~i'],
            ['~t.*s~i'],
        ];
    }

    /**
     * @dataProvider getTestPathData
     */
    public function test_path($matchPatterns, $noMatchPatterns, array $expected)
    {
        $finder = $this->buildFinder();
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures')
            ->path($matchPatterns)
            ->notPath($noMatchPatterns);

        $this->assertIterator($this->toAbsoluteFixtures($expected), $finder);
    }

    public function getTestPathData()
    {
        return [
            ['', '', []],
            ['/^A\/B\/C/', '/C$/',
                ['A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat'],
            ],
            ['/^A\/B/', 'foobar',
                [
                    'A'.DIRECTORY_SEPARATOR.'B',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                ],
            ],
            ['A/B/C', 'foobar',
                [
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat.copy',
                ],
            ],
            ['A/B', 'foobar',
                [
                    // dirs
                    'A'.DIRECTORY_SEPARATOR.'B',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    // files
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat.copy',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat.copy',
                ],
            ],
            ['/^with space\//', 'foobar',
                [
                    'with space'.DIRECTORY_SEPARATOR.'foo.txt',
                ],
            ],
        ];
    }

    public function test_access_denied_exception()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('chmod is not supported on Windows');
        }

        $finder = $this->buildFinder();
        $finder->files()->in(self::$tmpDir);

        // make 'foo' directory non-readable
        $testDir = self::$tmpDir.DIRECTORY_SEPARATOR.'foo';
        chmod($testDir, 0333);

        if (false === $couldRead = is_readable($testDir)) {
            try {
                $this->assertIterator($this->toAbsolute(['foo bar', 'test.php', 'test.py']), $finder->getIterator());
                $this->fail('Finder should throw an exception when opening a non-readable directory.');
            } catch (\Exception $e) {
                $expectedExceptionClass = 'Symfony\\Component\\Finder\\Exception\\AccessDeniedException';
                if ($e instanceof \PHPUnit_Framework_ExpectationFailedException) {
                    $this->fail(sprintf("Expected exception:\n%s\nGot:\n%s\nWith comparison failure:\n%s", $expectedExceptionClass, 'PHPUnit_Framework_ExpectationFailedException', $e->getComparisonFailure()->getExpectedAsString()));
                }

                $this->assertInstanceOf($expectedExceptionClass, $e);
            }
        }

        // restore original permissions
        chmod($testDir, 0777);
        clearstatcache($testDir);

        if ($couldRead) {
            $this->markTestSkipped('could read test files while test requires unreadable');
        }
    }

    public function test_ignored_access_denied_exception()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('chmod is not supported on Windows');
        }

        $finder = $this->buildFinder();
        $finder->files()->ignoreUnreadableDirs()->in(self::$tmpDir);

        // make 'foo' directory non-readable
        $testDir = self::$tmpDir.DIRECTORY_SEPARATOR.'foo';
        chmod($testDir, 0333);

        if (false === ($couldRead = is_readable($testDir))) {
            $this->assertIterator($this->toAbsolute(['foo bar', 'test.php', 'test.py']), $finder->getIterator());
        }

        // restore original permissions
        chmod($testDir, 0777);
        clearstatcache($testDir);

        if ($couldRead) {
            $this->markTestSkipped('could read test files while test requires unreadable');
        }
    }

    protected function buildFinder()
    {
        return Finder::create();
    }
}
