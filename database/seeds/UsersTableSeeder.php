<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => '1',
                'fname' => 'Jeremy',
                'lname' => 'Jacob',
                'email' => 'jeremy@example.org',
                'password' => bcrypt('letmein'),
                'mob' => '0404404440',
                'sex' => 'male',
                'lvl' => 'owner',
                'ppic_a' => '',
            ],
            [
                'id' => '2',
                'fname' => 'Sarah',
                'lname' => 'Hart',
                'email' => 'sarah@staffconnect.net',
                'password' => bcrypt('letmein'),
                'mob' => '0402342344',
                'sex' => 'female',
                'lvl' => 'owner',
                'ppic_a' => '',
            ],
            [
                'id' => '3',
                'fname' => 'Kasia',
                'lname' => 'Sikora',
                'email' => 'Kasia@staffconnect.net',
                'password' => bcrypt('letmein'),
                'mob' => '043456543',
                'sex' => 'female',
                'lvl' => 'owner',
                'ppic_a' => '',
            ],
            [
                'id' => '4',
                'fname' => 'Idris',
                'lname' => 'Adamjy',
                'email' => 'idris@staffconnect.net',
                'password' => bcrypt('letmein'),
                'mob' => '0434564356',
                'sex' => 'male',
                'lvl' => 'owner',
                'ppic_a' => '',
            ],
            [
                'id' => '5',
                'fname' => 'Alexander',
                'lname' => 'Pavlov',
                'email' => 'alex@staffconnect.net',
                'password' => bcrypt('letmein'),
                'mob' => '04467565',
                'sex' => 'male',
                'lvl' => 'owner',
                'ppic_a' => '',
            ],
            [
                'id' => '6',
                'fname' => 'Damian',
                'lname' => 'Bodestyne',
                'email' => 'jeremy.jacob11@gmail.com',
                'password' => bcrypt('letmein'),
                'mob' => '0416660127',
                'sex' => 'male',
                'lvl' => 'staff',
                'ppic_a' => '',
            ],
            [
                'id' => '7',
                'fname' => 'Morgan',
                'lname' => 'Almasi',
                'email' => 'morgan@test.com',
                'password' => bcrypt('letmein'),
                'mob' => '0423334523',
                'sex' => 'female',
                'lvl' => 'staff',
                'ppic_a' => '',
            ],
            [
                'id' => '8',
                'fname' => 'Karen',
                'lname' => 'Davies',
                'email' => 'karen@test.com',
                'password' => bcrypt('letmein'),
                'mob' => '043456433',
                'sex' => 'female',
                'lvl' => 'staff',
                'ppic_a' => '',
            ],
            [
                'id' => '9',
                'fname' => 'Felicity',
                'lname' => 'Waters',
                'email' => 'felicity@test.com',
                'password' => bcrypt('letmein'),
                'mob' => '04435436',
                'sex' => 'female',
                'lvl' => 'staff',
                'ppic_a' => '',
            ],
            [
                'id' => '10',
                'fname' => 'Admin2',
                'lname' => 'Test',
                'email' => 'admin2@staffconnect.net',
                'password' => bcrypt('letmein'),
                'mob' => '0434564356',
                'sex' => 'male',
                'lvl' => 'admin',
                'ppic_a' => '',
            ],
            [
                'id' => '11',
                'fname' => 'Admin3',
                'lname' => 'Test',
                'email' => 'admin3@staffconnect.net',
                'password' => bcrypt('letmein'),
                'mob' => '0434564356',
                'sex' => 'female',
                'lvl' => 'admin',
                'ppic_a' => '',
            ],
        ]);
    }
}
