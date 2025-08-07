<?php

namespace Faker\Test\Provider\it_IT;

use Faker\Generator;
use Faker\Provider\it_IT\Company;

class CompanyTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new Company($faker));
        $this->faker = $faker;
    }

    public function test_if_tax_id_can_return_data()
    {
        $vatId = $this->faker->vatId();
        $this->assertRegExp('/^IT[0-9]{11}$/', $vatId);
    }
}
