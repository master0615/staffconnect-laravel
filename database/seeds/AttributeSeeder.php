<?php
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attributes')->insert([
            [
                'aname' => "Driver's Licence",
            ],
            [
                'aname' => "RSA",
            ],
        ]);
    }
}
