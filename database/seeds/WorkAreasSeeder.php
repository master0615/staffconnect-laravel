<?php
use Illuminate\Database\Seeder;

class WorkAreasSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('work_area_categories')->insert([
            [
                'id' => '1',
                'cname' => 'Australia'
            ],
            [
                'id' => '2',
                'cname' => 'New Zealand'
            ]
        ]);
        
        DB::table('work_areas')->insert([
            [
                'aname' => "WA",
                'work_area_cat_id' => '1'
            ],
            [
                'aname' => "NSW",
                'work_area_cat_id' => '1'
            ],
            [
                'aname' => "VIC",
                'work_area_cat_id' => '1'
            ],
            [
                'aname' => "Wellington",
                'work_area_cat_id' => '2'
            ],
            [
                'aname' => "Auckland",
                'work_area_cat_id' => '2'
            ]
        ]);
    }
}
