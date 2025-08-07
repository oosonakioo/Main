<?php

namespace Faker\Test\Provider\id_ID;

use Faker\Generator;
use Faker\Provider\id_ID\Person;

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

    public function test_if_last_name_male_can_return_data()
    {
        $lastNameMale = $this->faker->lastNameMale();
        $this->assertNotEmpty($lastNameMale);
    }

    public function test_if_first_name_female_can_return_data()
    {
        $firstNameFemale = $this->faker->firstNameFemale();
        $this->assertNotEmpty($firstNameFemale);
    }

    public function test_if_last_name_female_can_return_data()
    {
        $lastNameFemale = $this->faker->lastNameFemale();
        $this->assertNotEmpty($lastNameFemale);
    }
}
