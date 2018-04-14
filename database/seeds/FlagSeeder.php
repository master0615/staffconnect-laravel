<?php

use Illuminate\Database\Seeder;

class FlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('flags')->insert([
            [
                'fname' => "Uniform Required",
                'color' => "#E3F2FD",
            ],
            [
                'fname' => "Stock Needed",
                'color' => "#FFFDE7",
            ],
        ]);
    }
}
