<?php

namespace Faker\Test\Provider\en_PH;

use Faker\Generator;
use Faker\Provider\en_PH\Address;

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

    public function test_province()
    {
        $province = $this->faker->province();
        $this->assertNotEmpty($province);
        $this->assertInternalType('string', $province);
    }

    public function test_city()
    {
        $city = $this->faker->city();
        $this->assertNotEmpty($city);
        $this->assertInternalType('string', $city);
    }

    public function test_municipality()
    {
        $municipality = $this->faker->municipality();
        $this->assertNotEmpty($municipality);
        $this->assertInternalType('string', $municipality);
    }

    public function test_barangay()
    {
        $barangay = $this->faker->barangay();
        $this->assertInternalType('string', $barangay);
    }
}
