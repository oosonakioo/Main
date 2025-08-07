<?php

namespace Faker\Test\Provider\de_DE;

use Faker\Generator;
use Faker\Provider\de_DE\Company;
use Faker\Provider\de_DE\Internet;
use Faker\Provider\de_DE\Person;

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
