<?php

namespace Faker\Provider\en_CA;

use Faker\Generator;

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
     * Test the validity of province
     */
    public function test_province()
    {
        $province = $this->faker->province();
        $this->assertNotEmpty($province);
        $this->assertInternalType('string', $province);
        $this->assertRegExp('/[A-Z][a-z]+/', $province);
    }

    /**
     * Test the validity of province abbreviation
     */
    public function test_province_abbr()
    {
        $provinceAbbr = $this->faker->provinceAbbr();
        $this->assertNotEmpty($provinceAbbr);
        $this->assertInternalType('string', $provinceAbbr);
        $this->assertRegExp('/^[A-Z]{2}$/', $provinceAbbr);
    }

    /**
     * Test the validity of postcode letter
     */
    public function test_postcode_letter()
    {
        $postcodeLetter = $this->faker->randomPostcodeLetter();
        $this->assertNotEmpty($postcodeLetter);
        $this->assertInternalType('string', $postcodeLetter);
        $this->assertRegExp('/^[A-Z]{1}$/', $postcodeLetter);
    }

    /**
     * Test the validity of Canadian postcode
     */
    public function test_postcode()
    {
        $postcode = $this->faker->postcode();
        $this->assertNotEmpty($postcode);
        $this->assertInternalType('string', $postcode);
        $this->assertRegExp('/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/', $postcode);
    }
}
