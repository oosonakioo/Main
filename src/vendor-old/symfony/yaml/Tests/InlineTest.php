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
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Inline;
use Symfony\Component\Yaml\Yaml;

class InlineTest extends TestCase
{
    /**
     * @dataProvider getTestsForParse
     */
    public function test_parse($yaml, $value)
    {
        $this->assertSame($value, Inline::parse($yaml), sprintf('::parse() converts an inline YAML to a PHP structure (%s)', $yaml));
    }

    /**
     * @dataProvider getTestsForParseWithMapObjects
     */
    public function test_parse_with_map_objects($yaml, $value)
    {
        $actual = Inline::parse($yaml, Yaml::PARSE_OBJECT_FOR_MAP);

        $this->assertSame(serialize($value), serialize($actual));
    }

    /**
     * @dataProvider getTestsForParsePhpConstants
     */
    public function test_parse_php_constants($yaml, $value)
    {
        $actual = Inline::parse($yaml, Yaml::PARSE_CONSTANT);

        $this->assertSame($value, $actual);
    }

    public function getTestsForParsePhpConstants()
    {
        return [
            ['!php/const:Symfony\Component\Yaml\Yaml::PARSE_CONSTANT', Yaml::PARSE_CONSTANT],
            ['!php/const:PHP_INT_MAX', PHP_INT_MAX],
            ['[!php/const:PHP_INT_MAX]', [PHP_INT_MAX]],
            ['{ foo: !php/const:PHP_INT_MAX }', ['foo' => PHP_INT_MAX]],
        ];
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage The constant "WRONG_CONSTANT" is not defined
     */
    public function test_parse_php_constant_throws_exception_when_undefined()
    {
        Inline::parse('!php/const:WRONG_CONSTANT', Yaml::PARSE_CONSTANT);
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessageRegExp #The string "!php/const:PHP_INT_MAX" could not be parsed as a constant.*#
     */
    public function test_parse_php_constant_throws_exception_on_invalid_type()
    {
        Inline::parse('!php/const:PHP_INT_MAX', Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
    }

    /**
     * @group legacy
     *
     * @dataProvider getTestsForParseWithMapObjects
     */
    public function test_parse_with_map_objects_passing_true($yaml, $value)
    {
        $actual = Inline::parse($yaml, false, false, true);

        $this->assertSame(serialize($value), serialize($actual));
    }

    /**
     * @dataProvider getTestsForDump
     */
    public function test_dump($yaml, $value)
    {
        $this->assertEquals($yaml, Inline::dump($value), sprintf('::dump() converts a PHP structure to an inline YAML (%s)', $yaml));

        $this->assertSame($value, Inline::parse(Inline::dump($value)), 'check consistency');
    }

    public function test_dump_numeric_value_with_locale()
    {
        $locale = setlocale(LC_NUMERIC, 0);
        if ($locale === false) {
            $this->markTestSkipped('Your platform does not support locales.');
        }

        try {
            $requiredLocales = ['fr_FR.UTF-8', 'fr_FR.UTF8', 'fr_FR.utf-8', 'fr_FR.utf8', 'French_France.1252'];
            if (setlocale(LC_NUMERIC, $requiredLocales) === false) {
                $this->markTestSkipped('Could not set any of required locales: '.implode(', ', $requiredLocales));
            }

            $this->assertEquals('1.2', Inline::dump(1.2));
            $this->assertContains('fr', strtolower(setlocale(LC_NUMERIC, 0)));
        } finally {
            setlocale(LC_NUMERIC, $locale);
        }
    }

    public function test_hash_strings_resembling_exponential_numerics_should_not_be_changed_to_inf()
    {
        $value = '686e444';

        $this->assertSame($value, Inline::parse(Inline::dump($value)));
    }

    /**
     * @expectedException        \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage Found unknown escape character "\V".
     */
    public function test_parse_scalar_with_non_escaped_blackslash_should_throw_exception()
    {
        Inline::parse('"Foo\Var"');
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_parse_scalar_with_non_escaped_blackslash_at_the_end_should_throw_exception()
    {
        Inline::parse('"Foo\\"');
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_parse_scalar_with_incorrectly_quoted_string_should_throw_exception()
    {
        $value = "'don't do somthin' like that'";
        Inline::parse($value);
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_parse_scalar_with_incorrectly_double_quoted_string_should_throw_exception()
    {
        $value = '"don"t do somthin" like that"';
        Inline::parse($value);
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_parse_invalid_mapping_key_should_throw_exception()
    {
        $value = '{ "foo " bar": "bar" }';
        Inline::parse($value);
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation Using a colon that is not followed by an indication character (i.e. " ", ",", "[", "]", "{", "}" is deprecated since version 3.2 and will throw a ParseException in 4.0.
     * throws \Symfony\Component\Yaml\Exception\ParseException in 4.0
     */
    public function test_parse_mapping_key_with_colon_not_followed_by_space()
    {
        Inline::parse('{1:""}');
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_parse_invalid_mapping_should_throw_exception()
    {
        Inline::parse('[foo] bar');
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_parse_invalid_sequence_should_throw_exception()
    {
        Inline::parse('{ foo: bar } bar');
    }

    public function test_parse_scalar_with_correctly_quoted_string_should_return_string()
    {
        $value = "'don''t do somthin'' like that'";
        $expect = "don't do somthin' like that";

        $this->assertSame($expect, Inline::parseScalar($value));
    }

    /**
     * @dataProvider getDataForParseReferences
     */
    public function test_parse_references($yaml, $expected)
    {
        $this->assertSame($expected, Inline::parse($yaml, 0, ['var' => 'var-value']));
    }

    /**
     * @group legacy
     *
     * @dataProvider getDataForParseReferences
     */
    public function test_parse_references_as_fifth_argument($yaml, $expected)
    {
        $this->assertSame($expected, Inline::parse($yaml, false, false, false, ['var' => 'var-value']));
    }

    public function getDataForParseReferences()
    {
        return [
            'scalar' => ['*var', 'var-value'],
            'list' => ['[ *var ]', ['var-value']],
            'list-in-list' => ['[[ *var ]]', [['var-value']]],
            'map-in-list' => ['[ { key: *var } ]', [['key' => 'var-value']]],
            'embedded-mapping-in-list' => ['[ key: *var ]', [['key' => 'var-value']]],
            'map' => ['{ key: *var }', ['key' => 'var-value']],
            'list-in-map' => ['{ key: [*var] }', ['key' => ['var-value']]],
            'map-in-map' => ['{ foo: { bar: *var } }', ['foo' => ['bar' => 'var-value']]],
        ];
    }

    public function test_parse_map_reference_in_sequence()
    {
        $foo = [
            'a' => 'Steve',
            'b' => 'Clark',
            'c' => 'Brian',
        ];
        $this->assertSame([$foo], Inline::parse('[*foo]', 0, ['foo' => $foo]));
    }

    /**
     * @group legacy
     */
    public function test_parse_map_reference_in_sequence_as_fifth_argument()
    {
        $foo = [
            'a' => 'Steve',
            'b' => 'Clark',
            'c' => 'Brian',
        ];
        $this->assertSame([$foo], Inline::parse('[*foo]', false, false, false, ['foo' => $foo]));
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage A reference must contain at least one character.
     */
    public function test_parse_unquoted_asterisk()
    {
        Inline::parse('{ foo: * }');
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage A reference must contain at least one character.
     */
    public function test_parse_unquoted_asterisk_followed_by_a_comment()
    {
        Inline::parse('{ foo: * #foo }');
    }

    /**
     * @dataProvider getReservedIndicators
     *
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage cannot start a plain scalar; you need to quote the scalar.
     */
    public function test_parse_unquoted_scalar_starting_with_reserved_indicator($indicator)
    {
        Inline::parse(sprintf('{ foo: %sfoo }', $indicator));
    }

    public function getReservedIndicators()
    {
        return [['@'], ['`']];
    }

    /**
     * @dataProvider getScalarIndicators
     *
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage cannot start a plain scalar; you need to quote the scalar.
     */
    public function test_parse_unquoted_scalar_starting_with_scalar_indicator($indicator)
    {
        Inline::parse(sprintf('{ foo: %sfoo }', $indicator));
    }

    public function getScalarIndicators()
    {
        return [['|'], ['>']];
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation Not quoting the scalar "%bar " starting with the "%" indicator character is deprecated since Symfony 3.1 and will throw a ParseException in 4.0.
     * throws \Symfony\Component\Yaml\Exception\ParseException in 4.0
     */
    public function test_parse_unquoted_scalar_starting_with_percent_character()
    {
        Inline::parse('{ foo: %bar }');
    }

    /**
     * @dataProvider getDataForIsHash
     */
    public function test_is_hash($array, $expected)
    {
        $this->assertSame($expected, Inline::isHash($array));
    }

    public function getDataForIsHash()
    {
        return [
            [[], false],
            [[1, 2, 3], false],
            [[2 => 1, 1 => 2, 0 => 3], true],
            [['foo' => 1, 'bar' => 2], true],
        ];
    }

    public function getTestsForParse()
    {
        return [
            ['', ''],
            ['null', null],
            ['false', false],
            ['true', true],
            ['12', 12],
            ['-12', -12],
            ['1_2', 12],
            ['_12', '_12'],
            ['12_', 12],
            ['"quoted string"', 'quoted string'],
            ["'quoted string'", 'quoted string'],
            ['12.30e+02', 12.30e+02],
            ['123.45_67', 123.4567],
            ['0x4D2', 0x4D2],
            ['0x_4_D_2_', 0x4D2],
            ['02333', 02333],
            ['0_2_3_3_3', 02333],
            ['.Inf', -log(0)],
            ['-.Inf', log(0)],
            ["'686e444'", '686e444'],
            ['686e444', 646e444],
            ['123456789123456789123456789123456789', '123456789123456789123456789123456789'],
            ['"foo\r\nbar"', "foo\r\nbar"],
            ["'foo#bar'", 'foo#bar'],
            ["'foo # bar'", 'foo # bar'],
            ["'#cfcfcf'", '#cfcfcf'],
            ['::form_base.html.twig', '::form_base.html.twig'],

            // Pre-YAML-1.2 booleans
            ["'y'", 'y'],
            ["'n'", 'n'],
            ["'yes'", 'yes'],
            ["'no'", 'no'],
            ["'on'", 'on'],
            ["'off'", 'off'],

            ['2007-10-30', gmmktime(0, 0, 0, 10, 30, 2007)],
            ['2007-10-30T02:59:43Z', gmmktime(2, 59, 43, 10, 30, 2007)],
            ['2007-10-30 02:59:43 Z', gmmktime(2, 59, 43, 10, 30, 2007)],
            ['1960-10-30 02:59:43 Z', gmmktime(2, 59, 43, 10, 30, 1960)],
            ['1730-10-30T02:59:43Z', gmmktime(2, 59, 43, 10, 30, 1730)],

            ['"a \\"string\\" with \'quoted strings inside\'"', 'a "string" with \'quoted strings inside\''],
            ["'a \"string\" with ''quoted strings inside'''", 'a "string" with \'quoted strings inside\''],

            // sequences
            // urls are no key value mapping. see #3609. Valid yaml "key: value" mappings require a space after the colon
            ['[foo, http://urls.are/no/mappings, false, null, 12]', ['foo', 'http://urls.are/no/mappings', false, null, 12]],
            ['[  foo  ,   bar , false  ,  null     ,  12  ]', ['foo', 'bar', false, null, 12]],
            ['[\'foo,bar\', \'foo bar\']', ['foo,bar', 'foo bar']],

            // mappings
            ['{foo: bar,bar: foo,false: false,null: null,integer: 12}', ['foo' => 'bar', 'bar' => 'foo', 'false' => false, 'null' => null, 'integer' => 12]],
            ['{ foo  : bar, bar : foo,  false  :   false,  null  :   null,  integer :  12  }', ['foo' => 'bar', 'bar' => 'foo', 'false' => false, 'null' => null, 'integer' => 12]],
            ['{foo: \'bar\', bar: \'foo: bar\'}', ['foo' => 'bar', 'bar' => 'foo: bar']],
            ['{\'foo\': \'bar\', "bar": \'foo: bar\'}', ['foo' => 'bar', 'bar' => 'foo: bar']],
            ['{\'foo\'\'\': \'bar\', "bar\"": \'foo: bar\'}', ['foo\'' => 'bar', 'bar"' => 'foo: bar']],
            ['{\'foo: \': \'bar\', "bar: ": \'foo: bar\'}', ['foo: ' => 'bar', 'bar: ' => 'foo: bar']],

            // nested sequences and mappings
            ['[foo, [bar, foo]]', ['foo', ['bar', 'foo']]],
            ['[foo, {bar: foo}]', ['foo', ['bar' => 'foo']]],
            ['{ foo: {bar: foo} }', ['foo' => ['bar' => 'foo']]],
            ['{ foo: [bar, foo] }', ['foo' => ['bar', 'foo']]],
            ['{ foo:{bar: foo} }', ['foo' => ['bar' => 'foo']]],
            ['{ foo:[bar, foo] }', ['foo' => ['bar', 'foo']]],

            ['[  foo, [  bar, foo  ]  ]', ['foo', ['bar', 'foo']]],

            ['[{ foo: {bar: foo} }]', [['foo' => ['bar' => 'foo']]]],

            ['[foo, [bar, [foo, [bar, foo]], foo]]', ['foo', ['bar', ['foo', ['bar', 'foo']], 'foo']]],

            ['[foo, {bar: foo, foo: [foo, {bar: foo}]}, [foo, {bar: foo}]]', ['foo', ['bar' => 'foo', 'foo' => ['foo', ['bar' => 'foo']]], ['foo', ['bar' => 'foo']]]],

            ['[foo, bar: { foo: bar }]', ['foo', '1' => ['bar' => ['foo' => 'bar']]]],
            ['[foo, \'@foo.baz\', { \'%foo%\': \'foo is %foo%\', bar: \'%foo%\' }, true, \'@service_container\']', ['foo', '@foo.baz', ['%foo%' => 'foo is %foo%', 'bar' => '%foo%'], true, '@service_container']],
        ];
    }

    public function getTestsForParseWithMapObjects()
    {
        return [
            ['', ''],
            ['null', null],
            ['false', false],
            ['true', true],
            ['12', 12],
            ['-12', -12],
            ['"quoted string"', 'quoted string'],
            ["'quoted string'", 'quoted string'],
            ['12.30e+02', 12.30e+02],
            ['0x4D2', 0x4D2],
            ['02333', 02333],
            ['.Inf', -log(0)],
            ['-.Inf', log(0)],
            ["'686e444'", '686e444'],
            ['686e444', 646e444],
            ['123456789123456789123456789123456789', '123456789123456789123456789123456789'],
            ['"foo\r\nbar"', "foo\r\nbar"],
            ["'foo#bar'", 'foo#bar'],
            ["'foo # bar'", 'foo # bar'],
            ["'#cfcfcf'", '#cfcfcf'],
            ['::form_base.html.twig', '::form_base.html.twig'],

            ['2007-10-30', gmmktime(0, 0, 0, 10, 30, 2007)],
            ['2007-10-30T02:59:43Z', gmmktime(2, 59, 43, 10, 30, 2007)],
            ['2007-10-30 02:59:43 Z', gmmktime(2, 59, 43, 10, 30, 2007)],
            ['1960-10-30 02:59:43 Z', gmmktime(2, 59, 43, 10, 30, 1960)],
            ['1730-10-30T02:59:43Z', gmmktime(2, 59, 43, 10, 30, 1730)],

            ['"a \\"string\\" with \'quoted strings inside\'"', 'a "string" with \'quoted strings inside\''],
            ["'a \"string\" with ''quoted strings inside'''", 'a "string" with \'quoted strings inside\''],

            // sequences
            // urls are no key value mapping. see #3609. Valid yaml "key: value" mappings require a space after the colon
            ['[foo, http://urls.are/no/mappings, false, null, 12]', ['foo', 'http://urls.are/no/mappings', false, null, 12]],
            ['[  foo  ,   bar , false  ,  null     ,  12  ]', ['foo', 'bar', false, null, 12]],
            ['[\'foo,bar\', \'foo bar\']', ['foo,bar', 'foo bar']],

            // mappings
            ['{foo: bar,bar: foo,false: false,null: null,integer: 12}', (object) ['foo' => 'bar', 'bar' => 'foo', 'false' => false, 'null' => null, 'integer' => 12]],
            ['{ foo  : bar, bar : foo,  false  :   false,  null  :   null,  integer :  12  }', (object) ['foo' => 'bar', 'bar' => 'foo', 'false' => false, 'null' => null, 'integer' => 12]],
            ['{foo: \'bar\', bar: \'foo: bar\'}', (object) ['foo' => 'bar', 'bar' => 'foo: bar']],
            ['{\'foo\': \'bar\', "bar": \'foo: bar\'}', (object) ['foo' => 'bar', 'bar' => 'foo: bar']],
            ['{\'foo\'\'\': \'bar\', "bar\"": \'foo: bar\'}', (object) ['foo\'' => 'bar', 'bar"' => 'foo: bar']],
            ['{\'foo: \': \'bar\', "bar: ": \'foo: bar\'}', (object) ['foo: ' => 'bar', 'bar: ' => 'foo: bar']],

            // nested sequences and mappings
            ['[foo, [bar, foo]]', ['foo', ['bar', 'foo']]],
            ['[foo, {bar: foo}]', ['foo', (object) ['bar' => 'foo']]],
            ['{ foo: {bar: foo} }', (object) ['foo' => (object) ['bar' => 'foo']]],
            ['{ foo: [bar, foo] }', (object) ['foo' => ['bar', 'foo']]],

            ['[  foo, [  bar, foo  ]  ]', ['foo', ['bar', 'foo']]],

            ['[{ foo: {bar: foo} }]', [(object) ['foo' => (object) ['bar' => 'foo']]]],

            ['[foo, [bar, [foo, [bar, foo]], foo]]', ['foo', ['bar', ['foo', ['bar', 'foo']], 'foo']]],

            ['[foo, {bar: foo, foo: [foo, {bar: foo}]}, [foo, {bar: foo}]]', ['foo', (object) ['bar' => 'foo', 'foo' => ['foo', (object) ['bar' => 'foo']]], ['foo', (object) ['bar' => 'foo']]]],

            ['[foo, bar: { foo: bar }]', ['foo', '1' => (object) ['bar' => (object) ['foo' => 'bar']]]],
            ['[foo, \'@foo.baz\', { \'%foo%\': \'foo is %foo%\', bar: \'%foo%\' }, true, \'@service_container\']', ['foo', '@foo.baz', (object) ['%foo%' => 'foo is %foo%', 'bar' => '%foo%'], true, '@service_container']],

            ['{}', new \stdClass],
            ['{ foo  : bar, bar : {}  }', (object) ['foo' => 'bar', 'bar' => new \stdClass]],
            ['{ foo  : [], bar : {}  }', (object) ['foo' => [], 'bar' => new \stdClass]],
            ['{foo: \'bar\', bar: {} }', (object) ['foo' => 'bar', 'bar' => new \stdClass]],
            ['{\'foo\': \'bar\', "bar": {}}', (object) ['foo' => 'bar', 'bar' => new \stdClass]],
            ['{\'foo\': \'bar\', "bar": \'{}\'}', (object) ['foo' => 'bar', 'bar' => '{}']],

            ['[foo, [{}, {}]]', ['foo', [new \stdClass, new \stdClass]]],
            ['[foo, [[], {}]]', ['foo', [[], new \stdClass]]],
            ['[foo, [[{}, {}], {}]]', ['foo', [[new \stdClass, new \stdClass], new \stdClass]]],
            ['[foo, {bar: {}}]', ['foo', '1' => (object) ['bar' => new \stdClass]]],
        ];
    }

    public function getTestsForDump()
    {
        return [
            ['null', null],
            ['false', false],
            ['true', true],
            ['12', 12],
            ["'1_2'", '1_2'],
            ['_12', '_12'],
            ["'12_'", '12_'],
            ["'quoted string'", 'quoted string'],
            ['!!float 1230', 12.30e+02],
            ['1234', 0x4D2],
            ['1243', 02333],
            ["'0x_4_D_2_'", '0x_4_D_2_'],
            ["'0_2_3_3_3'", '0_2_3_3_3'],
            ['.Inf', -log(0)],
            ['-.Inf', log(0)],
            ["'686e444'", '686e444'],
            ['"foo\r\nbar"', "foo\r\nbar"],
            ["'foo#bar'", 'foo#bar'],
            ["'foo # bar'", 'foo # bar'],
            ["'#cfcfcf'", '#cfcfcf'],

            ["'a \"string\" with ''quoted strings inside'''", 'a "string" with \'quoted strings inside\''],

            ["'-dash'", '-dash'],
            ["'-'", '-'],

            // Pre-YAML-1.2 booleans
            ["'y'", 'y'],
            ["'n'", 'n'],
            ["'yes'", 'yes'],
            ["'no'", 'no'],
            ["'on'", 'on'],
            ["'off'", 'off'],

            // sequences
            ['[foo, bar, false, null, 12]', ['foo', 'bar', false, null, 12]],
            ['[\'foo,bar\', \'foo bar\']', ['foo,bar', 'foo bar']],

            // mappings
            ['{ foo: bar, bar: foo, \'false\': false, \'null\': null, integer: 12 }', ['foo' => 'bar', 'bar' => 'foo', 'false' => false, 'null' => null, 'integer' => 12]],
            ['{ foo: bar, bar: \'foo: bar\' }', ['foo' => 'bar', 'bar' => 'foo: bar']],

            // nested sequences and mappings
            ['[foo, [bar, foo]]', ['foo', ['bar', 'foo']]],

            ['[foo, [bar, [foo, [bar, foo]], foo]]', ['foo', ['bar', ['foo', ['bar', 'foo']], 'foo']]],

            ['{ foo: { bar: foo } }', ['foo' => ['bar' => 'foo']]],

            ['[foo, { bar: foo }]', ['foo', ['bar' => 'foo']]],

            ['[foo, { bar: foo, foo: [foo, { bar: foo }] }, [foo, { bar: foo }]]', ['foo', ['bar' => 'foo', 'foo' => ['foo', ['bar' => 'foo']]], ['foo', ['bar' => 'foo']]]],

            ['[foo, \'@foo.baz\', { \'%foo%\': \'foo is %foo%\', bar: \'%foo%\' }, true, \'@service_container\']', ['foo', '@foo.baz', ['%foo%' => 'foo is %foo%', 'bar' => '%foo%'], true, '@service_container']],

            ['{ foo: { bar: { 1: 2, baz: 3 } } }', ['foo' => ['bar' => [1 => 2, 'baz' => 3]]]],
        ];
    }

    /**
     * @dataProvider getTimestampTests
     */
    public function test_parse_timestamp_as_unix_timestamp_by_default($yaml, $year, $month, $day, $hour, $minute, $second)
    {
        $this->assertSame(gmmktime($hour, $minute, $second, $month, $day, $year), Inline::parse($yaml));
    }

    /**
     * @dataProvider getTimestampTests
     */
    public function test_parse_timestamp_as_date_time_object($yaml, $year, $month, $day, $hour, $minute, $second, $timezone)
    {
        $expected = new \DateTime($yaml);
        $expected->setTimeZone(new \DateTimeZone('UTC'));
        $expected->setDate($year, $month, $day);

        if (PHP_VERSION_ID >= 70100) {
            $expected->setTime($hour, $minute, $second, 1000000 * ($second - (int) $second));
        } else {
            $expected->setTime($hour, $minute, $second);
        }

        $date = Inline::parse($yaml, Yaml::PARSE_DATETIME);
        $this->assertEquals($expected, $date);
        $this->assertSame($timezone, $date->format('O'));
    }

    public function getTimestampTests()
    {
        return [
            'canonical' => ['2001-12-15T02:59:43.1Z', 2001, 12, 15, 2, 59, 43.1, '+0000'],
            'ISO-8601' => ['2001-12-15t21:59:43.10-05:00', 2001, 12, 16, 2, 59, 43.1, '-0500'],
            'spaced' => ['2001-12-15 21:59:43.10 -5', 2001, 12, 16, 2, 59, 43.1, '-0500'],
            'date' => ['2001-12-15', 2001, 12, 15, 0, 0, 0, '+0000'],
        ];
    }

    /**
     * @dataProvider getTimestampTests
     */
    public function test_parse_nested_timestamp_list_as_date_time_object($yaml, $year, $month, $day, $hour, $minute, $second)
    {
        $expected = new \DateTime($yaml);
        $expected->setTimeZone(new \DateTimeZone('UTC'));
        $expected->setDate($year, $month, $day);
        if (PHP_VERSION_ID >= 70100) {
            $expected->setTime($hour, $minute, $second, 1000000 * ($second - (int) $second));
        } else {
            $expected->setTime($hour, $minute, $second);
        }

        $expectedNested = ['nested' => [$expected]];
        $yamlNested = "{nested: [$yaml]}";

        $this->assertEquals($expectedNested, Inline::parse($yamlNested, Yaml::PARSE_DATETIME));
    }

    /**
     * @dataProvider getDateTimeDumpTests
     */
    public function test_dump_date_time($dateTime, $expected)
    {
        $this->assertSame($expected, Inline::dump($dateTime));
    }

    public function getDateTimeDumpTests()
    {
        $tests = [];

        $dateTime = new \DateTime('2001-12-15 21:59:43', new \DateTimeZone('UTC'));
        $tests['date-time-utc'] = [$dateTime, '2001-12-15T21:59:43+00:00'];

        $dateTime = new \DateTimeImmutable('2001-07-15 21:59:43', new \DateTimeZone('Europe/Berlin'));
        $tests['immutable-date-time-europe-berlin'] = [$dateTime, '2001-07-15T21:59:43+02:00'];

        return $tests;
    }

    /**
     * @dataProvider getBinaryData
     */
    public function test_parse_binary_data($data)
    {
        $this->assertSame('Hello world', Inline::parse($data));
    }

    public function getBinaryData()
    {
        return [
            'enclosed with double quotes' => ['!!binary "SGVsbG8gd29ybGQ="'],
            'enclosed with single quotes' => ["!!binary 'SGVsbG8gd29ybGQ='"],
            'containing spaces' => ['!!binary  "SGVs bG8gd 29ybGQ="'],
        ];
    }

    /**
     * @dataProvider getInvalidBinaryData
     *
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_parse_invalid_binary_data($data, $expectedMessage)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectExceptionMessageRegExp($expectedMessage);
        } else {
            $this->setExpectedExceptionRegExp(ParseException::class, $expectedMessage);
        }

        Inline::parse($data);
    }

    public function getInvalidBinaryData()
    {
        return [
            'length not a multiple of four' => ['!!binary "SGVsbG8d29ybGQ="', '/The normalized base64 encoded data \(data without whitespace characters\) length must be a multiple of four \(\d+ bytes given\)/'],
            'invalid characters' => ['!!binary "SGVsbG8#d29ybGQ="', '/The base64 encoded data \(.*\) contains invalid characters/'],
            'too many equals characters' => ['!!binary "SGVsbG8gd29yb==="', '/The base64 encoded data \(.*\) contains invalid characters/'],
            'misplaced equals character' => ['!!binary "SGVsbG8gd29ybG=Q"', '/The base64 encoded data \(.*\) contains invalid characters/'],
        ];
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage Malformed inline YAML string: {this, is not, supported}.
     */
    public function test_not_supported_missing_value()
    {
        Inline::parse('{this, is not, supported}');
    }

    public function test_very_long_quoted_strings()
    {
        $longStringWithQuotes = str_repeat("x\r\n\\\"x\"x", 1000);

        $yamlString = Inline::dump(['longStringWithQuotes' => $longStringWithQuotes]);
        $arrayFromYaml = Inline::parse($yamlString);

        $this->assertEquals($longStringWithQuotes, $arrayFromYaml['longStringWithQuotes']);
    }

    public function test_omitted_mapping_key_is_parsed_as_colon()
    {
        $this->assertSame([':' => 'foo'], Inline::parse('{: foo}'));
    }

    public function test_boolean_mapping_keys_are_converted_to_strings()
    {
        $this->assertSame(['false' => 'foo'], Inline::parse('{false: foo}'));
        $this->assertSame(['true' => 'foo'], Inline::parse('{true: foo}'));
    }

    public function test_the_empty_string_is_a_valid_mapping_key()
    {
        $this->assertSame(['' => 'foo'], Inline::parse('{ "": foo }'));
    }
}
