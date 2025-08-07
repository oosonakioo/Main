<?php

namespace Faker\Test\Provider\ro_RO;

use Faker\Generator;
use Faker\Provider\ro_RO\PhoneNumber;

class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new PhoneNumber($faker));
        $this->faker = $faker;
    }

    public function test_phone_number_returns_normal_phone_number()
    {
        $this->assertRegExp('/^0(?:[23][13-7]|7\d)\d{7}$/', $this->faker->phoneNumber());
    }

    public function test_toll_free_phone_number_returns_toll_free_phone_number()
    {
        $this->assertRegExp('/^08(?:0[1267]|70)\d{6}$/', $this->faker->tollFreePhoneNumber());
    }

    public function test_premium_rate_phone_number_returns_premium_rate_phone_number()
    {
        $this->assertRegExp('/^090[036]\d{6}$/', $this->faker->premiumRatePhoneNumber());
    }
}
