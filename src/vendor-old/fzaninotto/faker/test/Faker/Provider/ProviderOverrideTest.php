<?php

/**
 * @author Mark van der Velden <mark@dynom.nl>
 */

namespace Faker\Test\Provider;

use Faker;

/**
 * Class ProviderOverrideTest
 */
class ProviderOverrideTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Constants with regular expression patterns for testing the output.
     *
     * Regular expressions are sensitive for malformed strings (e.g.: strings with incorrect encodings) so by using
     * PCRE for the tests, even though they seem fairly pointless, we test for incorrect encodings also.
     */
    const TEST_STRING_REGEX = '/.+/u';

    /**
     * Slightly more specific for e-mail, the point isn't to properly validate e-mails.
     */
    const TEST_EMAIL_REGEX = '/^(.+)@(.+)$/ui';

    /**
     * @dataProvider localeDataProvider
     *
     * @param  string  $locale
     */
    public function test_address($locale = null)
    {
        $faker = Faker\Factory::create($locale);

        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->city);
        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->postcode);
        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->address);
        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->country);
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @param  string  $locale
     */
    public function test_company($locale = null)
    {
        $faker = Faker\Factory::create($locale);

        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->company);
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @param  string  $locale
     */
    public function test_date_time($locale = null)
    {
        $faker = Faker\Factory::create($locale);

        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->century);
        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->timezone);
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @param  string  $locale
     */
    public function test_internet($locale = null)
    {
        $faker = Faker\Factory::create($locale);

        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->userName);

        $this->assertRegExp(static::TEST_EMAIL_REGEX, $faker->email);
        $this->assertRegExp(static::TEST_EMAIL_REGEX, $faker->safeEmail);
        $this->assertRegExp(static::TEST_EMAIL_REGEX, $faker->freeEmail);
        $this->assertRegExp(static::TEST_EMAIL_REGEX, $faker->companyEmail);
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @param  string  $locale
     */
    public function test_person($locale = null)
    {
        $faker = Faker\Factory::create($locale);

        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->name);
        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->title);
        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->firstName);
        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->lastName);
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @param  string  $locale
     */
    public function test_phone_number($locale = null)
    {
        $faker = Faker\Factory::create($locale);

        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->phoneNumber);
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @param  string  $locale
     */
    public function test_user_agent($locale = null)
    {
        $faker = Faker\Factory::create($locale);

        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->userAgent);
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @param  null  $locale
     * @param  string  $locale
     */
    public function test_uuid($locale = null)
    {
        $faker = Faker\Factory::create($locale);

        $this->assertRegExp(static::TEST_STRING_REGEX, $faker->uuid);
    }

    /**
     * @return array
     */
    public function localeDataProvider()
    {
        $locales = $this->getAllLocales();
        $data = [];

        foreach ($locales as $locale) {
            $data[] = [
                $locale,
            ];
        }

        return $data;
    }

    /**
     * Returns all locales as array values
     *
     * @return array
     */
    private function getAllLocales()
    {
        static $locales = [];

        if (! empty($locales)) {
            return $locales;
        }

        // Finding all PHP files in the xx_XX directories
        $providerDir = __DIR__.'/../../../src/Faker/Provider';
        foreach (glob($providerDir.'/*_*/*.php') as $file) {
            $localisation = basename(dirname($file));

            if (isset($locales[$localisation])) {
                continue;
            }

            $locales[$localisation] = $localisation;
        }

        return $locales;
    }
}
