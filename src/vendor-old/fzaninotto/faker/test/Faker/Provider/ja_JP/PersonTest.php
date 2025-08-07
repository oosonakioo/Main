<?php

namespace Faker\Test\Provider\ja_JP;

use Faker\Generator;
use Faker\Provider\ja_JP\Person;

class PersonTest extends \PHPUnit_Framework_TestCase
{
    public function test_kana_name_returns_aota_minoru()
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $faker->seed(1);

        $this->assertEquals('アオタ ミノル', $faker->kanaName);
    }

    public function test_first_kana_name_returns_haruka()
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $faker->seed(1);

        $this->assertEquals('ハルカ', $faker->firstKanaName);
    }

    public function test_last_kana_name_returns_nakajima()
    {
        $faker = new Generator;
        $faker->addProvider(new Person($faker));
        $faker->seed(1);

        $this->assertEquals('ナカジマ', $faker->lastKanaName);
    }
}
