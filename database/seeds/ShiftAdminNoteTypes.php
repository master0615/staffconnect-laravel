<?php

use Illuminate\Database\Seeder;

class ShiftAdminNoteTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shift_admin_note_types')->insert([
            [
                'tname' => "Accounting",
                'color' => "#E3F2FD",
            ],
            [
                'tname' => "Logistics",
                'color' => "#FFFDE7",
            ],
        ]);
    }
}
