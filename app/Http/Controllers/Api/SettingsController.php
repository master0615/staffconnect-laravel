<?php
namespace App\Http\Controllers\Api;

use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected $zonelist = array('Kwajalein' => '(GMT-12:00) International Date Line West',
        'Pacific/Midway' => '(GMT-11:00) Midway Island',
        'Pacific/Samoa' => '(GMT-11:00) Samoa',
        'Pacific/Honolulu' => '(GMT-10:00) Hawaii',
        'America/Anchorage' => '(GMT-09:00) Alaska',
        'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada)',
        'America/Tijuana' => '(GMT-08:00) Tijuana, Baja California',
        'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)',
        'America/Chihuahua' => '(GMT-07:00) Chihuahua',
        'America/Mazatlan' => '(GMT-07:00) Mazatlan',
        'America/Phoenix' => '(GMT-07:00) Arizona',
        'America/Regina' => '(GMT-06:00) Saskatchewan',
        'America/Tegucigalpa' => '(GMT-06:00) Central America',
        'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)',
        'America/Mexico_City' => '(GMT-06:00) Mexico City',
        'America/Monterrey' => '(GMT-06:00) Monterrey',
        'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)',
        'America/Bogota' => '(GMT-05:00) Bogota',
        'America/Lima' => '(GMT-05:00) Lima',
        'America/Rio_Branco' => '(GMT-05:00) Rio Branco',
        'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
        'America/Caracas' => '(GMT-04:30) Caracas',
        'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)',
        'America/Manaus' => '(GMT-04:00) Manaus',
        'America/Santiago' => '(GMT-04:00) Santiago',
        'America/La_Paz' => '(GMT-04:00) La Paz',
        'America/St_Johns' => '(GMT-03:30) Newfoundland',
        'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
        'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
        'America/Godthab' => '(GMT-03:00) Greenland',
        'America/Montevideo' => '(GMT-03:00) Montevideo',
        'Atlantic/South_Georgia' => '(GMT-02:00) Mid-Atlantic',
        'Atlantic/Azores' => '(GMT-01:00) Azores',
        'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
        'Europe/Dublin' => '(GMT) Dublin',
        'Europe/Lisbon' => '(GMT) Lisbon',
        'Europe/London' => '(GMT) London',
        'Africa/Monrovia' => '(GMT) Monrovia',
        'Atlantic/Reykjavik' => '(GMT) Reykjavik',
        'Africa/Casablanca' => '(GMT) Casablanca',
        'Europe/Belgrade' => '(GMT+01:00) Belgrade',
        'Europe/Bratislava' => '(GMT+01:00) Bratislava',
        'Europe/Budapest' => '(GMT+01:00) Budapest',
        'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
        'Europe/Prague' => '(GMT+01:00) Prague',
        'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
        'Europe/Skopje' => '(GMT+01:00) Skopje',
        'Europe/Warsaw' => '(GMT+01:00) Warsaw',
        'Europe/Zagreb' => '(GMT+01:00) Zagreb',
        'Europe/Brussels' => '(GMT+01:00) Brussels',
        'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
        'Europe/Madrid' => '(GMT+01:00) Madrid',
        'Europe/Paris' => '(GMT+01:00) Paris',
        'Africa/Algiers' => '(GMT+01:00) West Central Africa',
        'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
        'Europe/Berlin' => '(GMT+01:00) Berlin',
        'Europe/Rome' => '(GMT+01:00) Rome',
        'Europe/Stockholm' => '(GMT+01:00) Stockholm',
        'Europe/Vienna' => '(GMT+01:00) Vienna',
        'Europe/Minsk' => '(GMT+02:00) Minsk',
        'Africa/Cairo' => '(GMT+02:00) Cairo',
        'Europe/Helsinki' => '(GMT+02:00) Helsinki',
        'Europe/Riga' => '(GMT+02:00) Riga',
        'Europe/Sofia' => '(GMT+02:00) Sofia',
        'Europe/Tallinn' => '(GMT+02:00) Tallinn',
        'Europe/Vilnius' => '(GMT+02:00) Vilnius',
        'Europe/Athens' => '(GMT+02:00) Athens',
        'Europe/Bucharest' => '(GMT+02:00) Bucharest',
        'Europe/Istanbul' => '(GMT+02:00) Istanbul',
        'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
        'Asia/Amman' => '(GMT+02:00) Amman',
        'Asia/Beirut' => '(GMT+02:00) Beirut',
        'Africa/Windhoek' => '(GMT+02:00) Windhoek',
        'Africa/Harare' => '(GMT+02:00) Harare',
        'Asia/Kuwait' => '(GMT+03:00) Kuwait',
        'Asia/Riyadh' => '(GMT+03:00) Riyadh',
        'Asia/Baghdad' => '(GMT+03:00) Baghdad',
        'Africa/Nairobi' => '(GMT+03:00) Nairobi',
        'Asia/Tbilisi' => '(GMT+03:00) Tbilisi',
        'Europe/Moscow' => '(GMT+03:00) Moscow',
        'Europe/Volgograd' => '(GMT+03:00) Volgograd',
        'Asia/Tehran' => '(GMT+03:30) Tehran',
        'Asia/Muscat' => '(GMT+04:00) Muscat',
        'Asia/Baku' => '(GMT+04:00) Baku',
        'Asia/Yerevan' => '(GMT+04:00) Yerevan',
        'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
        'Asia/Karachi' => '(GMT+05:00) Karachi',
        'Asia/Tashkent' => '(GMT+05:00) Tashkent',
        'Asia/Kolkata' => '(GMT+05:30) Calcutta',
        'Asia/Colombo' => '(GMT+05:30) Sri Jayawardenepura',
        'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
        'Asia/Dhaka' => '(GMT+06:00) Dhaka',
        'Asia/Almaty' => '(GMT+06:00) Almaty',
        'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
        'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
        'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
        'Asia/Bangkok' => '(GMT+07:00) Bangkok',
        'Asia/Jakarta' => '(GMT+07:00) Jakarta',
        'Asia/Brunei' => '(GMT+08:00) Beijing',
        'Asia/Chongqing' => '(GMT+08:00) Chongqing',
        'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
        'Asia/Urumqi' => '(GMT+08:00) Urumqi',
        'Asia/Irkutsk' => '(GMT+08:00) Irkutsk',
        'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
        'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
        'Asia/Singapore' => '(GMT+08:00) Singapore',
        'Asia/Taipei' => '(GMT+08:00) Taipei',
        'Australia/Perth' => '(GMT+08:00) Perth',
        'Asia/Seoul' => '(GMT+09:00) Seoul',
        'Asia/Tokyo' => '(GMT+09:00) Tokyo',
        'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
        'Australia/Darwin' => '(GMT+09:30) Darwin',
        'Australia/Adelaide' => '(GMT+09:30) Adelaide',
        'Australia/Canberra' => '(GMT+10:00) Canberra',
        'Australia/Melbourne' => '(GMT+10:00) Melbourne',
        'Australia/Sydney' => '(GMT+10:00) Sydney',
        'Australia/Brisbane' => '(GMT+10:00) Brisbane',
        'Australia/Hobart' => '(GMT+10:00) Hobart',
        'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
        'Pacific/Guam' => '(GMT+10:00) Guam',
        'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
        'Asia/Magadan' => '(GMT+11:00) Magadan',
        'Pacific/Fiji' => '(GMT+12:00) Fiji',
        'Asia/Kamchatka' => '(GMT+12:00) Kamchatka',
        'Pacific/Auckland' => '(GMT+12:00) Auckland',
        'Pacific/Tongatapu' => '(GMT+13:00) Nukualofa');

    protected $ccodes = [
        'AD',
        'AE',
        'AF',
        'AG',
        'AI',
        'AL',
        'AM',
        'AN',
        'AO',
        'AQ',
        'AR',
        'AS',
        'AT',
        'AU',
        'AW',
        'AX',
        'AZ',
        'BA',
        'BB',
        'BD',
        'BE',
        'BF',
        'BG',
        'BH',
        'BI',
        'BJ',
        'BL',
        'BM',
        'BN',
        'BO',
        'BR',
        'BS',
        'BT',
        'BV',
        'BW',
        'BY',
        'BZ',
        'CA',
        'CC',
        'CD',
        'CF',
        'CG',
        'CH',
        'CI',
        'CK',
        'CL',
        'CM',
        'CN',
        'CO',
        'CR',
        'CU',
        'CV',
        'CX',
        'CY',
        'CZ',
        'DE',
        'DJ',
        'DK',
        'DM',
        'DO',
        'DZ',
        'EC',
        'EE',
        'EG',
        'EH',
        'ER',
        'ES',
        'ET',
        'FI',
        'FJ',
        'FK',
        'FM',
        'FO',
        'FR',
        'GA',
        'GB',
        'GD',
        'GE',
        'GF',
        'GG',
        'GH',
        'GI',
        'GL',
        'GM',
        'GN',
        'GP',
        'GQ',
        'GR',
        'GS',
        'GT',
        'GU',
        'GW',
        'GY',
        'HK',
        'HM',
        'HN',
        'HR',
        'HT',
        'HU',
        'ID',
        'IE',
        'IL',
        'IM',
        'IN',
        'IO',
        'IQ',
        'IR',
        'IS',
        'IT',
        'JE',
        'JM',
        'JO',
        'JP',
        'KE',
        'KG',
        'KH',
        'KI',
        'KM',
        'KN',
        'KP',
        'KR',
        'KW',
        'KY',
        'KZ',
        'LA',
        'LB',
        'LC',
        'LI',
        'LK',
        'LR',
        'LS',
        'LT',
        'LU',
        'LV',
        'LY',
        'MA',
        'MC',
        'MD',
        'ME',
        'MF',
        'MG',
        'MH',
        'MK',
        'ML',
        'MM',
        'MN',
        'MO',
        'MP',
        'MQ',
        'MR',
        'MS',
        'MT',
        'MU',
        'MV',
        'MW',
        'MX',
        'MY',
        'MZ',
        'NA',
        'NC',
        'NE',
        'NF',
        'NG',
        'NI',
        'NL',
        'NO',
        'NP',
        'NR',
        'NU',
        'NZ',
        'OM',
        'PA',
        'PE',
        'PF',
        'PG',
        'PH',
        'PK',
        'PL',
        'PM',
        'PN',
        'PR',
        'PS',
        'PT',
        'PW',
        'PY',
        'QA',
        'RE',
        'RO',
        'RS',
        'RU',
        'RW',
        'SA',
        'SB',
        'SC',
        'SD',
        'SE',
        'SG',
        'SH',
        'SI',
        'SJ',
        'SK',
        'SL',
        'SM',
        'SN',
        'SO',
        'SR',
        'SS',
        'ST',
        'SV',
        'SY',
        'SZ',
        'TC',
        'TD',
        'TF',
        'TG',
        'TH',
        'TJ',
        'TK',
        'TL',
        'TM',
        'TN',
        'TO',
        'TR',
        'TT',
        'TV',
        'TW',
        'TZ',
        'UA',
        'UG',
        'UM',
        'US',
        'UY',
        'UZ',
        'VA',
        'VC',
        'VE',
        'VG',
        'VI',
        'VN',
        'VU',
        'WF',
        'WS',
        'YE',
        'YT',
        'ZA',
        'ZM',
        'ZW',
    ];

    /**
     * GET /setting/{setting?}
     */
    public function get($id = false)
    {
        if ($id) {
            $s = Setting::findOrFail($id);
        } else {
            $s = Setting::all();
        }
        return response()->api($s);
    }

    /**
     * GET /setting/{setting?}/options
     */
    public function options($id = false)
    {
        if ($id) {
            $s = Setting::findOrFail($id);
            $ss = [$s];
        } else {
            $ss = Setting::all();
        }

        $options = [];
        foreach ($ss as $s) {
            switch ($s->id) {

                case 1: //timezone
                    $options[$s->id] = $this->zonelist;
                    break;

                case 2: //country codes
                    $options[$s->id] = $this->ccodes;
                    break;

                case 3: //date format
                    $options[$s->id] = ["j/m/Y", "m/j/Y"];
                    break;

                case 4: //time format
                    $options[$s->id] = ["g:i a", "H:i"];
                    break;

                case 7: //distance short
                    $options[$s->id] = ["km", "mi"];
                    break;

                case 8: //distance long
                    $options[$s->id] = ["kms", "miles"];
                    break;

                case 9: //user table columns
                    $options[$s->id] = [
                        'fname' => 'First Name',
                        'lname' => 'Last Name',
                        'email' => 'Email',
                        'mob' => 'Mobile',
                        'dob' => 'Date of Birth',
                        'sex' => 'Sex',
                        'age' => 'Age',
                        'address' => \App\ProfileElement::find(8)->ename,
                        'unit' => \App\ProfileElement::find(9)->ename,
                        'city' => \App\ProfileElement::find(10)->ename,
                        'state' => \App\ProfileElement::find(11)->ename,
                        'postcode' => \App\ProfileElement::find(12)->ename,
                        'last_login' => 'Last Login',
                    ];

                    $cats = \App\ProfileCategory::select('id', 'cname')
                        ->with(['profileElements' => function ($query) {
                            $query->where('deletable', 1);
                        }])
                        ->orderBy('display_order')->get();

                    foreach ($cats as $cat) {
                        foreach ($cat->profileElements as $pe) {
                            $options[$s->id]['pd|' . $pe->id] = $cat->cname . ' - ' . $pe->ename;
                        }
                    }
                    break;

                case 10: //staff_checkin_start minutes
                    $options[$s->id] = [10, 15, 30, 45, 60, 90, 120];
                    break;

                case 11: //shift table columns
                    $options[$s->id] = [
                        'title' => 'Title',
                        'location' => 'Location',
                    ];

                default:
                    $options[$s->id] = null;
                    break;
            }
        }

        if ($id && count($options)) {
            $options = $options[$id];
        }
        return response()->api($options);
    }

    /**
     * PUT /setting/{setting}
     */
    public function set(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|nullable',
        ]);

        $s = Setting::findOrFail($id);

        $value = $request->value;
        if ($value == 'null' || $value == '') {
            $value = null;
        }

        //validate important shit
        switch ($s->id) { // protect timezone or ApplySettings middleware will fail
            case 1:
                if (!in_array($value, timezone_identifiers_list())) {
                    throw new \App\Exceptions\SystemProtected;
                }
                break;
        }

        $s->value = $value;
        $s->save();

        return response()->api([
            'message' => "Setting saved.",
            'data' => $s,
        ]);
    }

    /**
     * GET /helpers/timezones
     */
    public function getTimezones()
    {
        return response()->api($this->zonelist);
    }
}
