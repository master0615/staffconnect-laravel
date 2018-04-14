<?php

use Illuminate\Database\Seeder;

class TrackingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tracking_categories')->insert([
            [
                'id' => '1',
                'cname' => 'Campaign',
                'staff_visibility' => 'visible',
                'client_visibility' => 'hidden',
            ],
            [
                'id' => '2',
                'cname' => 'Job Number',
                'staff_visibility' => 'visible_after_selection',
                'client_visibility' => 'visible',
            ],
        ]);

        DB::table('tracking_options')->insert([
            [
                'id' => '1',
                'tracking_cat_id' => '1',
                'oname' => 'Coke Zero',
            ],
            [
                'id' => '2',
                'tracking_cat_id' => '1',
                'oname' => 'Tequila Street Team',
            ],
            [
                'id' => '3',
                'tracking_cat_id' => '2',
                'oname' => '123ABC',
            ],
            [
                'id' => '4',
                'tracking_cat_id' => '2',
                'oname' => '346457BC',
            ],
            [
                'id' => '5',
                'tracking_cat_id' => '2',
                'oname' => 'asdt4e645',
            ],
            [
                'id' => '6',
                'tracking_cat_id' => '2',
                'oname' => '1232453ABC',
            ],
            [
                'id' => '7',
                'tracking_cat_id' => '2',
                'oname' => 'EE1E23ABC',
            ],
        ]);
    }
}
