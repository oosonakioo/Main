<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @since      Class available since Release 2.0.0
 */
class Framework_AssertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $filesDirectory;

    protected function setUp()
    {
        $this->filesDirectory = dirname(__DIR__).DIRECTORY_SEPARATOR.'_files'.DIRECTORY_SEPARATOR;
    }

    /**
     * @covers PHPUnit_Framework_Assert::fail
     */
    public function test_fail()
    {
        try {
            $this->fail();
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        throw new PHPUnit_Framework_AssertionFailedError('Fail did not throw fail exception');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContains
     */
    public function test_assert_spl_object_storage_contains_object()
    {
        $a = new stdClass;
        $b = new stdClass;
        $c = new SplObjectStorage;
        $c->attach($a);

        $this->assertContains($a, $c);

        try {
            $this->assertContains($b, $c);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContains
     */
    public function test_assert_array_contains_object()
    {
        $a = new stdClass;
        $b = new stdClass;

        $this->assertContains($a, [$a]);

        try {
            $this->assertContains($a, [$b]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContains
     */
    public function test_assert_array_contains_string()
    {
        $this->assertContains('foo', ['foo']);

        try {
            $this->assertContains('foo', ['bar']);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContains
     */
    public function test_assert_array_contains_non_object()
    {
        $this->assertContains('foo', [true]);

        try {
            $this->assertContains('foo', [true], '', false, true, true);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContainsOnlyInstancesOf
     */
    public function test_assert_contains_only_instances_of()
    {
        $test = [
            new Book,
            new Book,
        ];
        $this->assertContainsOnlyInstancesOf('Book', $test);
        $this->assertContainsOnlyInstancesOf('stdClass', [new stdClass]);

        $test2 = [
            new Author('Test'),
        ];
        try {
            $this->assertContainsOnlyInstancesOf('Book', $test2);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }
        $this->fail();
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertArrayHasKey
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_array_has_key_throws_exception_for_invalid_first_argument()
    {
        $this->assertArrayHasKey(null, []);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertArrayHasKey
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_array_has_key_throws_exception_for_invalid_second_argument()
    {
        $this->assertArrayHasKey(0, null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayHasKey
     */
    public function test_assert_array_has_integer_key()
    {
        $this->assertArrayHasKey(0, ['foo']);

        try {
            $this->assertArrayHasKey(1, ['foo']);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArraySubset
     * @covers PHPUnit_Framework_Constraint_ArraySubset
     */
    public function test_assert_array_subset()
    {
        $array = [
            'a' => 'item a',
            'b' => 'item b',
            'c' => ['a2' => 'item a2', 'b2' => 'item b2'],
            'd' => ['a2' => ['a3' => 'item a3', 'b3' => 'item b3']],
        ];

        $this->assertArraySubset(['a' => 'item a', 'c' => ['a2' => 'item a2']], $array);
        $this->assertArraySubset(['a' => 'item a', 'd' => ['a2' => ['b3' => 'item b3']]], $array);

        $arrayAccessData = new ArrayObject($array);

        $this->assertArraySubset(['a' => 'item a', 'c' => ['a2' => 'item a2']], $arrayAccessData);
        $this->assertArraySubset(['a' => 'item a', 'd' => ['a2' => ['b3' => 'item b3']]], $arrayAccessData);

        try {
            $this->assertArraySubset(['a' => 'bad value'], $array);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
        }

        try {
            $this->assertArraySubset(['d' => ['a2' => ['bad index' => 'item b3']]], $array);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArraySubset
     * @covers PHPUnit_Framework_Constraint_ArraySubset
     */
    public function test_assert_array_subset_with_deep_nested_arrays()
    {
        $array = [
            'path' => [
                'to' => [
                    'the' => [
                        'cake' => 'is a lie',
                    ],
                ],
            ],
        ];

        $this->assertArraySubset(['path' => []], $array);
        $this->assertArraySubset(['path' => ['to' => []]], $array);
        $this->assertArraySubset(['path' => ['to' => ['the' => []]]], $array);
        $this->assertArraySubset(['path' => ['to' => ['the' => ['cake' => 'is a lie']]]], $array);

        try {
            $this->assertArraySubset(['path' => ['to' => ['the' => ['cake' => 'is not a lie']]]], $array);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArraySubset
     * @covers PHPUnit_Framework_Constraint_ArraySubset
     */
    public function test_assert_array_subset_with_no_strict_check_and_objects()
    {
        $obj = new \stdClass;
        $reference = &$obj;
        $array = ['a' => $obj];

        $this->assertArraySubset(['a' => $reference], $array);
        $this->assertArraySubset(['a' => new \stdClass], $array);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArraySubset
     * @covers PHPUnit_Framework_Constraint_ArraySubset
     */
    public function test_assert_array_subset_with_strict_check_and_objects()
    {
        $obj = new \stdClass;
        $reference = &$obj;
        $array = ['a' => $obj];

        $this->assertArraySubset(['a' => $reference], $array, true);

        try {
            $this->assertArraySubset(['a' => new \stdClass], $array, true);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail('Strict recursive array check fail.');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArraySubset
     * @covers PHPUnit_Framework_Constraint_ArraySubset
     *
     * @expectedException PHPUnit_Framework_Exception
     *
     * @expectedExceptionMessage array or ArrayAccess
     *
     * @dataProvider assertArraySubsetInvalidArgumentProvider
     */
    public function test_assert_array_subset_raises_exception_for_invalid_arguments($partial, $subject)
    {
        $this->assertArraySubset($partial, $subject);
    }

    /**
     * @return array
     */
    public function assertArraySubsetInvalidArgumentProvider()
    {
        return [
            [false, []],
            [[], false],
        ];
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertArrayNotHasKey
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_array_not_has_key_throws_exception_for_invalid_first_argument()
    {
        $this->assertArrayNotHasKey(null, []);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertArrayNotHasKey
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_array_not_has_key_throws_exception_for_invalid_second_argument()
    {
        $this->assertArrayNotHasKey(0, null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayNotHasKey
     */
    public function test_assert_array_not_has_integer_key()
    {
        $this->assertArrayNotHasKey(1, ['foo']);

        try {
            $this->assertArrayNotHasKey(0, ['foo']);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayHasKey
     */
    public function test_assert_array_has_string_key()
    {
        $this->assertArrayHasKey('foo', ['foo' => 'bar']);

        try {
            $this->assertArrayHasKey('bar', ['foo' => 'bar']);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayNotHasKey
     */
    public function test_assert_array_not_has_string_key()
    {
        $this->assertArrayNotHasKey('bar', ['foo' => 'bar']);

        try {
            $this->assertArrayNotHasKey('foo', ['foo' => 'bar']);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayHasKey
     */
    public function test_assert_array_has_key_accepts_array_object_value()
    {
        $array = new ArrayObject;
        $array['foo'] = 'bar';
        $this->assertArrayHasKey('foo', $array);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayHasKey
     *
     * @expectedException PHPUnit_Framework_AssertionFailedError
     */
    public function test_assert_array_has_key_properly_fails_with_array_object_value()
    {
        $array = new ArrayObject;
        $array['bar'] = 'bar';
        $this->assertArrayHasKey('foo', $array);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayHasKey
     */
    public function test_assert_array_has_key_accepts_array_access_value()
    {
        $array = new SampleArrayAccess;
        $array['foo'] = 'bar';
        $this->assertArrayHasKey('foo', $array);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayHasKey
     *
     * @expectedException PHPUnit_Framework_AssertionFailedError
     */
    public function test_assert_array_has_key_properly_fails_with_array_access_value()
    {
        $array = new SampleArrayAccess;
        $array['bar'] = 'bar';
        $this->assertArrayHasKey('foo', $array);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayNotHasKey
     */
    public function test_assert_array_not_has_key_accepts_array_access_value()
    {
        $array = new ArrayObject;
        $array['foo'] = 'bar';
        $this->assertArrayNotHasKey('bar', $array);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertArrayNotHasKey
     *
     * @expectedException PHPUnit_Framework_AssertionFailedError
     */
    public function test_assert_array_not_has_key_propertly_fails_with_array_access_value()
    {
        $array = new ArrayObject;
        $array['bar'] = 'bar';
        $this->assertArrayNotHasKey('bar', $array);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertContains
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_contains_throws_exception()
    {
        $this->assertContains(null, null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContains
     */
    public function test_assert_iterator_contains_object()
    {
        $foo = new stdClass;

        $this->assertContains($foo, new TestIterator([$foo]));

        try {
            $this->assertContains($foo, new TestIterator([new stdClass]));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContains
     */
    public function test_assert_iterator_contains_string()
    {
        $this->assertContains('foo', new TestIterator(['foo']));

        try {
            $this->assertContains('foo', new TestIterator(['bar']));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContains
     */
    public function test_assert_string_contains_string()
    {
        $this->assertContains('foo', 'foobar');

        try {
            $this->assertContains('foo', 'bar');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertNotContains
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_contains_throws_exception()
    {
        $this->assertNotContains(null, null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotContains
     */
    public function test_assert_spl_object_storage_not_contains_object()
    {
        $a = new stdClass;
        $b = new stdClass;
        $c = new SplObjectStorage;
        $c->attach($a);

        $this->assertNotContains($b, $c);

        try {
            $this->assertNotContains($a, $c);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotContains
     */
    public function test_assert_array_not_contains_object()
    {
        $a = new stdClass;
        $b = new stdClass;

        $this->assertNotContains($a, [$b]);

        try {
            $this->assertNotContains($a, [$a]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotContains
     */
    public function test_assert_array_not_contains_string()
    {
        $this->assertNotContains('foo', ['bar']);

        try {
            $this->assertNotContains('foo', ['foo']);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotContains
     */
    public function test_assert_array_not_contains_non_object()
    {
        $this->assertNotContains('foo', [true], '', false, true, true);

        try {
            $this->assertNotContains('foo', [true]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotContains
     */
    public function test_assert_string_not_contains_string()
    {
        $this->assertNotContains('foo', 'bar');

        try {
            $this->assertNotContains('foo', 'foo');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertContainsOnly
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_contains_only_throws_exception()
    {
        $this->assertContainsOnly(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertNotContainsOnly
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_contains_only_throws_exception()
    {
        $this->assertNotContainsOnly(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertContainsOnlyInstancesOf
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_contains_only_instances_of_throws_exception()
    {
        $this->assertContainsOnlyInstancesOf(null, null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContainsOnly
     */
    public function test_assert_array_contains_only_integers()
    {
        $this->assertContainsOnly('integer', [1, 2, 3]);

        try {
            $this->assertContainsOnly('integer', ['1', 2, 3]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotContainsOnly
     */
    public function test_assert_array_not_contains_only_integers()
    {
        $this->assertNotContainsOnly('integer', ['1', 2, 3]);

        try {
            $this->assertNotContainsOnly('integer', [1, 2, 3]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertContainsOnly
     */
    public function test_assert_array_contains_only_std_class()
    {
        $this->assertContainsOnly('StdClass', [new stdClass]);

        try {
            $this->assertContainsOnly('StdClass', ['StdClass']);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotContainsOnly
     */
    public function test_assert_array_not_contains_only_std_class()
    {
        $this->assertNotContainsOnly('StdClass', ['StdClass']);

        try {
            $this->assertNotContainsOnly('StdClass', [new stdClass]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    protected function sameValues()
    {
        $object = new SampleClass(4, 8, 15);
        // cannot use $filesDirectory, because neither setUp() nor
        // setUpBeforeClass() are executed before the data providers
        $file = dirname(__DIR__).DIRECTORY_SEPARATOR.'_files'.DIRECTORY_SEPARATOR.'foo.xml';
        $resource = fopen($file, 'r');

        return [
            // null
            [null, null],
            // strings
            ['a', 'a'],
            // integers
            [0, 0],
            // floats
            [2.3, 2.3],
            [1 / 3, 1 - 2 / 3],
            [log(0), log(0)],
            // arrays
            [[], []],
            [[0 => 1], [0 => 1]],
            [[0 => null], [0 => null]],
            [['a', 'b' => [1, 2]], ['a', 'b' => [1, 2]]],
            // objects
            [$object, $object],
            // resources
            [$resource, $resource],
        ];
    }

    protected function notEqualValues()
    {
        // cyclic dependencies
        $book1 = new Book;
        $book1->author = new Author('Terry Pratchett');
        $book1->author->books[] = $book1;
        $book2 = new Book;
        $book2->author = new Author('Terry Pratch');
        $book2->author->books[] = $book2;

        $book3 = new Book;
        $book3->author = 'Terry Pratchett';
        $book4 = new stdClass;
        $book4->author = 'Terry Pratchett';

        $object1 = new SampleClass(4, 8, 15);
        $object2 = new SampleClass(16, 23, 42);
        $object3 = new SampleClass(4, 8, 15);
        $storage1 = new SplObjectStorage;
        $storage1->attach($object1);
        $storage2 = new SplObjectStorage;
        $storage2->attach($object3); // same content, different object

        // cannot use $filesDirectory, because neither setUp() nor
        // setUpBeforeClass() are executed before the data providers
        $file = dirname(__DIR__).DIRECTORY_SEPARATOR.'_files'.DIRECTORY_SEPARATOR.'foo.xml';

        return [
            // strings
            ['a', 'b'],
            ['a', 'A'],
            // https://github.com/sebastianbergmann/phpunit/issues/1023
            ['9E6666666', '9E7777777'],
            // integers
            [1, 2],
            [2, 1],
            // floats
            [2.3, 4.2],
            [2.3, 4.2, 0.5],
            [[2.3], [4.2], 0.5],
            [[[2.3]], [[4.2]], 0.5],
            [new Struct(2.3), new Struct(4.2), 0.5],
            [[new Struct(2.3)], [new Struct(4.2)], 0.5],
            // NAN
            [NAN, NAN],
            // arrays
            [[], [0 => 1]],
            [[0 => 1], []],
            [[0 => null], []],
            [[0 => 1, 1 => 2], [0 => 1, 1 => 3]],
            [['a', 'b' => [1, 2]], ['a', 'b' => [2, 1]]],
            // objects
            [new SampleClass(4, 8, 15), new SampleClass(16, 23, 42)],
            [$object1, $object2],
            [$book1, $book2],
            [$book3, $book4], // same content, different class
            // resources
            [fopen($file, 'r'), fopen($file, 'r')],
            // SplObjectStorage
            [$storage1, $storage2],
            // DOMDocument
            [
                PHPUnit_Util_XML::load('<root></root>'),
                PHPUnit_Util_XML::load('<bar/>'),
            ],
            [
                PHPUnit_Util_XML::load('<foo attr1="bar"/>'),
                PHPUnit_Util_XML::load('<foo attr1="foobar"/>'),
            ],
            [
                PHPUnit_Util_XML::load('<foo> bar </foo>'),
                PHPUnit_Util_XML::load('<foo />'),
            ],
            [
                PHPUnit_Util_XML::load('<foo xmlns="urn:myns:bar"/>'),
                PHPUnit_Util_XML::load('<foo xmlns="urn:notmyns:bar"/>'),
            ],
            [
                PHPUnit_Util_XML::load('<foo> bar </foo>'),
                PHPUnit_Util_XML::load('<foo> bir </foo>'),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 03:13:35', new DateTimeZone('America/New_York')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 03:13:35', new DateTimeZone('America/New_York')),
                3500,
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 05:13:35', new DateTimeZone('America/New_York')),
                3500,
            ],
            [
                new DateTime('2013-03-29', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
            ],
            [
                new DateTime('2013-03-29', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
                43200,
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/Chicago')),
                3500,
            ],
            [
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-30', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-29T05:13:35-0600'),
                new DateTime('2013-03-29T04:13:35-0600'),
            ],
            [
                new DateTime('2013-03-29T05:13:35-0600'),
                new DateTime('2013-03-29T05:13:35-0500'),
            ],
            // Exception
            // array(new Exception('Exception 1'), new Exception('Exception 2')),
            // different types
            [new SampleClass(4, 8, 15), false],
            [false, new SampleClass(4, 8, 15)],
            [[0 => 1, 1 => 2], false],
            [false, [0 => 1, 1 => 2]],
            [[], new stdClass],
            [new stdClass, []],
            // PHP: 0 == 'Foobar' => true!
            // We want these values to differ
            [0, 'Foobar'],
            ['Foobar', 0],
            [3, acos(8)],
            [acos(8), 3],
        ];
    }

    protected function equalValues()
    {
        // cyclic dependencies
        $book1 = new Book;
        $book1->author = new Author('Terry Pratchett');
        $book1->author->books[] = $book1;
        $book2 = new Book;
        $book2->author = new Author('Terry Pratchett');
        $book2->author->books[] = $book2;

        $object1 = new SampleClass(4, 8, 15);
        $object2 = new SampleClass(4, 8, 15);
        $storage1 = new SplObjectStorage;
        $storage1->attach($object1);
        $storage2 = new SplObjectStorage;
        $storage2->attach($object1);

        return [
            // strings
            ['a', 'A', 0, false, true], // ignore case
            // arrays
            [['a' => 1, 'b' => 2], ['b' => 2, 'a' => 1]],
            [[1], ['1']],
            [[3, 2, 1], [2, 3, 1], 0, true], // canonicalized comparison
            // floats
            [2.3, 2.5, 0.5],
            [[2.3], [2.5], 0.5],
            [[[2.3]], [[2.5]], 0.5],
            [new Struct(2.3), new Struct(2.5), 0.5],
            [[new Struct(2.3)], [new Struct(2.5)], 0.5],
            // numeric with delta
            [1, 2, 1],
            // objects
            [$object1, $object2],
            [$book1, $book2],
            // SplObjectStorage
            [$storage1, $storage2],
            // DOMDocument
            [
                PHPUnit_Util_XML::load('<root></root>'),
                PHPUnit_Util_XML::load('<root/>'),
            ],
            [
                PHPUnit_Util_XML::load('<root attr="bar"></root>'),
                PHPUnit_Util_XML::load('<root attr="bar"/>'),
            ],
            [
                PHPUnit_Util_XML::load('<root><foo attr="bar"></foo></root>'),
                PHPUnit_Util_XML::load('<root><foo attr="bar"/></root>'),
            ],
            [
                PHPUnit_Util_XML::load("<root>\n  <child/>\n</root>"),
                PHPUnit_Util_XML::load('<root><child/></root>'),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:13:25', new DateTimeZone('America/New_York')),
                10,
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 04:14:40', new DateTimeZone('America/New_York')),
                65,
            ],
            [
                new DateTime('2013-03-29', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29', new DateTimeZone('America/New_York')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 03:13:35', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 03:13:49', new DateTimeZone('America/Chicago')),
                15,
            ],
            [
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 23:00:00', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-30', new DateTimeZone('America/New_York')),
                new DateTime('2013-03-29 23:01:30', new DateTimeZone('America/Chicago')),
                100,
            ],
            [
                new DateTime('@1364616000'),
                new DateTime('2013-03-29 23:00:00', new DateTimeZone('America/Chicago')),
            ],
            [
                new DateTime('2013-03-29T05:13:35-0500'),
                new DateTime('2013-03-29T04:13:35-0600'),
            ],
            // Exception
            // array(new Exception('Exception 1'), new Exception('Exception 1')),
            // mixed types
            [0, '0'],
            ['0', 0],
            [2.3, '2.3'],
            ['2.3', 2.3],
            [(string) (1 / 3), 1 - 2 / 3],
            [1 / 3, (string) (1 - 2 / 3)],
            ['string representation', new ClassWithToString],
            [new ClassWithToString, 'string representation'],
        ];
    }

    public function equalProvider()
    {
        // same |= equal
        return array_merge($this->equalValues(), $this->sameValues());
    }

    public function notEqualProvider()
    {
        return $this->notEqualValues();
    }

    public function sameProvider()
    {
        return $this->sameValues();
    }

    public function notSameProvider()
    {
        // not equal |= not same
        // equal, ¬same |= not same
        return array_merge($this->notEqualValues(), $this->equalValues());
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertEquals
     *
     * @dataProvider equalProvider
     */
    public function test_assert_equals_succeeds($a, $b, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        $this->assertEquals($a, $b, '', $delta, 10, $canonicalize, $ignoreCase);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertEquals
     *
     * @dataProvider notEqualProvider
     */
    public function test_assert_equals_fails($a, $b, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        try {
            $this->assertEquals($a, $b, '', $delta, 10, $canonicalize, $ignoreCase);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotEquals
     *
     * @dataProvider notEqualProvider
     */
    public function test_assert_not_equals_succeeds($a, $b, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        $this->assertNotEquals($a, $b, '', $delta, 10, $canonicalize, $ignoreCase);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotEquals
     *
     * @dataProvider equalProvider
     */
    public function test_assert_not_equals_fails($a, $b, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        try {
            $this->assertNotEquals($a, $b, '', $delta, 10, $canonicalize, $ignoreCase);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertSame
     *
     * @dataProvider sameProvider
     */
    public function test_assert_same_succeeds($a, $b)
    {
        $this->assertSame($a, $b);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertSame
     *
     * @dataProvider notSameProvider
     */
    public function test_assert_same_fails($a, $b)
    {
        try {
            $this->assertSame($a, $b);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotSame
     *
     * @dataProvider notSameProvider
     */
    public function test_assert_not_same_succeeds($a, $b)
    {
        $this->assertNotSame($a, $b);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotSame
     *
     * @dataProvider sameProvider
     */
    public function test_assert_not_same_fails($a, $b)
    {
        try {
            $this->assertNotSame($a, $b);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertXmlFileEqualsXmlFile
     */
    public function test_assert_xml_file_equals_xml_file()
    {
        $this->assertXmlFileEqualsXmlFile(
            $this->filesDirectory.'foo.xml',
            $this->filesDirectory.'foo.xml'
        );

        try {
            $this->assertXmlFileEqualsXmlFile(
                $this->filesDirectory.'foo.xml',
                $this->filesDirectory.'bar.xml'
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertXmlFileNotEqualsXmlFile
     */
    public function test_assert_xml_file_not_equals_xml_file()
    {
        $this->assertXmlFileNotEqualsXmlFile(
            $this->filesDirectory.'foo.xml',
            $this->filesDirectory.'bar.xml'
        );

        try {
            $this->assertXmlFileNotEqualsXmlFile(
                $this->filesDirectory.'foo.xml',
                $this->filesDirectory.'foo.xml'
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertXmlStringEqualsXmlFile
     */
    public function test_assert_xml_string_equals_xml_file()
    {
        $this->assertXmlStringEqualsXmlFile(
            $this->filesDirectory.'foo.xml',
            file_get_contents($this->filesDirectory.'foo.xml')
        );

        try {
            $this->assertXmlStringEqualsXmlFile(
                $this->filesDirectory.'foo.xml',
                file_get_contents($this->filesDirectory.'bar.xml')
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertXmlStringNotEqualsXmlFile
     */
    public function test_xml_string_not_equals_xml_file()
    {
        $this->assertXmlStringNotEqualsXmlFile(
            $this->filesDirectory.'foo.xml',
            file_get_contents($this->filesDirectory.'bar.xml')
        );

        try {
            $this->assertXmlStringNotEqualsXmlFile(
                $this->filesDirectory.'foo.xml',
                file_get_contents($this->filesDirectory.'foo.xml')
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertXmlStringEqualsXmlString
     */
    public function test_assert_xml_string_equals_xml_string()
    {
        $this->assertXmlStringEqualsXmlString('<root/>', '<root/>');

        try {
            $this->assertXmlStringEqualsXmlString('<foo/>', '<bar/>');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     *
     * @covers            PHPUnit_Framework_Assert::assertXmlStringEqualsXmlString
     *
     * @ticket            1860
     */
    public function test_assert_xml_string_equals_xml_string2()
    {
        $this->assertXmlStringEqualsXmlString('<a></b>', '<c></d>');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertXmlStringEqualsXmlString
     *
     * @ticket 1860
     */
    public function test_assert_xml_string_equals_xml_string3()
    {
        $expected = <<<'XML'
<?xml version="1.0"?>
<root>
    <node />
</root>
XML;

        $actual = <<<'XML'
<?xml version="1.0"?>
<root>
<node />
</root>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertXmlStringNotEqualsXmlString
     */
    public function test_assert_xml_string_not_equals_xml_string()
    {
        $this->assertXmlStringNotEqualsXmlString('<foo/>', '<bar/>');

        try {
            $this->assertXmlStringNotEqualsXmlString('<root/>', '<root/>');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertEqualXMLStructure
     */
    public function test_xml_structure_is_same()
    {
        $expected = new DOMDocument;
        $expected->load($this->filesDirectory.'structureExpected.xml');

        $actual = new DOMDocument;
        $actual->load($this->filesDirectory.'structureExpected.xml');

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, true
        );
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertEqualXMLStructure
     *
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     */
    public function test_xml_structure_wrong_number_of_attributes()
    {
        $expected = new DOMDocument;
        $expected->load($this->filesDirectory.'structureExpected.xml');

        $actual = new DOMDocument;
        $actual->load($this->filesDirectory.'structureWrongNumberOfAttributes.xml');

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, true
        );
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertEqualXMLStructure
     *
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     */
    public function test_xml_structure_wrong_number_of_nodes()
    {
        $expected = new DOMDocument;
        $expected->load($this->filesDirectory.'structureExpected.xml');

        $actual = new DOMDocument;
        $actual->load($this->filesDirectory.'structureWrongNumberOfNodes.xml');

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, true
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertEqualXMLStructure
     */
    public function test_xml_structure_is_same_but_data_is_not()
    {
        $expected = new DOMDocument;
        $expected->load($this->filesDirectory.'structureExpected.xml');

        $actual = new DOMDocument;
        $actual->load($this->filesDirectory.'structureIsSameButDataIsNot.xml');

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, true
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertEqualXMLStructure
     */
    public function test_xml_structure_attributes_are_same_but_values_are_not()
    {
        $expected = new DOMDocument;
        $expected->load($this->filesDirectory.'structureExpected.xml');

        $actual = new DOMDocument;
        $actual->load($this->filesDirectory.'structureAttributesAreSameButValuesAreNot.xml');

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, true
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertEqualXMLStructure
     */
    public function test_xml_structure_ignore_text_nodes()
    {
        $expected = new DOMDocument;
        $expected->load($this->filesDirectory.'structureExpected.xml');

        $actual = new DOMDocument;
        $actual->load($this->filesDirectory.'structureIgnoreTextNodes.xml');

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, true
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertEquals
     */
    public function test_assert_string_equals_numeric()
    {
        $this->assertEquals('0', 0);

        try {
            $this->assertEquals('0', 1);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotEquals
     */
    public function test_assert_string_equals_numeric2()
    {
        $this->assertNotEquals('A', 0);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertFileExists
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_file_exists_throws_exception()
    {
        $this->assertFileExists(null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertFileExists
     */
    public function test_assert_file_exists()
    {
        $this->assertFileExists(__FILE__);

        try {
            $this->assertFileExists(__DIR__.DIRECTORY_SEPARATOR.'NotExisting');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertFileNotExists
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_file_not_exists_throws_exception()
    {
        $this->assertFileNotExists(null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertFileNotExists
     */
    public function test_assert_file_not_exists()
    {
        $this->assertFileNotExists(__DIR__.DIRECTORY_SEPARATOR.'NotExisting');

        try {
            $this->assertFileNotExists(__FILE__);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
     */
    public function test_assert_object_has_attribute()
    {
        $o = new Author('Terry Pratchett');

        $this->assertObjectHasAttribute('name', $o);

        try {
            $this->assertObjectHasAttribute('foo', $o);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
     */
    public function test_assert_object_not_has_attribute()
    {
        $o = new Author('Terry Pratchett');

        $this->assertObjectNotHasAttribute('foo', $o);

        try {
            $this->assertObjectNotHasAttribute('name', $o);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNull
     */
    public function test_assert_null()
    {
        $this->assertNull(null);

        try {
            $this->assertNull(new stdClass);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotNull
     */
    public function test_assert_not_null()
    {
        $this->assertNotNull(new stdClass);

        try {
            $this->assertNotNull(null);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertTrue
     */
    public function test_assert_true()
    {
        $this->assertTrue(true);

        try {
            $this->assertTrue(false);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotTrue
     */
    public function test_assert_not_true()
    {
        $this->assertNotTrue(false);
        $this->assertNotTrue(1);
        $this->assertNotTrue('true');

        try {
            $this->assertNotTrue(true);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertFalse
     */
    public function test_assert_false()
    {
        $this->assertFalse(false);

        try {
            $this->assertFalse(true);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotFalse
     */
    public function test_assert_not_false()
    {
        $this->assertNotFalse(true);
        $this->assertNotFalse(0);
        $this->assertNotFalse('');

        try {
            $this->assertNotFalse(false);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertRegExp
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_reg_exp_throws_exception()
    {
        $this->assertRegExp(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertRegExp
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_reg_exp_throws_exception2()
    {
        $this->assertRegExp('', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertNotRegExp
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_reg_exp_throws_exception()
    {
        $this->assertNotRegExp(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertNotRegExp
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_reg_exp_throws_exception2()
    {
        $this->assertNotRegExp('', null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertRegExp
     */
    public function test_assert_reg_exp()
    {
        $this->assertRegExp('/foo/', 'foobar');

        try {
            $this->assertRegExp('/foo/', 'bar');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotRegExp
     */
    public function test_assert_not_reg_exp()
    {
        $this->assertNotRegExp('/foo/', 'bar');

        try {
            $this->assertNotRegExp('/foo/', 'foobar');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertSame
     */
    public function test_assert_same()
    {
        $o = new stdClass;

        $this->assertSame($o, $o);

        try {
            $this->assertSame(
                new stdClass,
                new stdClass
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertSame
     */
    public function test_assert_same2()
    {
        $this->assertSame(true, true);
        $this->assertSame(false, false);

        try {
            $this->assertSame(true, false);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotSame
     */
    public function test_assert_not_same()
    {
        $this->assertNotSame(
            new stdClass,
            null
        );

        $this->assertNotSame(
            null,
            new stdClass
        );

        $this->assertNotSame(
            new stdClass,
            new stdClass
        );

        $o = new stdClass;

        try {
            $this->assertNotSame($o, $o);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotSame
     */
    public function test_assert_not_same2()
    {
        $this->assertNotSame(true, false);
        $this->assertNotSame(false, true);

        try {
            $this->assertNotSame(true, true);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotSame
     */
    public function test_assert_not_same_fails_null()
    {
        try {
            $this->assertNotSame(null, null);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertGreaterThan
     */
    public function test_greater_than()
    {
        $this->assertGreaterThan(1, 2);

        try {
            $this->assertGreaterThan(2, 1);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeGreaterThan
     */
    public function test_attribute_greater_than()
    {
        $this->assertAttributeGreaterThan(
            1, 'bar', new ClassWithNonPublicAttributes
        );

        try {
            $this->assertAttributeGreaterThan(
                1, 'foo', new ClassWithNonPublicAttributes
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertGreaterThanOrEqual
     */
    public function test_greater_than_or_equal()
    {
        $this->assertGreaterThanOrEqual(1, 2);

        try {
            $this->assertGreaterThanOrEqual(2, 1);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeGreaterThanOrEqual
     */
    public function test_attribute_greater_than_or_equal()
    {
        $this->assertAttributeGreaterThanOrEqual(
            1, 'bar', new ClassWithNonPublicAttributes
        );

        try {
            $this->assertAttributeGreaterThanOrEqual(
                2, 'foo', new ClassWithNonPublicAttributes
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertLessThan
     */
    public function test_less_than()
    {
        $this->assertLessThan(2, 1);

        try {
            $this->assertLessThan(1, 2);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeLessThan
     */
    public function test_attribute_less_than()
    {
        $this->assertAttributeLessThan(
            2, 'foo', new ClassWithNonPublicAttributes
        );

        try {
            $this->assertAttributeLessThan(
                1, 'bar', new ClassWithNonPublicAttributes
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertLessThanOrEqual
     */
    public function test_less_than_or_equal()
    {
        $this->assertLessThanOrEqual(2, 1);

        try {
            $this->assertLessThanOrEqual(1, 2);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeLessThanOrEqual
     */
    public function test_attribute_less_than_or_equal()
    {
        $this->assertAttributeLessThanOrEqual(
            2, 'foo', new ClassWithNonPublicAttributes
        );

        try {
            $this->assertAttributeLessThanOrEqual(
                1, 'bar', new ClassWithNonPublicAttributes
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::readAttribute
     * @covers PHPUnit_Framework_Assert::getStaticAttribute
     * @covers PHPUnit_Framework_Assert::getObjectAttribute
     */
    public function test_read_attribute()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertEquals('foo', $this->readAttribute($obj, 'publicAttribute'));
        $this->assertEquals('bar', $this->readAttribute($obj, 'protectedAttribute'));
        $this->assertEquals('baz', $this->readAttribute($obj, 'privateAttribute'));
        $this->assertEquals('bar', $this->readAttribute($obj, 'protectedParentAttribute'));
        // $this->assertEquals('bar', $this->readAttribute($obj, 'privateParentAttribute'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::readAttribute
     * @covers PHPUnit_Framework_Assert::getStaticAttribute
     * @covers PHPUnit_Framework_Assert::getObjectAttribute
     */
    public function test_read_attribute2()
    {
        $this->assertEquals('foo', $this->readAttribute('ClassWithNonPublicAttributes', 'publicStaticAttribute'));
        $this->assertEquals('bar', $this->readAttribute('ClassWithNonPublicAttributes', 'protectedStaticAttribute'));
        $this->assertEquals('baz', $this->readAttribute('ClassWithNonPublicAttributes', 'privateStaticAttribute'));
        $this->assertEquals('foo', $this->readAttribute('ClassWithNonPublicAttributes', 'protectedStaticParentAttribute'));
        $this->assertEquals('foo', $this->readAttribute('ClassWithNonPublicAttributes', 'privateStaticParentAttribute'));
    }

    /**
     * @covers            PHPUnit_Framework_Assert::readAttribute
     * @covers            PHPUnit_Framework_Assert::getStaticAttribute
     * @covers            PHPUnit_Framework_Assert::getObjectAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_read_attribute3()
    {
        $this->readAttribute('StdClass', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::readAttribute
     * @covers            PHPUnit_Framework_Assert::getStaticAttribute
     * @covers            PHPUnit_Framework_Assert::getObjectAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_read_attribute4()
    {
        $this->readAttribute('NotExistingClass', 'foo');
    }

    /**
     * @covers            PHPUnit_Framework_Assert::readAttribute
     * @covers            PHPUnit_Framework_Assert::getStaticAttribute
     * @covers            PHPUnit_Framework_Assert::getObjectAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_read_attribute5()
    {
        $this->readAttribute(null, 'foo');
    }

    /**
     * @covers            PHPUnit_Framework_Assert::readAttribute
     * @covers            PHPUnit_Framework_Assert::getStaticAttribute
     * @covers            PHPUnit_Framework_Assert::getObjectAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_read_attribute_if_attribute_name_is_not_valid()
    {
        $this->readAttribute('StdClass', '2');
    }

    /**
     * @covers PHPUnit_Framework_Assert::getStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_static_attribute_raises_exception_for_invalid_first_argument()
    {
        $this->getStaticAttribute(null, 'foo');
    }

    /**
     * @covers PHPUnit_Framework_Assert::getStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_static_attribute_raises_exception_for_invalid_first_argument2()
    {
        $this->getStaticAttribute('NotExistingClass', 'foo');
    }

    /**
     * @covers PHPUnit_Framework_Assert::getStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_static_attribute_raises_exception_for_invalid_second_argument()
    {
        $this->getStaticAttribute('stdClass', null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::getStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_static_attribute_raises_exception_for_invalid_second_argument2()
    {
        $this->getStaticAttribute('stdClass', '0');
    }

    /**
     * @covers PHPUnit_Framework_Assert::getStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_static_attribute_raises_exception_for_invalid_second_argument3()
    {
        $this->getStaticAttribute('stdClass', 'foo');
    }

    /**
     * @covers PHPUnit_Framework_Assert::getObjectAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_object_attribute_raises_exception_for_invalid_first_argument()
    {
        $this->getObjectAttribute(null, 'foo');
    }

    /**
     * @covers PHPUnit_Framework_Assert::getObjectAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_object_attribute_raises_exception_for_invalid_second_argument()
    {
        $this->getObjectAttribute(new stdClass, null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::getObjectAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_object_attribute_raises_exception_for_invalid_second_argument2()
    {
        $this->getObjectAttribute(new stdClass, '0');
    }

    /**
     * @covers PHPUnit_Framework_Assert::getObjectAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_object_attribute_raises_exception_for_invalid_second_argument3()
    {
        $this->getObjectAttribute(new stdClass, 'foo');
    }

    /**
     * @covers PHPUnit_Framework_Assert::getObjectAttribute
     */
    public function test_get_object_attribute_works_for_inherited_attributes()
    {
        $this->assertEquals(
            'bar',
            $this->getObjectAttribute(new ClassWithNonPublicAttributes, 'privateParentAttribute')
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeContains
     */
    public function test_assert_public_attribute_contains()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeContains('foo', 'publicArray', $obj);

        try {
            $this->assertAttributeContains('bar', 'publicArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeContainsOnly
     */
    public function test_assert_public_attribute_contains_only()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeContainsOnly('string', 'publicArray', $obj);

        try {
            $this->assertAttributeContainsOnly('integer', 'publicArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotContains
     */
    public function test_assert_public_attribute_not_contains()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotContains('bar', 'publicArray', $obj);

        try {
            $this->assertAttributeNotContains('foo', 'publicArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotContainsOnly
     */
    public function test_assert_public_attribute_not_contains_only()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotContainsOnly('integer', 'publicArray', $obj);

        try {
            $this->assertAttributeNotContainsOnly('string', 'publicArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeContains
     */
    public function test_assert_protected_attribute_contains()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeContains('bar', 'protectedArray', $obj);

        try {
            $this->assertAttributeContains('foo', 'protectedArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotContains
     */
    public function test_assert_protected_attribute_not_contains()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotContains('foo', 'protectedArray', $obj);

        try {
            $this->assertAttributeNotContains('bar', 'protectedArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeContains
     */
    public function test_assert_private_attribute_contains()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeContains('baz', 'privateArray', $obj);

        try {
            $this->assertAttributeContains('foo', 'privateArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotContains
     */
    public function test_assert_private_attribute_not_contains()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotContains('foo', 'privateArray', $obj);

        try {
            $this->assertAttributeNotContains('baz', 'privateArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeContains
     */
    public function test_assert_attribute_contains_non_object()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeContains(true, 'privateArray', $obj);

        try {
            $this->assertAttributeContains(true, 'privateArray', $obj, '', false, true, true);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotContains
     */
    public function test_assert_attribute_not_contains_non_object()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotContains(true, 'privateArray', $obj, '', false, true, true);

        try {
            $this->assertAttributeNotContains(true, 'privateArray', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeEquals
     */
    public function test_assert_public_attribute_equals()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeEquals('foo', 'publicAttribute', $obj);

        try {
            $this->assertAttributeEquals('bar', 'publicAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
     */
    public function test_assert_public_attribute_not_equals()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotEquals('bar', 'publicAttribute', $obj);

        try {
            $this->assertAttributeNotEquals('foo', 'publicAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeSame
     */
    public function test_assert_public_attribute_same()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeSame('foo', 'publicAttribute', $obj);

        try {
            $this->assertAttributeSame('bar', 'publicAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotSame
     */
    public function test_assert_public_attribute_not_same()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotSame('bar', 'publicAttribute', $obj);

        try {
            $this->assertAttributeNotSame('foo', 'publicAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeEquals
     */
    public function test_assert_protected_attribute_equals()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeEquals('bar', 'protectedAttribute', $obj);

        try {
            $this->assertAttributeEquals('foo', 'protectedAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
     */
    public function test_assert_protected_attribute_not_equals()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotEquals('foo', 'protectedAttribute', $obj);

        try {
            $this->assertAttributeNotEquals('bar', 'protectedAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeEquals
     */
    public function test_assert_private_attribute_equals()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeEquals('baz', 'privateAttribute', $obj);

        try {
            $this->assertAttributeEquals('foo', 'privateAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
     */
    public function test_assert_private_attribute_not_equals()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertAttributeNotEquals('foo', 'privateAttribute', $obj);

        try {
            $this->assertAttributeNotEquals('baz', 'privateAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeEquals
     */
    public function test_assert_public_static_attribute_equals()
    {
        $this->assertAttributeEquals('foo', 'publicStaticAttribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertAttributeEquals('bar', 'publicStaticAttribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
     */
    public function test_assert_public_static_attribute_not_equals()
    {
        $this->assertAttributeNotEquals('bar', 'publicStaticAttribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertAttributeNotEquals('foo', 'publicStaticAttribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeEquals
     */
    public function test_assert_protected_static_attribute_equals()
    {
        $this->assertAttributeEquals('bar', 'protectedStaticAttribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertAttributeEquals('foo', 'protectedStaticAttribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
     */
    public function test_assert_protected_static_attribute_not_equals()
    {
        $this->assertAttributeNotEquals('foo', 'protectedStaticAttribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertAttributeNotEquals('bar', 'protectedStaticAttribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeEquals
     */
    public function test_assert_private_static_attribute_equals()
    {
        $this->assertAttributeEquals('baz', 'privateStaticAttribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertAttributeEquals('foo', 'privateStaticAttribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
     */
    public function test_assert_private_static_attribute_not_equals()
    {
        $this->assertAttributeNotEquals('foo', 'privateStaticAttribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertAttributeNotEquals('baz', 'privateStaticAttribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_has_attribute_throws_exception()
    {
        $this->assertClassHasAttribute(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_has_attribute_throws_exception2()
    {
        $this->assertClassHasAttribute('foo', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_has_attribute_throws_exception_if_attribute_name_is_not_valid()
    {
        $this->assertClassHasAttribute('1', 'ClassWithNonPublicAttributes');
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassNotHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_not_has_attribute_throws_exception()
    {
        $this->assertClassNotHasAttribute(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassNotHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_not_has_attribute_throws_exception2()
    {
        $this->assertClassNotHasAttribute('foo', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassNotHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_not_has_attribute_throws_exception_if_attribute_name_is_not_valid()
    {
        $this->assertClassNotHasAttribute('1', 'ClassWithNonPublicAttributes');
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassHasStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_has_static_attribute_throws_exception()
    {
        $this->assertClassHasStaticAttribute(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassHasStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_has_static_attribute_throws_exception2()
    {
        $this->assertClassHasStaticAttribute('foo', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassHasStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_has_static_attribute_throws_exception_if_attribute_name_is_not_valid()
    {
        $this->assertClassHasStaticAttribute('1', 'ClassWithNonPublicAttributes');
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassNotHasStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_not_has_static_attribute_throws_exception()
    {
        $this->assertClassNotHasStaticAttribute(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassNotHasStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_not_has_static_attribute_throws_exception2()
    {
        $this->assertClassNotHasStaticAttribute('foo', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertClassNotHasStaticAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_class_not_has_static_attribute_throws_exception_if_attribute_name_is_not_valid()
    {
        $this->assertClassNotHasStaticAttribute('1', 'ClassWithNonPublicAttributes');
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertObjectHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_object_has_attribute_throws_exception()
    {
        $this->assertObjectHasAttribute(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertObjectHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_object_has_attribute_throws_exception2()
    {
        $this->assertObjectHasAttribute('foo', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertObjectHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_object_has_attribute_throws_exception_if_attribute_name_is_not_valid()
    {
        $this->assertObjectHasAttribute('1', 'ClassWithNonPublicAttributes');
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertObjectNotHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_object_not_has_attribute_throws_exception()
    {
        $this->assertObjectNotHasAttribute(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertObjectNotHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_object_not_has_attribute_throws_exception2()
    {
        $this->assertObjectNotHasAttribute('foo', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertObjectNotHasAttribute
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_object_not_has_attribute_throws_exception_if_attribute_name_is_not_valid()
    {
        $this->assertObjectNotHasAttribute('1', 'ClassWithNonPublicAttributes');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertClassHasAttribute
     */
    public function test_class_has_public_attribute()
    {
        $this->assertClassHasAttribute('publicAttribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertClassHasAttribute('attribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertClassNotHasAttribute
     */
    public function test_class_not_has_public_attribute()
    {
        $this->assertClassNotHasAttribute('attribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertClassNotHasAttribute('publicAttribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertClassHasStaticAttribute
     */
    public function test_class_has_public_static_attribute()
    {
        $this->assertClassHasStaticAttribute('publicStaticAttribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertClassHasStaticAttribute('attribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertClassNotHasStaticAttribute
     */
    public function test_class_not_has_public_static_attribute()
    {
        $this->assertClassNotHasStaticAttribute('attribute', 'ClassWithNonPublicAttributes');

        try {
            $this->assertClassNotHasStaticAttribute('publicStaticAttribute', 'ClassWithNonPublicAttributes');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
     */
    public function test_object_has_public_attribute()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertObjectHasAttribute('publicAttribute', $obj);

        try {
            $this->assertObjectHasAttribute('attribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
     */
    public function test_object_not_has_public_attribute()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertObjectNotHasAttribute('attribute', $obj);

        try {
            $this->assertObjectNotHasAttribute('publicAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
     */
    public function test_object_has_on_the_fly_attribute()
    {
        $obj = new stdClass;
        $obj->foo = 'bar';

        $this->assertObjectHasAttribute('foo', $obj);

        try {
            $this->assertObjectHasAttribute('bar', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
     */
    public function test_object_not_has_on_the_fly_attribute()
    {
        $obj = new stdClass;
        $obj->foo = 'bar';

        $this->assertObjectNotHasAttribute('bar', $obj);

        try {
            $this->assertObjectNotHasAttribute('foo', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
     */
    public function test_object_has_protected_attribute()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertObjectHasAttribute('protectedAttribute', $obj);

        try {
            $this->assertObjectHasAttribute('attribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
     */
    public function test_object_not_has_protected_attribute()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertObjectNotHasAttribute('attribute', $obj);

        try {
            $this->assertObjectNotHasAttribute('protectedAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
     */
    public function test_object_has_private_attribute()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertObjectHasAttribute('privateAttribute', $obj);

        try {
            $this->assertObjectHasAttribute('attribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
     */
    public function test_object_not_has_private_attribute()
    {
        $obj = new ClassWithNonPublicAttributes;

        $this->assertObjectNotHasAttribute('attribute', $obj);

        try {
            $this->assertObjectNotHasAttribute('privateAttribute', $obj);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::attribute
     * @covers PHPUnit_Framework_Assert::equalTo
     */
    public function test_assert_that_attribute_equals()
    {
        $this->assertThat(
            new ClassWithNonPublicAttributes,
            $this->attribute(
                $this->equalTo('foo'),
                'publicAttribute'
            )
        );
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertThat
     * @covers            PHPUnit_Framework_Assert::attribute
     * @covers            PHPUnit_Framework_Assert::equalTo
     *
     * @expectedException PHPUnit_Framework_AssertionFailedError
     */
    public function test_assert_that_attribute_equals2()
    {
        $this->assertThat(
            new ClassWithNonPublicAttributes,
            $this->attribute(
                $this->equalTo('bar'),
                'publicAttribute'
            )
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::attribute
     * @covers PHPUnit_Framework_Assert::equalTo
     */
    public function test_assert_that_attribute_equal_to()
    {
        $this->assertThat(
            new ClassWithNonPublicAttributes,
            $this->attributeEqualTo('publicAttribute', 'foo')
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::anything
     */
    public function test_assert_that_anything()
    {
        $this->assertThat('anything', $this->anything());
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::isTrue
     */
    public function test_assert_that_is_true()
    {
        $this->assertThat(true, $this->isTrue());
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::isFalse
     */
    public function test_assert_that_is_false()
    {
        $this->assertThat(false, $this->isFalse());
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::isJson
     */
    public function test_assert_that_is_json()
    {
        $this->assertThat('{}', $this->isJson());
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::anything
     * @covers PHPUnit_Framework_Assert::logicalAnd
     */
    public function test_assert_that_anything_and_anything()
    {
        $this->assertThat(
            'anything',
            $this->logicalAnd(
                $this->anything(), $this->anything()
            )
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::anything
     * @covers PHPUnit_Framework_Assert::logicalOr
     */
    public function test_assert_that_anything_or_anything()
    {
        $this->assertThat(
            'anything',
            $this->logicalOr(
                $this->anything(), $this->anything()
            )
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::anything
     * @covers PHPUnit_Framework_Assert::logicalNot
     * @covers PHPUnit_Framework_Assert::logicalXor
     */
    public function test_assert_that_anything_xor_not_anything()
    {
        $this->assertThat(
            'anything',
            $this->logicalXor(
                $this->anything(),
                $this->logicalNot($this->anything())
            )
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::contains
     */
    public function test_assert_that_contains()
    {
        $this->assertThat(['foo'], $this->contains('foo'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::stringContains
     */
    public function test_assert_that_string_contains()
    {
        $this->assertThat('barfoobar', $this->stringContains('foo'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::containsOnly
     */
    public function test_assert_that_contains_only()
    {
        $this->assertThat(['foo'], $this->containsOnly('string'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::containsOnlyInstancesOf
     */
    public function test_assert_that_contains_only_instances_of()
    {
        $this->assertThat([new Book], $this->containsOnlyInstancesOf('Book'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::arrayHasKey
     */
    public function test_assert_that_array_has_key()
    {
        $this->assertThat(['foo' => 'bar'], $this->arrayHasKey('foo'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::classHasAttribute
     */
    public function test_assert_that_class_has_attribute()
    {
        $this->assertThat(
            new ClassWithNonPublicAttributes,
            $this->classHasAttribute('publicAttribute')
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::classHasStaticAttribute
     */
    public function test_assert_that_class_has_static_attribute()
    {
        $this->assertThat(
            new ClassWithNonPublicAttributes,
            $this->classHasStaticAttribute('publicStaticAttribute')
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::objectHasAttribute
     */
    public function test_assert_that_object_has_attribute()
    {
        $this->assertThat(
            new ClassWithNonPublicAttributes,
            $this->objectHasAttribute('publicAttribute')
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::equalTo
     */
    public function test_assert_that_equal_to()
    {
        $this->assertThat('foo', $this->equalTo('foo'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::identicalTo
     */
    public function test_assert_that_identical_to()
    {
        $value = new stdClass;
        $constraint = $this->identicalTo($value);

        $this->assertThat($value, $constraint);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::isInstanceOf
     */
    public function test_assert_that_is_instance_of()
    {
        $this->assertThat(new stdClass, $this->isInstanceOf('StdClass'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::isType
     */
    public function test_assert_that_is_type()
    {
        $this->assertThat('string', $this->isType('string'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::isEmpty
     */
    public function test_assert_that_is_empty()
    {
        $this->assertThat([], $this->isEmpty());
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::fileExists
     */
    public function test_assert_that_file_exists()
    {
        $this->assertThat(__FILE__, $this->fileExists());
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::greaterThan
     */
    public function test_assert_that_greater_than()
    {
        $this->assertThat(2, $this->greaterThan(1));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::greaterThanOrEqual
     */
    public function test_assert_that_greater_than_or_equal()
    {
        $this->assertThat(2, $this->greaterThanOrEqual(1));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::lessThan
     */
    public function test_assert_that_less_than()
    {
        $this->assertThat(1, $this->lessThan(2));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::lessThanOrEqual
     */
    public function test_assert_that_less_than_or_equal()
    {
        $this->assertThat(1, $this->lessThanOrEqual(2));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::matchesRegularExpression
     */
    public function test_assert_that_matches_regular_expression()
    {
        $this->assertThat('foobar', $this->matchesRegularExpression('/foo/'));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::callback
     */
    public function test_assert_that_callback()
    {
        $this->assertThat(
            null,
            $this->callback(function ($other) {
                return true;
            })
        );
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertThat
     * @covers PHPUnit_Framework_Assert::countOf
     */
    public function test_assert_that_count_of()
    {
        $this->assertThat([1], $this->countOf(1));
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertFileEquals
     */
    public function test_assert_file_equals()
    {
        $this->assertFileEquals(
            $this->filesDirectory.'foo.xml',
            $this->filesDirectory.'foo.xml'
        );

        try {
            $this->assertFileEquals(
                $this->filesDirectory.'foo.xml',
                $this->filesDirectory.'bar.xml'
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertFileNotEquals
     */
    public function test_assert_file_not_equals()
    {
        $this->assertFileNotEquals(
            $this->filesDirectory.'foo.xml',
            $this->filesDirectory.'bar.xml'
        );

        try {
            $this->assertFileNotEquals(
                $this->filesDirectory.'foo.xml',
                $this->filesDirectory.'foo.xml'
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringEqualsFile
     */
    public function test_assert_string_equals_file()
    {
        $this->assertStringEqualsFile(
            $this->filesDirectory.'foo.xml',
            file_get_contents($this->filesDirectory.'foo.xml')
        );

        try {
            $this->assertStringEqualsFile(
                $this->filesDirectory.'foo.xml',
                file_get_contents($this->filesDirectory.'bar.xml')
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringNotEqualsFile
     */
    public function test_assert_string_not_equals_file()
    {
        $this->assertStringNotEqualsFile(
            $this->filesDirectory.'foo.xml',
            file_get_contents($this->filesDirectory.'bar.xml')
        );

        try {
            $this->assertStringNotEqualsFile(
                $this->filesDirectory.'foo.xml',
                file_get_contents($this->filesDirectory.'foo.xml')
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertStringStartsWith
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_starts_with_throws_exception()
    {
        $this->assertStringStartsWith(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertStringStartsWith
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_starts_with_throws_exception2()
    {
        $this->assertStringStartsWith('', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertStringStartsNotWith
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_starts_not_with_throws_exception()
    {
        $this->assertStringStartsNotWith(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertStringStartsNotWith
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_starts_not_with_throws_exception2()
    {
        $this->assertStringStartsNotWith('', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertStringEndsWith
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_ends_with_throws_exception()
    {
        $this->assertStringEndsWith(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertStringEndsWith
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_ends_with_throws_exception2()
    {
        $this->assertStringEndsWith('', null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertStringEndsNotWith
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_ends_not_with_throws_exception()
    {
        $this->assertStringEndsNotWith(null, null);
    }

    /**
     * @covers            PHPUnit_Framework_Assert::assertStringEndsNotWith
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_ends_not_with_throws_exception2()
    {
        $this->assertStringEndsNotWith('', null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringStartsWith
     */
    public function test_assert_string_starts_with()
    {
        $this->assertStringStartsWith('prefix', 'prefixfoo');

        try {
            $this->assertStringStartsWith('prefix', 'foo');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringStartsNotWith
     */
    public function test_assert_string_starts_not_with()
    {
        $this->assertStringStartsNotWith('prefix', 'foo');

        try {
            $this->assertStringStartsNotWith('prefix', 'prefixfoo');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringEndsWith
     */
    public function test_assert_string_ends_with()
    {
        $this->assertStringEndsWith('suffix', 'foosuffix');

        try {
            $this->assertStringEndsWith('suffix', 'foo');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringEndsNotWith
     */
    public function test_assert_string_ends_not_with()
    {
        $this->assertStringEndsNotWith('suffix', 'foo');

        try {
            $this->assertStringEndsNotWith('suffix', 'foosuffix');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringMatchesFormat
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_matches_format_raises_exception_for_invalid_first_argument()
    {
        $this->assertStringMatchesFormat(null, '');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringMatchesFormat
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_matches_format_raises_exception_for_invalid_second_argument()
    {
        $this->assertStringMatchesFormat('', null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringMatchesFormat
     */
    public function test_assert_string_matches_format()
    {
        $this->assertStringMatchesFormat('*%s*', '***');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringMatchesFormat
     *
     * @expectedException PHPUnit_Framework_AssertionFailedError
     */
    public function test_assert_string_matches_format_failure()
    {
        $this->assertStringMatchesFormat('*%s*', '**');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringNotMatchesFormat
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_not_matches_format_raises_exception_for_invalid_first_argument()
    {
        $this->assertStringNotMatchesFormat(null, '');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringNotMatchesFormat
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_not_matches_format_raises_exception_for_invalid_second_argument()
    {
        $this->assertStringNotMatchesFormat('', null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringNotMatchesFormat
     */
    public function test_assert_string_not_matches_format()
    {
        $this->assertStringNotMatchesFormat('*%s*', '**');

        try {
            $this->assertStringMatchesFormat('*%s*', '**');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertEmpty
     */
    public function test_assert_empty()
    {
        $this->assertEmpty([]);

        try {
            $this->assertEmpty(['foo']);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotEmpty
     */
    public function test_assert_not_empty()
    {
        $this->assertNotEmpty(['foo']);

        try {
            $this->assertNotEmpty([]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeEmpty
     */
    public function test_assert_attribute_empty()
    {
        $o = new stdClass;
        $o->a = [];

        $this->assertAttributeEmpty('a', $o);

        try {
            $o->a = ['b'];
            $this->assertAttributeEmpty('a', $o);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotEmpty
     */
    public function test_assert_attribute_not_empty()
    {
        $o = new stdClass;
        $o->a = ['b'];

        $this->assertAttributeNotEmpty('a', $o);

        try {
            $o->a = [];
            $this->assertAttributeNotEmpty('a', $o);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::markTestIncomplete
     */
    public function test_mark_test_incomplete()
    {
        try {
            $this->markTestIncomplete('incomplete');
        } catch (PHPUnit_Framework_IncompleteTestError $e) {
            $this->assertEquals('incomplete', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::markTestSkipped
     */
    public function test_mark_test_skipped()
    {
        try {
            $this->markTestSkipped('skipped');
        } catch (PHPUnit_Framework_SkippedTestError $e) {
            $this->assertEquals('skipped', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertCount
     */
    public function test_assert_count()
    {
        $this->assertCount(2, [1, 2]);

        try {
            $this->assertCount(2, [1, 2, 3]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertCount
     */
    public function test_assert_count_traversable()
    {
        $this->assertCount(2, new ArrayIterator([1, 2]));

        try {
            $this->assertCount(2, new ArrayIterator([1, 2, 3]));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertCount
     */
    public function test_assert_count_throws_exception_if_expected_count_is_no_integer()
    {
        try {
            $this->assertCount('a', []);
        } catch (PHPUnit_Framework_Exception $e) {
            $this->assertEquals('Argument #1 (No Value) of PHPUnit_Framework_Assert::assertCount() must be a integer', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertCount
     */
    public function test_assert_count_throws_exception_if_element_is_not_countable()
    {
        try {
            $this->assertCount(2, '');
        } catch (PHPUnit_Framework_Exception $e) {
            $this->assertEquals('Argument #2 (No Value) of PHPUnit_Framework_Assert::assertCount() must be a countable or traversable', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeCount
     */
    public function test_assert_attribute_count()
    {
        $o = new stdClass;
        $o->a = [];

        $this->assertAttributeCount(0, 'a', $o);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotCount
     */
    public function test_assert_not_count()
    {
        $this->assertNotCount(2, [1, 2, 3]);

        try {
            $this->assertNotCount(2, [1, 2]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotCount
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_count_throws_exception_if_expected_count_is_no_integer()
    {
        $this->assertNotCount('a', []);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotCount
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_count_throws_exception_if_element_is_not_countable()
    {
        $this->assertNotCount(2, '');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotCount
     */
    public function test_assert_attribute_not_count()
    {
        $o = new stdClass;
        $o->a = [];

        $this->assertAttributeNotCount(1, 'a', $o);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertSameSize
     */
    public function test_assert_same_size()
    {
        $this->assertSameSize([1, 2], [3, 4]);

        try {
            $this->assertSameSize([1, 2], [1, 2, 3]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertSameSize
     */
    public function test_assert_same_size_throws_exception_if_expected_is_not_countable()
    {
        try {
            $this->assertSameSize('a', []);
        } catch (PHPUnit_Framework_Exception $e) {
            $this->assertEquals('Argument #1 (No Value) of PHPUnit_Framework_Assert::assertSameSize() must be a countable or traversable', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertSameSize
     */
    public function test_assert_same_size_throws_exception_if_actual_is_not_countable()
    {
        try {
            $this->assertSameSize([], '');
        } catch (PHPUnit_Framework_Exception $e) {
            $this->assertEquals('Argument #2 (No Value) of PHPUnit_Framework_Assert::assertSameSize() must be a countable or traversable', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotSameSize
     */
    public function test_assert_not_same_size()
    {
        $this->assertNotSameSize([1, 2], [1, 2, 3]);

        try {
            $this->assertNotSameSize([1, 2], [3, 4]);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotSameSize
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_same_size_throws_exception_if_expected_is_not_countable()
    {
        $this->assertNotSameSize('a', []);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotSameSize
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_same_size_throws_exception_if_actual_is_not_countable()
    {
        $this->assertNotSameSize([], '');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJson
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_json_raises_exception_for_invalid_argument()
    {
        $this->assertJson(null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJson
     */
    public function test_assert_json()
    {
        $this->assertJson('{}');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonString
     */
    public function test_assert_json_string_equals_json_string()
    {
        $expected = '{"Mascott" : "Tux"}';
        $actual = '{"Mascott" : "Tux"}';
        $message = 'Given Json strings do not match';

        $this->assertJsonStringEqualsJsonString($expected, $actual, $message);
    }

    /**
     * @dataProvider validInvalidJsonDataprovider
     *
     * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonString
     */
    public function test_assert_json_string_equals_json_string_error_raised($expected, $actual)
    {
        try {
            $this->assertJsonStringEqualsJsonString($expected, $actual);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }
        $this->fail('Expected exception not found');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonStringNotEqualsJsonString
     */
    public function test_assert_json_string_not_equals_json_string()
    {
        $expected = '{"Mascott" : "Beastie"}';
        $actual = '{"Mascott" : "Tux"}';
        $message = 'Given Json strings do match';

        $this->assertJsonStringNotEqualsJsonString($expected, $actual, $message);
    }

    /**
     * @dataProvider validInvalidJsonDataprovider
     *
     * @covers PHPUnit_Framework_Assert::assertJsonStringNotEqualsJsonString
     */
    public function test_assert_json_string_not_equals_json_string_error_raised($expected, $actual)
    {
        try {
            $this->assertJsonStringNotEqualsJsonString($expected, $actual);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }
        $this->fail('Expected exception not found');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonFile
     */
    public function test_assert_json_string_equals_json_file()
    {
        $file = __DIR__.'/../_files/JsonData/simpleObject.json';
        $actual = json_encode(['Mascott' => 'Tux']);
        $message = '';
        $this->assertJsonStringEqualsJsonFile($file, $actual, $message);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonFile
     */
    public function test_assert_json_string_equals_json_file_expecting_expectation_failed_exception()
    {
        $file = __DIR__.'/../_files/JsonData/simpleObject.json';
        $actual = json_encode(['Mascott' => 'Beastie']);
        $message = '';
        try {
            $this->assertJsonStringEqualsJsonFile($file, $actual, $message);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals(
                'Failed asserting that \'{"Mascott":"Beastie"}\' matches JSON string "{"Mascott":"Tux"}".',
                $e->getMessage()
            );

            return;
        }

        $this->fail('Expected Exception not thrown.');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonFile
     */
    public function test_assert_json_string_equals_json_file_expecting_exception()
    {
        $file = __DIR__.'/../_files/JsonData/simpleObject.json';
        try {
            $this->assertJsonStringEqualsJsonFile($file, null);
        } catch (PHPUnit_Framework_Exception $e) {
            return;
        }
        $this->fail('Expected Exception not thrown.');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonStringNotEqualsJsonFile
     */
    public function test_assert_json_string_not_equals_json_file()
    {
        $file = __DIR__.'/../_files/JsonData/simpleObject.json';
        $actual = json_encode(['Mascott' => 'Beastie']);
        $message = '';
        $this->assertJsonStringNotEqualsJsonFile($file, $actual, $message);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonStringNotEqualsJsonFile
     */
    public function test_assert_json_string_not_equals_json_file_expecting_exception()
    {
        $file = __DIR__.'/../_files/JsonData/simpleObject.json';
        try {
            $this->assertJsonStringNotEqualsJsonFile($file, null);
        } catch (PHPUnit_Framework_Exception $e) {
            return;
        }
        $this->fail('Expected exception not found.');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonFileNotEqualsJsonFile
     */
    public function test_assert_json_file_not_equals_json_file()
    {
        $fileExpected = __DIR__.'/../_files/JsonData/simpleObject.json';
        $fileActual = __DIR__.'/../_files/JsonData/arrayObject.json';
        $message = '';
        $this->assertJsonFileNotEqualsJsonFile($fileExpected, $fileActual, $message);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertJsonFileEqualsJsonFile
     */
    public function test_assert_json_file_equals_json_file()
    {
        $file = __DIR__.'/../_files/JsonData/simpleObject.json';
        $message = '';
        $this->assertJsonFileEqualsJsonFile($file, $file, $message);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertInstanceOf
     */
    public function test_assert_instance_of()
    {
        $this->assertInstanceOf('stdClass', new stdClass);

        try {
            $this->assertInstanceOf('Exception', new stdClass);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertInstanceOf
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_instance_of_throws_exception_for_invalid_argument()
    {
        $this->assertInstanceOf(null, new stdClass);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeInstanceOf
     */
    public function test_assert_attribute_instance_of()
    {
        $o = new stdClass;
        $o->a = new stdClass;

        $this->assertAttributeInstanceOf('stdClass', 'a', $o);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotInstanceOf
     */
    public function test_assert_not_instance_of()
    {
        $this->assertNotInstanceOf('Exception', new stdClass);

        try {
            $this->assertNotInstanceOf('stdClass', new stdClass);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotInstanceOf
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_instance_of_throws_exception_for_invalid_argument()
    {
        $this->assertNotInstanceOf(null, new stdClass);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotInstanceOf
     */
    public function test_assert_attribute_not_instance_of()
    {
        $o = new stdClass;
        $o->a = new stdClass;

        $this->assertAttributeNotInstanceOf('Exception', 'a', $o);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertInternalType
     */
    public function test_assert_internal_type()
    {
        $this->assertInternalType('integer', 1);

        try {
            $this->assertInternalType('string', 1);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertInternalType
     */
    public function test_assert_internal_type_double()
    {
        $this->assertInternalType('double', 1.0);

        try {
            $this->assertInternalType('double', 1);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertInternalType
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_internal_type_throws_exception_for_invalid_argument()
    {
        $this->assertInternalType(null, 1);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeInternalType
     */
    public function test_assert_attribute_internal_type()
    {
        $o = new stdClass;
        $o->a = 1;

        $this->assertAttributeInternalType('integer', 'a', $o);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotInternalType
     */
    public function test_assert_not_internal_type()
    {
        $this->assertNotInternalType('string', 1);

        try {
            $this->assertNotInternalType('integer', 1);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertNotInternalType
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_not_internal_type_throws_exception_for_invalid_argument()
    {
        $this->assertNotInternalType(null, 1);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertAttributeNotInternalType
     */
    public function test_assert_attribute_not_internal_type()
    {
        $o = new stdClass;
        $o->a = 1;

        $this->assertAttributeNotInternalType('string', 'a', $o);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringMatchesFormatFile
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_matches_format_file_throws_exception_for_invalid_argument()
    {
        $this->assertStringMatchesFormatFile('not_existing_file', '');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringMatchesFormatFile
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_matches_format_file_throws_exception_for_invalid_argument2()
    {
        $this->assertStringMatchesFormatFile($this->filesDirectory.'expectedFileFormat.txt', null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringMatchesFormatFile
     */
    public function test_assert_string_matches_format_file()
    {
        $this->assertStringMatchesFormatFile($this->filesDirectory.'expectedFileFormat.txt', "FOO\n");

        try {
            $this->assertStringMatchesFormatFile($this->filesDirectory.'expectedFileFormat.txt', "BAR\n");
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringNotMatchesFormatFile
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_not_matches_format_file_throws_exception_for_invalid_argument()
    {
        $this->assertStringNotMatchesFormatFile('not_existing_file', '');
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringNotMatchesFormatFile
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_assert_string_not_matches_format_file_throws_exception_for_invalid_argument2()
    {
        $this->assertStringNotMatchesFormatFile($this->filesDirectory.'expectedFileFormat.txt', null);
    }

    /**
     * @covers PHPUnit_Framework_Assert::assertStringNotMatchesFormatFile
     */
    public function test_assert_string_not_matches_format_file()
    {
        $this->assertStringNotMatchesFormatFile($this->filesDirectory.'expectedFileFormat.txt', "BAR\n");

        try {
            $this->assertStringNotMatchesFormatFile($this->filesDirectory.'expectedFileFormat.txt', "FOO\n");
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @return array
     */
    public static function validInvalidJsonDataprovider()
    {
        return [
            'error syntax in expected JSON' => ['{"Mascott"::}', '{"Mascott" : "Tux"}'],
            'error UTF-8 in actual JSON' => ['{"Mascott" : "Tux"}', '{"Mascott" : :}'],
        ];
    }
}
