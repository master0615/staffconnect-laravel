<?php
use Illuminate\Database\Seeder;

class PayLevelsSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pay_categories')->insert([
            [
                'id' => '1',
                'cname' => 'Promotions'
            ],
            [
                'id' => '2',
                'cname' => 'Modelling'
            ]
        ]);
        
        DB::table('pay_levels')->insert([
            [
                'pname' => "Beginner",
                'pay_rate' => "15",
                'pay_rate_type' => 'phr',
                'pay_cat_id' => '1'
            ],
            [
                'pname' => "Intermediate",
                'pay_rate' => "18",
                'pay_rate_type' => 'phr',
                'pay_cat_id' => '1'
            ],
            [
                'pname' => "Advanced",
                'pay_rate' => "20",
                'pay_rate_type' => 'phr',
                'pay_cat_id' => '1'
            ]
        ]);
    }
}
