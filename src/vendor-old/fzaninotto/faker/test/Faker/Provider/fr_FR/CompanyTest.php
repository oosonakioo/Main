<?php

namespace Faker\Test\Provider\fr_FR;

use Faker\Calculator\Luhn;
use Faker\Generator;
use Faker\Provider\fr_FR\Company;

class CompanyTest extends \PHPUnit_Framework_TestCase
{
    private $faker;

    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new Company($faker));
        $this->faker = $faker;
    }

    public function test_siret_returns_a_valid_siret()
    {
        $siret = $this->faker->siret(false);
        $this->assertRegExp("/^\d{14}$/", $siret);
        $this->assertTrue(Luhn::isValid($siret));
    }

    public function test_siret_returns_a_well_formatted_siret()
    {
        $siret = $this->faker->siret();
        $this->assertRegExp("/^\d{3}\s\d{3}\s\d{3}\s\d{5}$/", $siret);
        $siret = str_replace(' ', '', $siret);
        $this->assertTrue(Luhn::isValid($siret));
    }

    public function test_siren_returns_a_valid_siren()
    {
        $siren = $this->faker->siren(false);
        $this->assertRegExp("/^\d{9}$/", $siren);
        $this->assertTrue(Luhn::isValid($siren));
    }

    public function test_siren_returns_a_well_formatted_siren()
    {
        $siren = $this->faker->siren();
        $this->assertRegExp("/^\d{3}\s\d{3}\s\d{3}$/", $siren);
        $siren = str_replace(' ', '', $siren);
        $this->assertTrue(Luhn::isValid($siren));
    }

    public function test_catch_phrase_returns_valid_catch_phrase()
    {
        $this->assertTrue(TestableCompany::isCatchPhraseValid($this->faker->catchPhrase()));
    }

    public function test_is_catch_phrase_valid_returns_false_when_a_words_appears_twice()
    {
        $isCatchPhraseValid = TestableCompany::isCatchPhraseValid('La sécurité de rouler en toute sécurité');
        $this->assertFalse($isCatchPhraseValid);
    }

    public function test_is_catch_phrase_valid_returns_true_when_no_word_appears_twice()
    {
        $isCatchPhraseValid = TestableCompany::isCatchPhraseValid('La sécurité de rouler en toute simplicité');
        $this->assertTrue($isCatchPhraseValid);
    }
}

class TestableCompany extends Company
{
    public static function isCatchPhraseValid($catchPhrase)
    {
        return parent::isCatchPhraseValid($catchPhrase);
    }
}
