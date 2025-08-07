<?php

namespace Faker\Test\Provider\pt_PT;

use Faker\Generator;
use Faker\Provider\pt_PT\PhoneNumber;

class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new PhoneNumber($faker));
        $this->faker = $faker;
    }

    public function test_phone_number_returns_phone_number_with_or_without_prefix()
    {
        $this->assertRegExp('/^(9[1,2,3,6][0-9]{7})|(2[0-9]{8})|(\+351 [2][0-9]{8})|(\+351 9[1,2,3,6][0-9]{7})/', $this->faker->phoneNumber());
    }

    public function test_mobile_number_returns_mobile_number_with_or_without_prefix()
    {
        $this->assertRegExp('/^(9[1,2,3,6][0-9]{7})/', $this->faker->mobileNumber());
    }
}
