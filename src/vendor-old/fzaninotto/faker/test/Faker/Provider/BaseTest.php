<?php

namespace Faker\Test\Provider;

use Faker\Provider\Base as BaseProvider;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function test_random_digit_returns_integer()
    {
        $this->assertTrue(is_int(BaseProvider::randomDigit()));
    }

    public function test_random_digit_returns_digit()
    {
        $this->assertTrue(BaseProvider::randomDigit() >= 0);
        $this->assertTrue(BaseProvider::randomDigit() < 10);
    }

    public function test_random_digit_not_null_returns_not_null_digit()
    {
        $this->assertTrue(BaseProvider::randomDigitNotNull() > 0);
        $this->assertTrue(BaseProvider::randomDigitNotNull() < 10);
    }

    public function test_random_digit_not_returns_valid_digit()
    {
        for ($i = 0; $i <= 9; $i++) {
            $this->assertTrue(BaseProvider::randomDigitNot($i) >= 0);
            $this->assertTrue(BaseProvider::randomDigitNot($i) < 10);
            $this->assertTrue(BaseProvider::randomDigitNot($i) !== $i);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_random_number_throws_exception_when_called_with_a_max()
    {
        BaseProvider::randomNumber(5, 200);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_random_number_throws_exception_when_called_with_a_too_high_number_of_digits()
    {
        BaseProvider::randomNumber(10);
    }

    public function test_random_number_returns_integer()
    {
        $this->assertTrue(is_int(BaseProvider::randomNumber()));
        $this->assertTrue(is_int(BaseProvider::randomNumber(5, false)));
    }

    public function test_random_number_returns_digit()
    {
        $this->assertTrue(BaseProvider::randomNumber(3) >= 0);
        $this->assertTrue(BaseProvider::randomNumber(3) < 1000);
    }

    public function test_random_number_accepts_strict_param_to_enforce_number_size()
    {
        $this->assertEquals(5, strlen((string) BaseProvider::randomNumber(5, true)));
    }

    public function test_number_between()
    {
        $min = 5;
        $max = 6;

        $this->assertGreaterThanOrEqual($min, BaseProvider::numberBetween($min, $max));
        $this->assertGreaterThanOrEqual(BaseProvider::numberBetween($min, $max), $max);
    }

    public function test_number_between_accepts_zero_as_max()
    {
        $this->assertEquals(0, BaseProvider::numberBetween(0, 0));
    }

    public function test_random_float()
    {
        $min = 4;
        $max = 10;
        $nbMaxDecimals = 8;

        $result = BaseProvider::randomFloat($nbMaxDecimals, $min, $max);

        $parts = explode('.', $result);

        $this->assertInternalType('float', $result);
        $this->assertGreaterThanOrEqual($min, $result);
        $this->assertLessThanOrEqual($max, $result);
        $this->assertLessThanOrEqual($nbMaxDecimals, strlen($parts[1]));
    }

    public function test_random_letter_returns_string()
    {
        $this->assertTrue(is_string(BaseProvider::randomLetter()));
    }

    public function test_random_letter_returns_single_letter()
    {
        $this->assertEquals(1, strlen(BaseProvider::randomLetter()));
    }

    public function test_random_letter_returns_lowercase_letter()
    {
        $lowercaseLetters = 'abcdefghijklmnopqrstuvwxyz';
        $this->assertTrue(strpos($lowercaseLetters, BaseProvider::randomLetter()) !== false);
    }

    public function test_random_ascii_returns_string()
    {
        $this->assertTrue(is_string(BaseProvider::randomAscii()));
    }

    public function test_random_ascii_returns_single_character()
    {
        $this->assertEquals(1, strlen(BaseProvider::randomAscii()));
    }

    public function test_random_ascii_returns_ascii_character()
    {
        $lowercaseLetters = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
        $this->assertTrue(strpos($lowercaseLetters, BaseProvider::randomAscii()) !== false);
    }

    public function test_random_element_returns_null_when_array_empty()
    {
        $this->assertNull(BaseProvider::randomElement([]));
    }

    public function test_random_element_returns_element_from_array()
    {
        $elements = ['23', 'e', 32, '#'];
        $this->assertContains(BaseProvider::randomElement($elements), $elements);
    }

    public function test_random_element_returns_element_from_associative_array()
    {
        $elements = ['tata' => '23', 'toto' => 'e', 'tutu' => 32, 'titi' => '#'];
        $this->assertContains(BaseProvider::randomElement($elements), $elements);
    }

    public function test_shuffle_returns_string_when_passed_a_string_argument()
    {
        $this->assertInternalType('string', BaseProvider::shuffle('foo'));
    }

    public function test_shuffle_returns_array_when_passed_an_array_argument()
    {
        $this->assertInternalType('array', BaseProvider::shuffle([1, 2, 3]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_shuffle_throws_exception_when_passed_an_invalid_argument()
    {
        BaseProvider::shuffle(false);
    }

    public function test_shuffle_array_supports_empty_arrays()
    {
        $this->assertEquals([], BaseProvider::shuffleArray([]));
    }

    public function test_shuffle_array_returns_an_array_of_the_same_size()
    {
        $array = [1, 2, 3, 4, 5];
        $this->assertSameSize($array, BaseProvider::shuffleArray($array));
    }

    public function test_shuffle_array_returns_an_array_with_same_elements()
    {
        $array = [2, 4, 6, 8, 10];
        $shuffleArray = BaseProvider::shuffleArray($array);
        $this->assertContains(2, $shuffleArray);
        $this->assertContains(4, $shuffleArray);
        $this->assertContains(6, $shuffleArray);
        $this->assertContains(8, $shuffleArray);
        $this->assertContains(10, $shuffleArray);
    }

    public function test_shuffle_array_returns_a_different_array_than_the_original()
    {
        $arr = [1, 2, 3, 4, 5];
        $shuffledArray = BaseProvider::shuffleArray($arr);
        $this->assertNotEquals($arr, $shuffledArray);
    }

    public function test_shuffle_array_leaves_the_original_array_untouched()
    {
        $arr = [1, 2, 3, 4, 5];
        BaseProvider::shuffleArray($arr);
        $this->assertEquals($arr, [1, 2, 3, 4, 5]);
    }

    public function test_shuffle_string_supports_empty_strings()
    {
        $this->assertEquals('', BaseProvider::shuffleString(''));
    }

    public function test_shuffle_string_returns_an_string_of_the_same_size()
    {
        $string = 'abcdef';
        $this->assertEquals(strlen($string), strlen(BaseProvider::shuffleString($string)));
    }

    public function test_shuffle_string_returns_an_string_with_same_elements()
    {
        $string = 'acegi';
        $shuffleString = BaseProvider::shuffleString($string);
        $this->assertContains('a', $shuffleString);
        $this->assertContains('c', $shuffleString);
        $this->assertContains('e', $shuffleString);
        $this->assertContains('g', $shuffleString);
        $this->assertContains('i', $shuffleString);
    }

    public function test_shuffle_string_returns_a_different_string_than_the_original()
    {
        $string = 'abcdef';
        $shuffledString = BaseProvider::shuffleString($string);
        $this->assertNotEquals($string, $shuffledString);
    }

    public function test_shuffle_string_leaves_the_original_string_untouched()
    {
        $string = 'abcdef';
        BaseProvider::shuffleString($string);
        $this->assertEquals($string, 'abcdef');
    }

    public function test_numerify_returns_same_string_when_it_contains_no_hash_sign()
    {
        $this->assertEquals('fooBar?', BaseProvider::numerify('fooBar?'));
    }

    public function test_numerify_returns_string_with_hash_signs_replaced_by_digits()
    {
        $this->assertRegExp('/foo\dBa\dr/', BaseProvider::numerify('foo#Ba#r'));
    }

    public function test_numerify_returns_string_with_percentage_signs_replaced_by_digits()
    {
        $this->assertRegExp('/foo\dBa\dr/', BaseProvider::numerify('foo%Ba%r'));
    }

    public function test_numerify_returns_string_with_percentage_signs_replaced_by_not_null_digits()
    {
        $this->assertNotEquals('0', BaseProvider::numerify('%'));
    }

    public function test_numerify_can_generate_a_large_number_of_digits()
    {
        $largePattern = str_repeat('#', 20); // definitely larger than PHP_INT_MAX on all systems
        $this->assertEquals(20, strlen(BaseProvider::numerify($largePattern)));
    }

    public function test_lexify_returns_same_string_when_it_contains_no_question_mark()
    {
        $this->assertEquals('fooBar#', BaseProvider::lexify('fooBar#'));
    }

    public function test_lexify_returns_string_with_question_marks_replaced_by_letters()
    {
        $this->assertRegExp('/foo[a-z]Ba[a-z]r/', BaseProvider::lexify('foo?Ba?r'));
    }

    public function test_bothify_combines_numerify_and_lexify()
    {
        $this->assertRegExp('/foo[a-z]Ba\dr/', BaseProvider::bothify('foo?Ba#r'));
    }

    public function test_bothify_asterisk()
    {
        $this->assertRegExp('/foo([a-z]|\d)Ba([a-z]|\d)r/', BaseProvider::bothify('foo*Ba*r'));
    }

    public function test_bothify_utf()
    {
        $utf = 'œ∑´®†¥¨ˆøπ“‘和製╯°□°╯︵ ┻━┻🐵 🙈 ﺚﻣ ﻦﻔﺳ ﺲﻘﻄﺗ ﻮﺑﺎﻠﺘﺣﺪﻳﺩ،, ﺝﺰﻳﺮﺘﻳ ﺏﺎﺴﺘﺧﺩﺎﻣ ﺄﻧ ﺪﻧﻭ. ﺇﺫ ﻪﻧﺍ؟ ﺎﻠﺴﺗﺍﺭ ﻮﺘ';
        $this->assertRegExp('/'.$utf.'foo\dB[a-z]a([a-z]|\d)r/u', BaseProvider::bothify($utf.'foo#B?a*r'));
    }

    public function test_asciify_returns_same_string_when_it_contains_no_star_sign()
    {
        $this->assertEquals('fooBar?', BaseProvider::asciify('fooBar?'));
    }

    public function test_asciify_returns_string_with_star_signs_replaced_by_ascii_chars()
    {
        $this->assertRegExp('/foo.Ba.r/', BaseProvider::asciify('foo*Ba*r'));
    }

    public function regexifyBasicDataProvider()
    {
        return [
            ['azeQSDF1234', 'azeQSDF1234', 'does not change non regex chars'],
            ['foo(bar){1}', 'foobar', 'replaces regex characters'],
            ['', '', 'supports empty string'],
            ['/^foo(bar){1}$/', 'foobar', 'ignores regex delimiters'],
        ];
    }

    /**
     * @dataProvider regexifyBasicDataProvider
     */
    public function test_regexify_basic_features($input, $output, $message)
    {
        $this->assertEquals($output, BaseProvider::regexify($input), $message);
    }

    public function regexifyDataProvider()
    {
        return [
            ['\d', 'numbers'],
            ['\w', 'letters'],
            ['(a|b)', 'alternation'],
            ['[aeiou]', 'basic character class'],
            ['[a-z]', 'character class range'],
            ['[a-z1-9]', 'multiple character class range'],
            ['a*b+c?', 'single character quantifiers'],
            ['a{2}', 'brackets quantifiers'],
            ['a{2,3}', 'min-max brackets quantifiers'],
            ['[aeiou]{2,3}', 'brackets quantifiers on basic character class'],
            ['[a-z]{2,3}', 'brackets quantifiers on character class range'],
            ['(a|b){2,3}', 'brackets quantifiers on alternation'],
            ['\.\*\?\+', 'escaped characters'],
            ['[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}', 'complex regex'],
        ];
    }

    /**
     * @dataProvider regexifyDataProvider
     */
    public function test_regexify_supported_regex_syntax($pattern, $message)
    {
        $this->assertRegExp('/'.$pattern.'/', BaseProvider::regexify($pattern), 'Regexify supports '.$message);
    }

    public function test_optional_returns_provider_value_when_called_with_weight1()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $this->assertNotNull($faker->optional(100)->randomDigit);
    }

    public function test_optional_returns_null_when_called_with_weight0()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $this->assertNull($faker->optional(0)->randomDigit);
    }

    public function test_optional_allows_chaining_property_access()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $faker->addProvider(new \ArrayObject([1])); // hack because method_exists forbids stubs
        $this->assertEquals(1, $faker->optional(100)->count);
        $this->assertNull($faker->optional(0)->count);
    }

    public function test_optional_allows_chaining_method_call()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $faker->addProvider(new \ArrayObject([1])); // hack because method_exists forbids stubs
        $this->assertEquals(1, $faker->optional(100)->count());
        $this->assertNull($faker->optional(0)->count());
    }

    public function test_optional_allows_chaining_provider_call_randomly_return_null()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $values = [];
        for ($i = 0; $i < 10; $i++) {
            $values[] = $faker->optional()->randomDigit;
        }
        $this->assertContains(null, $values);

        $values = [];
        for ($i = 0; $i < 10; $i++) {
            $values[] = $faker->optional(50)->randomDigit;
        }
        $this->assertContains(null, $values);
    }

    /**
     * @link https://github.com/fzaninotto/Faker/issues/265
     */
    public function test_optional_percentage_and_weight()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $faker->addProvider(new \Faker\Provider\Miscellaneous($faker));

        $valuesOld = [];
        $valuesNew = [];

        for ($i = 0; $i < 10000; $i++) {
            $valuesOld[] = $faker->optional(0.5)->boolean(100);
            $valuesNew[] = $faker->optional(50)->boolean(100);
        }

        $this->assertEquals(
            round(array_sum($valuesOld) / 10000, 2),
            round(array_sum($valuesNew) / 10000, 2)
        );
    }

    public function test_unique_allows_chaining_property_access()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $faker->addProvider(new \ArrayObject([1])); // hack because method_exists forbids stubs
        $this->assertEquals(1, $faker->unique()->count);
    }

    public function test_unique_allows_chaining_method_call()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $faker->addProvider(new \ArrayObject([1])); // hack because method_exists forbids stubs
        $this->assertEquals(1, $faker->unique()->count());
    }

    public function test_unique_returns_only_unique_values()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $values = [];
        for ($i = 0; $i < 10; $i++) {
            $values[] = $faker->unique()->randomDigit;
        }
        sort($values);
        $this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $values);
    }

    /**
     * @expectedException OverflowException
     */
    public function test_unique_throws_exception_when_no_unique_value_can_be_generated()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        for ($i = 0; $i < 11; $i++) {
            $faker->unique()->randomDigit;
        }
    }

    public function test_unique_can_reset_uniques_when_passed_true_as_argument()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $values = [];
        for ($i = 0; $i < 10; $i++) {
            $values[] = $faker->unique()->randomDigit;
        }
        $values[] = $faker->unique(true)->randomDigit;
        for ($i = 0; $i < 9; $i++) {
            $values[] = $faker->unique()->randomDigit;
        }
        sort($values);
        $this->assertEquals([0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9], $values);
    }

    public function test_valid_allows_chaining_property_access()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $this->assertLessThan(10, $faker->valid()->randomDigit);
    }

    public function test_valid_allows_chaining_method_call()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $this->assertLessThan(10, $faker->valid()->numberBetween(5, 9));
    }

    public function test_valid_returns_only_valid_values()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $values = [];
        $evenValidator = function ($digit) {
            return $digit % 2 === 0;
        };
        for ($i = 0; $i < 50; $i++) {
            $values[$faker->valid($evenValidator)->randomDigit] = true;
        }
        $uniqueValues = array_keys($values);
        sort($uniqueValues);
        $this->assertEquals([0, 2, 4, 6, 8], $uniqueValues);
    }

    /**
     * @expectedException OverflowException
     */
    public function test_valid_throws_exception_when_no_valid_value_can_be_generated()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $evenValidator = function ($digit) {
            return $digit % 2 === 0;
        };
        for ($i = 0; $i < 11; $i++) {
            $faker->valid($evenValidator)->randomElement([1, 3, 5, 7, 9]);
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_valid_throws_exception_when_parameter_is_not_collable()
    {
        $faker = new \Faker\Generator;
        $faker->addProvider(new \Faker\Provider\Base($faker));
        $faker->valid(12)->randomElement([1, 3, 5, 7, 9]);
    }

    /**
     * @expectedException LengthException
     *
     * @expectedExceptionMessage Cannot get 2 elements, only 1 in array
     */
    public function test_random_elements_throws_when_requesting_too_many_keys()
    {
        BaseProvider::randomElements(['foo'], 2);
    }

    public function test_random_elements()
    {
        $this->assertCount(1, BaseProvider::randomElements(), 'Should work without any input');

        $empty = BaseProvider::randomElements([], 0);
        $this->assertInternalType('array', $empty);
        $this->assertCount(0, $empty);

        $shuffled = BaseProvider::randomElements(['foo', 'bar', 'baz'], 3);
        $this->assertContains('foo', $shuffled);
        $this->assertContains('bar', $shuffled);
        $this->assertContains('baz', $shuffled);
    }
}
