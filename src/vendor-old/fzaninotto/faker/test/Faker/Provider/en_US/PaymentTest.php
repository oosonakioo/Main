<?php

namespace Faker\Provider\en_US;

use Faker\Generator;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Generator
     */
    private $faker;

    protected function setUp()
    {
        $faker = new Generator;
        $faker->addProvider(new Payment($faker));
        $this->faker = $faker;
    }

    public function test_bank_account_number()
    {
        $accNo = $this->faker->bankAccountNumber;
        $this->assertTrue(ctype_digit($accNo));
        $this->assertLessThanOrEqual(17, strlen($accNo));
    }

    public function test_bank_routing_number()
    {
        $routingNo = $this->faker->bankRoutingNumber;
        $this->assertRegExp('/^\d{9}$/', $routingNo);
        $this->assertEquals(Payment::calculateRoutingNumberChecksum($routingNo), $routingNo[8]);
    }

    public function routingNumberProvider()
    {
        return [
            ['122105155'],
            ['082000549'],
            ['121122676'],
            ['122235821'],
            ['102101645'],
            ['102000021'],
            ['123103729'],
            ['071904779'],
            ['081202759'],
            ['074900783'],
            ['104000029'],
            ['073000545'],
            ['101000187'],
            ['042100175'],
            ['083900363'],
            ['091215927'],
            ['091300023'],
            ['091000022'],
            ['081000210'],
            ['101200453'],
            ['092900383'],
            ['104000029'],
            ['121201694'],
            ['107002312'],
            ['091300023'],
            ['041202582'],
            ['042000013'],
            ['123000220'],
            ['091408501'],
            ['064000059'],
            ['124302150'],
            ['125000105'],
            ['075000022'],
            ['307070115'],
            ['091000022'],
        ];
    }

    /**
     * @dataProvider routingNumberProvider
     */
    public function test_calculate_routing_number_checksum($routingNo)
    {
        $this->assertEquals($routingNo[8], Payment::calculateRoutingNumberChecksum($routingNo), $routingNo);
    }
}
