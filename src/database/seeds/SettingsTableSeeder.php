<?php

use App\Models\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                Settings::KEY => Settings::WEB_TITLE,
                Settings::VALUE => 'Laravel Starter Kit',
            ],
            [
                Settings::KEY => Settings::WEB_DESC,
                Settings::VALUE => 'Build Website by Laravel Framework',
            ],
            [
                Settings::KEY => Settings::WEB_KEYWORD,
                Settings::VALUE => 'website,template,laravel',
            ],
        ];
        DB::table('settings')->insert($settings);
    }
}
