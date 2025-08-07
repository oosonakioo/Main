<?php

namespace Faker\Test\Provider;

use Faker\Generator;
use Faker\Provider\Person;

class PersonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider firstNameProvider
     */
    public function test_first_name($gender, $expected)
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $this->assertContains($faker->firstName($gender), $expected);
    }

    public function firstNameProvider()
    {
        return [
            [null, ['John', 'Jane']],
            ['foobar', ['John', 'Jane']],
            ['male', ['John']],
            ['female', ['Jane']],
        ];
    }

    public function test_first_name_male()
    {
        $this->assertContains(Person::firstNameMale(), ['John']);
    }

    public function test_first_name_female()
    {
        $this->assertContains(Person::firstNameFemale(), ['Jane']);
    }

    /**
     * @dataProvider titleProvider
     */
    public function test_title($gender, $expected)
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $this->assertContains($faker->title($gender), $expected);
    }

    public function titleProvider()
    {
        return [
            [null, ['Mr.', 'Mrs.', 'Ms.', 'Miss', 'Dr.', 'Prof.']],
            ['foobar', ['Mr.', 'Mrs.', 'Ms.', 'Miss', 'Dr.', 'Prof.']],
            ['male', ['Mr.', 'Dr.', 'Prof.']],
            ['female', ['Mrs.', 'Ms.', 'Miss', 'Dr.', 'Prof.']],
        ];
    }

    public function test_title_male()
    {
        $this->assertContains(Person::titleMale(), ['Mr.', 'Dr.', 'Prof.']);
    }

    public function test_title_female()
    {
        $this->assertContains(Person::titleFemale(), ['Mrs.', 'Ms.', 'Miss', 'Dr.', 'Prof.']);
    }

    public function test_last_name_returns_doe()
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $this->assertEquals($faker->lastName(), 'Doe');
    }

    public function test_name_returns_first_name_and_last_name()
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $this->assertContains($faker->name(), ['John Doe', 'Jane Doe']);
        $this->assertContains($faker->name('foobar'), ['John Doe', 'Jane Doe']);
        $this->assertContains($faker->name('male'), ['John Doe']);
        $this->assertContains($faker->name('female'), ['Jane Doe']);
    }
}
