<?php

namespace Faker\Provider\en_AU;

use Faker\Generator;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Faker\Generator
     */
    private $faker;

    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new Address($faker));
        $this->faker = $faker;
    }

    public function test_city_prefix()
    {
        $cityPrefix = $this->faker->cityPrefix();
        $this->assertNotEmpty($cityPrefix);
        $this->assertInternalType('string', $cityPrefix);
        $this->assertRegExp('/[A-Z][a-z]+/', $cityPrefix);
    }

    public function test_street_suffix()
    {
        $streetSuffix = $this->faker->streetSuffix();
        $this->assertNotEmpty($streetSuffix);
        $this->assertInternalType('string', $streetSuffix);
        $this->assertRegExp('/[A-Z][a-z]+/', $streetSuffix);
    }

    public function test_state()
    {
        $state = $this->faker->state();
        $this->assertNotEmpty($state);
        $this->assertInternalType('string', $state);
        $this->assertRegExp('/[A-Z][a-z]+/', $state);
    }
}
