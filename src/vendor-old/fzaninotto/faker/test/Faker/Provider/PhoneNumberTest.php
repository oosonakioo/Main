<?php

namespace Faker\Test\Provider;

use Faker\Calculator\Luhn;
use Faker\Generator;
use Faker\Provider\PhoneNumber;

class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Generator
     */
    private $faker;

    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new PhoneNumber($faker));
        $this->faker = $faker;
    }

    public function test_phone_number_format()
    {
        $number = $this->faker->e164PhoneNumber();
        $this->assertRegExp('/^\+[0-9]{11,}$/', $number);
    }

    public function test_imei_returns_valid_number()
    {
        $imei = $this->faker->imei();
        $this->assertTrue(Luhn::isValid($imei));
    }
}
