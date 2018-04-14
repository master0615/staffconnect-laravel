<?php

use Illuminate\Database\Seeder;

class StaffStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('staff_statuses')->insert([
            [
                'id' => '1',
                'status' => 'Applied',
                'priority' => '2',
                'selected' => '0',
                'bg_color' => '#00BCD4',
                'border_color' => '#00BCD4',
                'message' => "You have applied for this role.",
            ],
            [
                'id' => '2',
                'status' => 'Standby',
                'priority' => '3',
                'selected' => '1',
                'bg_color' => '#673AB7',
                'border_color' => '#673AB7',
                'message' => "You are on standby for this role.",
            ],
            [
                'id' => '3',
                'status' => 'Selected',
                'priority' => '4',
                'selected' => '1',
                'bg_color' => '#00BCD4',
                'border_color' => '#00BCD4',
                'message' => "You have been selected for this role.",
            ],
            [
                'id' => '4',
                'status' => 'Confirmed',
                'priority' => '5',
                'selected' => '1',
                'bg_color' => '#8BC34A',
                'border_color' => '#8BC34A',
                'message' => "You are confirmed for this role.",
            ],
            [
                'id' => '5',
                'status' => 'Checked In Attempted',
                'priority' => '5',
                'selected' => '0',
                'bg_color' => '#CDDC39',
                'border_color' => '#CDDC39',
                'message' => "Check-in unsuccessful.",
            ],
            [
                'id' => '6',
                'status' => 'Checked In',
                'priority' => '6',
                'selected' => '0',
                'bg_color' => '#4CAF50',
                'border_color' => '#4CAF50',
                'message' => "You have checked-in",
            ],
            [
                'id' => '7',
                'status' => 'No Show',
                'priority' => '100',
                'selected' => '1',
                'bg_color' => '#F44336',
                'border_color' => '#F44336',
                'message' => "You did not check-in to this role on time.",
            ],
            [
                'id' => '8',
                'status' => 'Check Out Attempted',
                'priority' => '8',
                'selected' => '1',
                'bg_color' => '#1B5E20',
                'border_color' => '#1B5E20',
                'message' => "Check out unsuccessful.",
            ],
            [
                'id' => '9',
                'status' => 'Checked Out',
                'priority' => '9',
                'selected' => '1',
                'bg_color' => '#1B5E20',
                'border_color' => '#1B5E20',
                'message' => "You have checked-out.",
            ],
            [
                'id' => '10',
                'status' => 'Replacement Requested',
                'priority' => '99',
                'selected' => '1',
                'bg_color' => '#FFEB3B',
                'border_color' => '#FFEB3B',
                'message' => "You have requested a replacement for this role.",
            ],
            [
                'id' => '11',
                'status' => 'Standby Replacement Requested',
                'priority' => '98',
                'selected' => '1',
                'bg_color' => '#FFEB3B',
                'border_color' => '#FFEB3B',
                'message' => "You have requested a replacement for standby on this role.",
            ],
            [
                'id' => '12',
                'status' => 'Completed',
                'priority' => '12',
                'selected' => '1',
                'bg_color' => '#212121',
                'border_color' => '#212121',
                'message' => "You have marked this role complete.",
            ],
            [
                'id' => '13',
                'status' => 'Invoiced',
                'priority' => '13',
                'selected' => '1',
                'bg_color' => '#424242',
                'border_color' => '#424242',
                'message' => "You have invoiced for this role.",
            ],
            [
                'id' => '14',
                'status' => 'Paid',
                'priority' => '14',
                'selected' => '1',
                'bg_color' => '#757575',
                'border_color' => '#757575',
                'message' => "You have been paid for this role.",
            ],
            [
                'id' => '15',
                'status' => 'Rejected',
                'priority' => '15',
                'selected' => '0',
                'bg_color' => '#FF8A65',
                'border_color' => '#FF8A65',
                'message' => "You were not selected for this role.",
            ],
            [
                'id' => '16',
                'status' => 'Hidden Rejected',
                'priority' => '16',
                'selected' => '1',
                'bg_color' => '#FF8A65',
                'border_color' => '#FF8A65',
                'message' => "You have applied for this role.",
            ],
            [
                'id' => '17',
                'status' => 'Not Available',
                'priority' => '1',
                'selected' => '0',
                'bg_color' => '#BCAAA4',
                'border_color' => '#BCAAA4',
                'message' => "You are not available for this role",
            ],
        ]);
    }
}
