<?php

namespace Faker\Test\Provider\fr_CH;

use Faker\Generator;
use Faker\Provider\fr_CH\Company;
use Faker\Provider\fr_CH\Internet;
use Faker\Provider\fr_CH\Person;

class InternetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Faker\Generator
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

    /**
     * @test
     */
    public function email_is_valid()
    {
        $email = $this->faker->email();
        $this->assertNotFalse(filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}
