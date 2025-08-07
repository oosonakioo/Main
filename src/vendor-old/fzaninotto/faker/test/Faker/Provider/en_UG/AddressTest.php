<?php

namespace Faker\Test\Provider\en_UG;

use Faker\Generator;
use Faker\Provider\en_UG\Address;

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

    /**
     * @test
     */
    public function test_city_name()
    {
        $city = $this->faker->cityName();
        $this->assertNotEmpty($city);
        $this->assertInternalType('string', $city);
    }

    /**
     * @test
     */
    public function test_district()
    {
        $district = $this->faker->district();
        $this->assertNotEmpty($district);
        $this->assertInternalType('string', $district);
    }

    /**
     * @test
     */
    public function test_region()
    {
        $region = $this->faker->region();
        $this->assertNotEmpty($region);
        $this->assertInternaltype('string', $region);
    }
}
