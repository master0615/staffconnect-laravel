<?php
use Illuminate\Database\Seeder;

class ShiftStatusesSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //priority for determining status when mutiple roles
        DB::table('shift_statuses')->insert([
            [
                'id' => '1',
                'status' => 'Booking', // client requests a booking
                'priority' => '20',
                'bg_color' => '#00BCD4',
                'border_color' => '#00BCD4',
            ],
            [
                'id' => '2',
                'status' => 'Quote', // quote sent to client
                'priority' => '30',
                'bg_color' => '#009688',
                'border_color' => '#009688',
            ],
            [
                'id' => '3',
                'status' => 'Cancelled', // cancelled but may be restored / rebooked in future
                'priority' => '101',
                'bg_color' => '#E0E0E0',
                'border_color' => '#E0E0E0',
            ],
            [
                'id' => '4',
                'status' => 'Replacement Requested', // at least one selected staff requested replacement
                'priority' => '98',
                'bg_color' => '#FFEB3B',
                'border_color' => '#FFEB3B',
            ],
            [
                'id' => '5',
                'status' => 'Unfilled', // not enough applicants or selected staff
                'priority' => '50',
                'bg_color' => '#E91E63',
                'border_color' => '#E91E63',
            ],
            [
                'id' => '6',
                'status' => 'Enough Applicants', // enough applicants to fill shift
                'priority' => '49',
                'bg_color' => '#9C27B0',
                'border_color' => '#9C27B0',
            ],
            [
                'id' => '7',
                'status' => 'Filled', // enough staff selected
                'priority' => '48',
                'bg_color' => '#2196F3',
                'border_color' => '#2196F3',
            ],
            [
                'id' => '8',
                'status' => 'Confirmed', // all selected staff confirmed
                'priority' => '47',
                'bg_color' => '#8BC34A',
                'border_color' => '#8BC34A',
            ],
            [
                'id' => '9',
                'status' => 'Checked In', // all selected staff checked in
                'priority' => '46',
                'bg_color' => '#4CAF50',
                'border_color' => '#4CAF50',
            ],
            [
                'id' => '10',
                'status' => 'No Show',
                'priority' => '99',
                'bg_color' => '#F44336',
                'border_color' => '#F44336',
            ],
            [
                'id' => '11',
                'status' => 'Checked Out', // all selected staff checked out
                'priority' => '45',
                'bg_color' => '#1B5E20',
                'border_color' => '#1B5E20',
            ],
            [
                'id' => '12',
                'status' => 'Reports Submitted', // all assigned completion reports submitted
                'priority' => '88',
                'bg_color' => '#8D6E63',
                'border_color' => '#8D6E63',
            ],
            [
                'id' => '13',
                'status' => 'Past',
                'priority' => '89',
                'bg_color' => '#FF8A65',
                'border_color' => '#FF8A65',
            ],
            [
                'id' => '14',
                'status' => 'Staff Completed', // all staff marked complete
                'priority' => '87',
                'bg_color' => '#795548',
                'border_color' => '#795548',
            ],
            [
                'id' => '15',
                'status' => 'Admin Completed', // admin marked shift complete
                'priority' => '100',
                'bg_color' => '#9E9E9E',
                'border_color' => '#9E9E9E',
            ],
        ]);
    }
}
