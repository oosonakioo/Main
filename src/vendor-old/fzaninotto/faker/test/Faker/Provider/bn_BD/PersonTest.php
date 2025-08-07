<?php

namespace Faker\Test\Provider\bn_BD;

use Faker\Generator;
use Faker\Provider\bn_BD\Person;

class PersonTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $this->faker = $faker;
    }

    public function test_if_first_name_male_can_return_data()
    {
        $firstNameMale = $this->faker->firstNameMale();
        $this->assertNotEmpty($firstNameMale);
    }

    public function test_if_first_name_female_can_return_data()
    {
        $firstNameFemale = $this->faker->firstNameFemale();
        $this->assertNotEmpty($firstNameFemale);
    }
}
