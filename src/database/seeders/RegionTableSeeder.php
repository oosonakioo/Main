<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loop = 0;
        $json = File::get('database/data/region.json');
        $data = json_decode($json);
        foreach ($data as $obj) {
            $loop++;
            DB::table('regions')->insert([
                'main_id' => $obj->id,
                'parent_regions_id' => $obj->parent_categories_id,
                'menu' => $obj->menu,
                'sort' => $obj->sort,
                'active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            // insert translation
            DB::table('regions_translations')->insert([
                'regions_id' => $loop,
                'title' => $obj->title_th,
                'detail' => '',
                'locale' => 'th',
            ]);
            DB::table('regions_translations')->insert([
                'regions_id' => $loop,
                'title' => $obj->title_en,
                'detail' => '',
                'locale' => 'en',
            ]);
        }

        $json = File::get('database/data/province.json');
        $data = json_decode($json);
        foreach ($data as $obj) {
            $loop++;
            DB::table('regions')->insert([
                'main_id' => $obj->id,
                'parent_regions_id' => $obj->parent_categories_id,
                'menu' => $obj->menu,
                'sort' => $obj->sort,
                'active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            DB::table('regions_translations')->insert([
                'regions_id' => $loop,
                'title' => $obj->title_th,
                'detail' => '',
                'locale' => 'th',
            ]);
            DB::table('regions_translations')->insert([
                'regions_id' => $loop,
                'title' => $obj->title_en,
                'detail' => '',
                'locale' => 'en',
            ]);
        }

        $json = File::get('database/data/district.json');
        $data = json_decode($json);
        foreach ($data as $obj) {
            $loop++;
            DB::table('regions')->insert([
                'main_id' => $obj->id,
                'parent_regions_id' => $obj->parent_categories_id,
                'menu' => $obj->menu,
                'sort' => $obj->sort,
                'active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            DB::table('regions_translations')->insert([
                'regions_id' => $loop,
                'title' => $obj->title_th,
                'detail' => '',
                'locale' => 'th',
            ]);
            DB::table('regions_translations')->insert([
                'regions_id' => $loop,
                'title' => $obj->title_en,
                'detail' => '',
                'locale' => 'en',
            ]);
        }

        $json = File::get('database/data/subdistrict.json');
        $data = json_decode($json);
        foreach ($data as $obj) {
            $loop++;
            DB::table('regions')->insert([
                'main_id' => $obj->id,
                'parent_regions_id' => $obj->parent_categories_id,
                'menu' => $obj->menu,
                'sort' => $obj->sort,
                'active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            DB::table('regions_translations')->insert([
                'regions_id' => $loop,
                'title' => $obj->title_th,
                'detail' => '',
                'locale' => 'th',
            ]);
            DB::table('regions_translations')->insert([
                'regions_id' => $loop,
                'title' => $obj->title_en,
                'detail' => '',
                'locale' => 'en',
            ]);
        }
    }
}
