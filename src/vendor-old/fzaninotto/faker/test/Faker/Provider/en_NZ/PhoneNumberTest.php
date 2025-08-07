<?php

namespace Faker\Test\Provider\en_NZ;

use Faker\Generator;
use Faker\Provider\en_NZ\PhoneNumber;

class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Faker\Generator
     */
    private $faker;

    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new PhoneNumber($faker));
        $this->faker = $faker;
    }

    public function test_if_phone_number_can_return_data()
    {
        $number = $this->faker->phoneNumber;
        $this->assertNotEmpty($number);
    }

    public function phoneNumberFormat()
    {
        $number = $this->faker->phoneNumber;
        $this->assertRegExp('/(^\([0]\d{1}\))(\d{7}$)|(^\([0][2]\d{1}\))(\d{6,8}$)|([0][8][0][0])([\s])(\d{5,8}$)/', $number);
    }
}
