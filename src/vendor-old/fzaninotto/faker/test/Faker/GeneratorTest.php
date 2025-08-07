<?php

namespace Faker\Test;

use Faker\Generator;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function test_add_provider_gives_priority_to_newly_added_provider()
    {
        $generator = new Generator;
        $generator->addProvider(new FooProvider);
        $generator->addProvider(new BarProvider);
        $this->assertEquals('barfoo', $generator->format('fooFormatter'));
    }

    public function test_get_formatter_returns_callable()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $this->assertTrue(is_callable($generator->getFormatter('fooFormatter')));
    }

    public function test_get_formatter_returns_correct_formatter()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $expected = [$provider, 'fooFormatter'];
        $this->assertEquals($expected, $generator->getFormatter('fooFormatter'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_get_formatter_throws_exception_on_incorrect_provider()
    {
        $generator = new Generator;
        $generator->getFormatter('fooFormatter');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_get_formatter_throws_exception_on_incorrect_formatter()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $generator->getFormatter('barFormatter');
    }

    public function test_format_calls_formatter_on_provider()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $this->assertEquals('foobar', $generator->format('fooFormatter'));
    }

    public function test_format_transfers_arguments_to_formatter()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $this->assertEquals('bazfoo', $generator->format('fooFormatterWithArguments', ['foo']));
    }

    public function test_parse_returns_same_string_when_it_contains_no_curly_braces()
    {
        $generator = new Generator;
        $this->assertEquals('fooBar#?', $generator->parse('fooBar#?'));
    }

    public function test_parse_returns_string_with_tokens_replaced_by_formatters()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $this->assertEquals('This is foobar a text with foobar', $generator->parse('This is {{fooFormatter}} a text with {{ fooFormatter }}'));
    }

    public function test_magic_get_calls_format()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $this->assertEquals('foobar', $generator->fooFormatter);
    }

    public function test_magic_call_calls_format()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $this->assertEquals('foobar', $generator->fooFormatter());
    }

    public function test_magic_call_calls_format_with_arguments()
    {
        $generator = new Generator;
        $provider = new FooProvider;
        $generator->addProvider($provider);
        $this->assertEquals('bazfoo', $generator->fooFormatterWithArguments('foo'));
    }

    public function test_seed()
    {
        $generator = new Generator;

        $generator->seed(0);
        $mtRandWithSeedZero = mt_rand();
        $generator->seed(0);
        $this->assertEquals($mtRandWithSeedZero, mt_rand(), 'seed(0) should be deterministic.');

        $generator->seed();
        $mtRandWithoutSeed = mt_rand();
        $this->assertNotEquals($mtRandWithSeedZero, $mtRandWithoutSeed, 'seed() should be different than seed(0)');
        $generator->seed();
        $this->assertNotEquals($mtRandWithoutSeed, mt_rand(), 'seed() should not be deterministic.');

        $generator->seed('10');
        $this->assertTrue(true, 'seeding with a non int value doesn\'t throw an exception');
    }
}

class FooProvider
{
    public function fooFormatter()
    {
        return 'foobar';
    }

    public function fooFormatterWithArguments($value = '')
    {
        return 'baz'.$value;
    }
}

class BarProvider
{
    public function fooFormatter()
    {
        return 'barfoo';
    }
}
