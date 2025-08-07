<?php

namespace Faker\Test\Provider\en_ZA;

use Faker\Generator;
use Faker\Provider\en_ZA\Company;

class CompanyTest extends \PHPUnit_Framework_TestCase
{
    private $faker;

    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new Company($faker));
        $this->faker = $faker;
    }

    public function test_generate_valid_company_number()
    {
        $companyRegNo = $this->faker->companyNumber();

        $this->assertEquals(14, strlen($companyRegNo));
        $this->assertRegExp('#^\d{4}/\d{6}/\d{2}$#', $companyRegNo);
    }
}
