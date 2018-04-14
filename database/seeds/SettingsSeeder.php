<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'id' => '1',
                'setting' => "php_tz",
                'value' => "Australia/Perth",
            ],
            [
                'id' => '2',
                'setting' => "country_code",
                'value' => "AU",
            ],
            [
                'id' => '3',
                'setting' => "date_format",
                'value' => "j/m/Y",
            ],
            [
                'id' => '4',
                'setting' => "time_format",
                'value' => "g:i a",
            ],
            [
                'id' => '5',
                'setting' => "phone_code",
                'value' => null,
            ],
            [
                'id' => '6',
                'setting' => "currency_symbol",
                'value' => "$",
            ],
            [
                'id' => '7',
                'setting' => "distance_short",
                'value' => "km",
            ],
            [
                'id' => '8',
                'setting' => "distance_long",
                'value' => "kms",
            ],
            [
                'id' => '9',
                'setting' => "user_table_columns",
                'value' => "fname,lname,email,mob,last_login",
            ],
            [
                'id' => '10',
                'setting' => "check_in_start",
                'value' => '60',
            ],
            [
                'id' => '11',
                'setting' => "shift_table_columns",
                'value' => "title,location",
            ],
            [
                'id' => '12',
                'setting' => "check_in_out_enable",
                'value' => "0",
            ],
            [
                'id' => '13',
                'setting' => "check_in_photo_enable",
                'value' => "0",
            ],
            [
                'id' => '14',
                'setting' => "check_out_photo_enable",
                'value' => "0",
            ],
            [
                'id' => '15',
                'setting' => "check_in_no_show",
                'value' => "60",
            ],
            [
                'id' => '16',
                'setting' => "client_enable",
                'value' => "0",
            ],
            [
                'id' => '17',
                'setting' => "client_booking",
                'value' => "0",
            ],
            [
                'id' => '18',
                'setting' => "client_invoice_enable",
                'value' => "0",
            ],
            [
                'id' => '19',
                'setting' => "client_invoice_top",
                'value' => "",
            ],
            [
                'id' => '20',
                'setting' => "client_invoice_notes",
                'value' => "",
            ],
            [
                'id' => '21',
                'setting' => "client_invoice_bottom",
                'value' => "",
            ],
            [
                'id' => '22',
                'setting' => "client_invoice_stripe_key",
                'value' => "",
            ],
            [
                'id' => '23',
                'setting' => "client_invoice_stripe_secret",
                'value' => "",
            ],
            [
                'id' => '24',
                'setting' => "company_email_from",
                'value' => "StaffConnect",
            ],
            [
                'id' => '25',
                'setting' => "company_email_address",
                'value' => "no_reply@staffconnect.net",
            ],
            [
                'id' => '26',
                'setting' => "company_email_signature",
                'value' => "",
            ],
            [
                'id' => '27',
                'setting' => "expenses_enable",
                'value' => "0",
            ],
            [
                'id' => '28',
                'setting' => "expenses_staff",
                'value' => "0",
            ],
            [
                'id' => '29',
                'setting' => "expense_receipt_required",
                'value' => "1",
            ],
            [
                'id' => '30',
                'setting' => "locations_enable",
                'value' => "0",
            ],
            [
                'id' => '31',
                'setting' => "location_address",
                'value' => "",
            ],
            [
                'id' => '32',
                'setting' => "location_contact",
                'value' => "",
            ],
            [
                'id' => '33',
                'setting' => "location_geocode",
                'value' => "",
            ],
            [
                'id' => '34',
                'setting' => "outsource_enable",
                'value' => "0",
            ],
            [
                'id' => '35',
                'setting' => "paylvl_enable",
                'value' => "",
            ],
            [
                'id' => '36',
                'setting' => "profile_doc_message",
                'value' => "Upload any documents here.",
            ],
            [
                'id' => '37',
                'setting' => "profile_doc_required",
                'value' => "0",
            ],
            [
                'id' => '38',
                'setting' => "profile_photo_msg",
                'value' => "Upload your photos here.",
            ],
            [
                'id' => '39',
                'setting' => "profile_photo_required",
                'value' => "1",
            ],
            [
                'id' => '40',
                'setting' => "profile_video_enable",
                'value' => "0",
            ],
            [
                'id' => '41',
                'setting' => "profile_video_msg",
                'value' => "Upload your videos here.",
            ],
            [
                'id' => '43',
                'setting' => "quiz_enable",
                'value' => "0",
            ],
            [
                'id' => '44',
                'setting' => "registration_enable",
                'value' => "0",
            ],
            [
                'id' => '45',
                'setting' => "registration_msg_welcome",
                'value' => "Welcome to StaffConnect!",
            ],
            [
                'id' => '46',
                'setting' => "shift_enable",
                'value' => "0",
            ],
            [
                'id' => '47',
                'setting' => "shift_default_times_locked",
                'value' => "1",
            ],
            [
                'id' => '48',
                'setting' => "shift_timezone_display",
                'value' => "0",
            ],
            [
                'id' => '49',
                'setting' => "shift_standby_unavailable",
                'value' => "1",
            ],
            [
                'id' => '50',
                'setting' => "shift_application_reason",
                'value' => "0",
            ],
            [
                'id' => '51',
                'setting' => "shift_staff_confirm",
                'value' => "1",
            ],
            [
                'id' => '52',
                'setting' => "shift_msg_application",
                'value' => "",
            ],
            [
                'id' => '53',
                'setting' => "shift_msg_confirmation",
                'value' => "",
            ],
            [
                'id' => '54',
                'setting' => "shift_msg_completion",
                'value' => "",
            ],
            [
                'id' => '55',
                'setting' => "shift_calendar_overnight_single",
                'value' => "0",
            ],
            [
                'id' => '56',
                'setting' => "shift_replacement_request",
                'value' => "1",
            ],
            [
                'id' => '57',
                'setting' => "shift_msg_replacement_request",
                'value' => "",
            ],
            [
                'id' => '58',
                'setting' => "shift_replacement_request_deadline",
                'value' => "",
            ],
            [
                'id' => '59',
                'setting' => "shift_msg_replacement_request_na",
                'value' => "",
            ],
            [
                'id' => '60',
                'setting' => "shift_replacement_request_email",
                'value' => "",
            ],
            [
                'id' => '61',
                'setting' => "showcase_module",
                'value' => "0",
            ],
            [
                'id' => '62',
                'setting' => "staff_inactive_after",
                'value' => "24",
            ],
            [
                'id' => '63',
                'setting' => "staff_inactive_message",
                'value' => "Your account is inactive. Please contact us to re-activate.",
            ],
            [
                'id' => '64',
                'setting' => "staff_blacklisted_login",
                'value' => "0",
            ],
            [
                'id' => '65',
                'setting' => "staff_blacklisted_msg",
                'value' => "Your account is inactive. Please contact us to re-activate.",
            ],
            [
                'id' => '66',
                'setting' => "staff_see_others",
                'value' => "always",
            ],
            [
                'id' => '67',
                'setting' => "staff_see_others_photo",
                'value' => "1",
            ],
            [
                'id' => '68',
                'setting' => "staff_see_others_name",
                'value' => "0",
            ],
            [
                'id' => '69',
                'setting' => "staff_see_others_mob",
                'value' => "0",
            ],
            [
                'id' => '70',
                'setting' => "staff_see_shift_address",
                'value' => "always",
            ],
            [
                'id' => '71',
                'setting' => "staff_see_shift_contact",
                'value' => "after_selection",
            ],
            [
                'id' => '72',
                'setting' => "staff_see_shift_manager",
                'value' => "always",
            ],
            [
                'id' => '73',
                'setting' => "staff_see_shift_notes",
                'value' => "always",
            ],
            [
                'id' => '74',
                'setting' => "staff_see_role_notes",
                'value' => "always",
            ],
            [
                'id' => '75',
                'setting' => "staff_see_role_required",
                'value' => "always",
            ],
            [
                'id' => '76',
                'setting' => "staff_change_work_areas",
                'value' => "1",
            ],
            [
                'id' => '77',
                'setting' => "staff_upload_shift_files",
                'value' => "1",
            ],
            [
                'id' => '78',
                'setting' => "staff_invoice_enable",
                'value' => "0",
            ],
            [
                'id' => '79',
                'setting' => "staff_invoice_default",
                'value' => "all",
            ],
            [
                'id' => '80',
                'setting' => "staff_invoice_combine",
                'value' => "0",
            ],
            [
                'id' => '81',
                'setting' => "staff_invoice_weekly",
                'value' => "0",
            ],
            [
                'id' => '82',
                'setting' => "staff_invoice_deadline",
                'value' => "12",
            ],
            [
                'id' => '83',
                'setting' => "staff_invoice_msg_creation",
                'value' => "",
            ],
            [
                'id' => '84',
                'setting' => "staff_invoice_top",
                'value' => "",
            ],
            [
                'id' => '85',
                'setting' => "staff_invoice_financial",
                'value' => "",
            ],
            [
                'id' => '86',
                'setting' => "survey_enable",
                'value' => "0",
            ],
            [
                'id' => '87',
                'setting' => "company_name",
                'value' => "",
            ],
            [
                'id' => '88',
                'setting' => "system_name",
                'value' => "StaffConnect4",
            ],
            [
                'id' => '89',
                'setting' => "company_website",
                'value' => "",
            ],
            [
                'id' => '90',
                'setting' => "company_address",
                'value' => "",
            ],
            [
                'id' => '91',
                'setting' => "company_city",
                'value' => "",
            ],
            [
                'id' => '92',
                'setting' => "company_postcode",
                'value' => "",
            ],
            [
                'id' => '93',
                'setting' => "company_country",
                'value' => "",
            ],
            [
                'id' => '94',
                'setting' => "tracking_enable",
                'value' => "0",
            ],
            [
                'id' => '95',
                'setting' => "work_areas_enable",
                'value' => "0",
            ],
            [
                'id' => '96',
                'setting' => "work_market_enable",
                'value' => "0",
            ],
            [
                'id' => '97',
                'setting' => "xero_enable",
                'value' => "0",
            ],
            [
                'id' => '98',
                'setting' => "xero_client_invoice",
                'value' => "0",
            ],
            [
                'id' => '99',
                'setting' => "xero_staff_invoice",
                'value' => "0",
            ],
            [
                'id' => '100',
                'setting' => "xero_payroll",
                'value' => "0",
            ],
        ]);
    }
}
