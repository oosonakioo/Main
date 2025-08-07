<?php

namespace Faker\Test\Provider\da_DK;

use Faker\Generator;
use Faker\Provider\da_DK\Company;
use Faker\Provider\da_DK\Internet;
use Faker\Provider\da_DK\Person;

class InternetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Generator
     */
    private $faker;

    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $faker->addProvider(new Internet($faker));
        $faker->addProvider(new Company($faker));
        $this->faker = $faker;
    }

    public function test_email_is_valid()
    {
        $email = $this->faker->email();
        $this->assertNotFalse(filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}
