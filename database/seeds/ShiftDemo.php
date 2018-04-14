<?php

use Illuminate\Database\Seeder;

class ShiftDemo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('shifts')->insert([
            [
                'id' => '1',
                'shift_status_id' => '5',
                'live' => '1',
                'title' => 'Coke Zero Sampling',
                'generic_title' => 'Softdrink Sampling',
                'location' => 'Kmart Carousel S/C',
                'generic_location' => 'Carousel S/C',
                'shift_start' => date('Y-m-d 09:00:00', strtotime(date('Y-m-d') . " +3 days")),
                'shift_end' => date('Y-m-d 14:30:00', strtotime(date('Y-m-d') . " +3 days")),
                'timezone' => 'Australia/Perth',
                'contact' => 'Joe Smith 034345345',
                'address' => '1382 Albany Hwy, Cannington WA 6107, Australia',
                'lat' => '-32.0187071',
                'lon' => '115.9355114',
                'notes' => '<img alt="" src="https://assistmarketing.staffconnect.net/editor_uploads/1629.png" style="width: 203px; height: 105px;" /><br/>blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah',
            ],
            [
                'id' => '2',
                'shift_status_id' => '5',
                'live' => '1',
                'title' => 'Coke Zero Sampling',
                'generic_title' => 'Softdrink Sampling',
                'location' => 'Target Carousel S/C',
                'generic_location' => 'Carousel S/C',
                'shift_start' => date('Y-m-d 09:00:00', strtotime(date('Y-m-d') . " +4 days")),
                'shift_end' => date('Y-m-d 14:30:00', strtotime(date('Y-m-d') . " +4 days")),
                'timezone' => 'Australia/Perth',
                'contact' => 'Joe Smith 034345345',
                'address' => '1382 Albany Hwy, Cannington WA 6107, Australia',
                'lat' => '-32.0187071',
                'lon' => '115.9355114',
                'notes' => '<img alt="" src="https://assistmarketing.staffconnect.net/editor_uploads/1629.png" style="width: 203px; height: 105px;" /><br/>blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah',
            ],
            [
                'id' => '3',
                'shift_status_id' => '5',
                'live' => '0',
                'title' => 'Fanta Sampling',
                'generic_title' => 'Softdrink Sampling',
                'location' => 'Target Carousel S/C',
                'generic_location' => 'Carousel S/C',
                'shift_start' => date('Y-m-d 09:00:00', strtotime(date('Y-m-d') . " +7 days")),
                'shift_end' => date('Y-m-d 14:30:00', strtotime(date('Y-m-d') . " +7 days")),
                'timezone' => 'Australia/Perth',
                'contact' => 'Joe Smith 034345345',
                'address' => '1382 Albany Hwy, Cannington WA 6107, Australia',
                'lat' => '-32.0187071',
                'lon' => '115.9355114',
                'notes' => '<img alt="" src="https://assistmarketing.staffconnect.net/editor_uploads/1629.png" style="width: 203px; height: 105px;" /><br/>blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah',
            ],
        ]);

        DB::table('shift_work_area')->insert([
            [
                'shift_id' => '1',
                'work_area_id' => '1',
            ],
            [
                'shift_id' => '1',
                'work_area_id' => '2',
            ],
            [
                'shift_id' => '2',
                'work_area_id' => '1',
            ],
        ]);

        DB::table('shift_tracking_option')->insert([
            [
                'shift_id' => '1',
                'tracking_option_id' => '1',
            ],
            [
                'shift_id' => '1',
                'tracking_option_id' => '5',
            ],
            [
                'shift_id' => '1',
                'tracking_option_id' => '6',
            ],
        ]);

        DB::table('shift_manager')->insert([
            [
                'shift_id' => '1',
                'user_id' => '1',
            ],
            [
                'shift_id' => '1',
                'user_id' => '2',
            ],
            [
                'shift_id' => '2',
                'user_id' => '1',
            ],
        ]);

        DB::table('shift_roles')->insert([
            [
                'id' => '1',
                'shift_id' => '1',
                'rname' => 'Mascot',
                'num_required' => '1',
                'sex' => 'male',
                'role_start' => null,
                'role_end' => null,
                'notes' => "You will be dressing up in a Duracell Bunny costume, running around hugging kids and giving out balloons.<br/>blah blah blah blah blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah",
                'completion_notes' => "Wash and return uniform ASAP.<br/>blah blah blah blah blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah",
                'bill_rate' => '60',
                'bill_rate_type' => 'phr',
                'pay_rate' => '25',
                'pay_rate_type' => 'phr',
                'unpaid_break' => '30',
                'paid_break' => '15',
                'expense_limit' => null,
            ],
            [
                'id' => '2',
                'shift_id' => '1',
                'rname' => 'Minder',
                'num_required' => '2',
                'sex' => 'female',
                'role_start' => date('Y-m-d 09:30:00', strtotime(date('Y-m-d') . " +3 days")),
                'role_end' => date('Y-m-d 14:45:00', strtotime(date('Y-m-d') . " +3 days")),
                'notes' => "You will be loooking after the mascot.<br/>blah blah blah blah blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah",
                'completion_notes' => "Wash and return uniform ASAP.<br/>blah blah blah blah blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah",
                'bill_rate' => '50',
                'bill_rate_type' => 'phr',
                'pay_rate' => '20',
                'pay_rate_type' => 'phr',
                'unpaid_break' => '30',
                'paid_break' => '15',
                'expense_limit' => '50',
            ],
            [
                'id' => '3',
                'shift_id' => '2',
                'rname' => 'Mascot',
                'num_required' => '1',
                'sex' => 'male',
                'role_start' => null,
                'role_end' => null,
                'notes' => "You will be dressing up in a Duracell Bunny costume, running around hugging kids and giving out balloons.<br/>blah blah blah blah blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah",
                'completion_notes' => "Wash and return uniform ASAP.<br/>blah blah blah blah blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah",
                'bill_rate' => '60',
                'bill_rate_type' => 'phr',
                'pay_rate' => '25',
                'pay_rate_type' => 'phr',
                'unpaid_break' => '30',
                'paid_break' => '15',
                'expense_limit' => null,
            ],
            [
                'id' => '4',
                'shift_id' => '2',
                'rname' => 'Minder',
                'num_required' => '2',
                'sex' => 'female',
                'role_start' => date('Y-m-d 09:30:00', strtotime(date('Y-m-d') . " +4 days")),
                'role_end' => date('Y-m-d 14:45:00', strtotime(date('Y-m-d') . " +4 days")),
                'notes' => "You will be loooking after the mascot.<br/>blah blah blah blah blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah",
                'completion_notes' => "Wash and return uniform ASAP.<br/>blah blah blah blah blah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blahblah blah blah blah",
                'bill_rate' => '50',
                'bill_rate_type' => 'phr',
                'pay_rate' => '20',
                'pay_rate_type' => 'phr',
                'unpaid_break' => '30',
                'paid_break' => '15',
                'expense_limit' => '50',
            ],
        ]);

        DB::table('role_pay_items')->insert([
            [
                'shift_role_id' => '1',
                'unit_rate' => '11',
                'unit_rate_type' => 'pu',
                'units' => '1.5',
                'item_name' => 'Travel',
                'item_type' => 'travel',
            ],
            [
                'shift_role_id' => '1',
                'unit_rate' => '50',
                'unit_rate_type' => 'flat',
                'units' => '1',
                'item_name' => 'Last minute',
                'item_type' => 'bonus',
            ],
        ]);

        DB::table('role_staff')->insert([
            [
                'id' => '1',
                'shift_role_id' => '2',
                'user_id' => '8',
                'staff_status_id' => '4',
                'staff_start' => null,
                'staff_end' => null,
                'unpaid_break' => null,
                'paid_break' => null,
                'expense_limit' => null,
                'bill_rate' => null,
                'bill_rate_type' => null,
                'pay_rate' => null,
                'pay_rate_type' => null,
                'team_leader' => 0,
            ],
            [
                'id' => '2',
                'shift_role_id' => '2',
                'user_id' => '9',
                'staff_status_id' => '5',
                'staff_start' => date('Y-m-d 09:15:00', strtotime(date('Y-m-d') . " +3 days")),
                'staff_end' => date('Y-m-d 14:55:00', strtotime(date('Y-m-d') . " +3 days")),
                'unpaid_break' => '40',
                'paid_break' => '60',
                'expense_limit' => '100',
                'bill_rate' => '70',
                'bill_rate_type' => 'phr',
                'pay_rate' => '35',
                'pay_rate_type' => 'phr',
                'team_leader' => 1,
            ],
            [
                'id' => '3',
                'shift_role_id' => '4',
                'user_id' => '8',
                'staff_status_id' => '2',
                'staff_start' => null,
                'staff_end' => null,
                'unpaid_break' => null,
                'paid_break' => null,
                'expense_limit' => null,
                'bill_rate' => null,
                'bill_rate_type' => null,
                'pay_rate' => null,
                'pay_rate_type' => null,
                'team_leader' => 0,
            ],
        ]);
    }
}
