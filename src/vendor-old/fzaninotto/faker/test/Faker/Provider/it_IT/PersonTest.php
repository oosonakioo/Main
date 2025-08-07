<?php

namespace Faker\Test\Provider\it_IT;

use Faker\Generator;
use Faker\Provider\it_IT\Person;

class PersonTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $this->faker = $faker;
    }

    public function test_if_tax_id_can_return_data()
    {
        $taxId = $this->faker->taxId();
        $this->assertRegExp('/^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$/', $taxId);
    }
}
