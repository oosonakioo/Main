<?php

namespace Faker\Test\Provider;

use Faker\Provider\Miscellaneous;

class MiscellaneousTest extends \PHPUnit_Framework_TestCase
{
    public function test_boolean()
    {
        $this->assertContains(Miscellaneous::boolean(), [true, false]);
    }

    public function test_md5()
    {
        $this->assertRegExp('/^[a-z0-9]{32}$/', Miscellaneous::md5());
    }

    public function test_sha1()
    {
        $this->assertRegExp('/^[a-z0-9]{40}$/', Miscellaneous::sha1());
    }

    public function test_sha256()
    {
        $this->assertRegExp('/^[a-z0-9]{64}$/', Miscellaneous::sha256());
    }

    public function test_locale()
    {
        $this->assertRegExp('/^[a-z]{2,3}_[A-Z]{2}$/', Miscellaneous::locale());
    }

    public function test_country_code()
    {
        $this->assertRegExp('/^[A-Z]{2}$/', Miscellaneous::countryCode());
    }

    public function test_country_iso_alpha3()
    {
        $this->assertRegExp('/^[A-Z]{3}$/', Miscellaneous::countryISOAlpha3());
    }

    public function test_language()
    {
        $this->assertRegExp('/^[a-z]{2}$/', Miscellaneous::languageCode());
    }

    public function test_currency_code()
    {
        $this->assertRegExp('/^[A-Z]{3}$/', Miscellaneous::currencyCode());
    }
}
