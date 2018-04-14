<?php
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(AttributeSeeder::class);
        $this->call(ProfileDataSeeder::class);
        $this->call(PayLevelsSeeder::class);
        $this->call(RatingsSeeder::class);
        $this->call(WorkAreasSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(ShiftStatusesSeeder::class);
        $this->call(StaffStatusesSeeder::class);
        $this->call(TrackingCategorySeeder::class);
        $this->call(ShiftDemo::class);
        $this->call(ShiftAdminNoteTypes::class);
        $this->call(FlagSeeder::class);
    }
}
