<?php
namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

define('TENANT_DIR', \Illuminate\Support\Facades\Storage::disk('tenant')->getDriver()
        ->getAdapter()
        ->getPathPrefix());

define('PHOTO_SIZE', '1280'); // fullsize
define('PHOTO_SIZE_THUMB', '200'); // medium for emails, thumbnails
define('PHOTO_SIZE_TTHUMB', '50'); // tiny for user table

//settings ids
define('SETTING_DISTANCE_SHORT', 7);
define('SETTING_STAFF_CHECKIN_START', 10);

//shift status ids from ShiftStatusSeeder used in ShiftRole, Shift
define('SHIFT_STATUS_BOOKING', '1');
define('SHIFT_STATUS_QUOTE', '2');
define('SHIFT_STATUS_CANCELLED', '3');
define('SHIFT_STATUS_REPLACEMENT_REQUESTED', '4');
define('SHIFT_STATUS_UNFILLED', '5');
define('SHIFT_STATUS_ENOUGH_APPLICANTS', '6');
define('SHIFT_STATUS_FILLED', '7');
define('SHIFT_STATUS_CONFIRMED', '8');
define('SHIFT_STATUS_CHECKED_IN', '9');
define('SHIFT_STATUS_NO_SHOW', '10');
define('SHIFT_STATUS_CHECKED_OUT', '11');
define('SHIFT_STATUS_REPORTS_SUBMITTED', '12');
define('SHIFT_STATUS_PAST', '13');
define('SHIFT_STATUS_STAFF_COMPLETED', '14');
define('SHIFT_STATUS_ADMIN_COMPLETED', '15');

//staff status ids from StaffStatusSeeder
define('STAFF_STATUS_APPLIED', '1');
define('STAFF_STATUS_STANDBY', '2');
define('STAFF_STATUS_SELECTED', '3');
define('STAFF_STATUS_CONFIRMED', '4');
define('STAFF_STATUS_CHECK_IN_ATTEMPTED', '5');
define('STAFF_STATUS_CHECKED_IN', '6');
define('STAFF_STATUS_NO_SHOW', '7');
define('STAFF_STATUS_CHECK_OUT_ATTEMPTED', '8');
define('STAFF_STATUS_CHECKED_OUT', '9');
define('STAFF_STATUS_REPLACEMENT_REQUESTED', '10');
define('STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED', '11');
define('STAFF_STATUS_COMPLETED', '12');
define('STAFF_STATUS_INVOICED', '13');
define('STAFF_STATUS_PAID', '14');
define('STAFF_STATUS_REJECTED', '15');
define('STAFF_STATUS_HIDDEN_REJECTED', '16');
define('STAFF_STATUS_NOT_AVAILABLE', '17');

define('STAFF_STATUSES_SELECTED', [
    STAFF_STATUS_SELECTED,
    STAFF_STATUS_CONFIRMED,
    STAFF_STATUS_CHECK_IN_ATTEMPTED,
    STAFF_STATUS_CHECKED_IN,
    STAFF_STATUS_NO_SHOW,
    STAFF_STATUS_CHECK_OUT_ATTEMPTED,
    STAFF_STATUS_CHECKED_OUT,
    STAFF_STATUS_REPLACEMENT_REQUESTED,
    STAFF_STATUS_COMPLETED,
    STAFF_STATUS_INVOICED,
    STAFF_STATUS_PAID,
]);

define('STAFF_STATUSES_STANDBY', [
    STAFF_STATUS_STANDBY,
    STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED,
]);

define('STAFF_STATUSES_NA', [
    STAFF_STATUS_REJECTED,
    STAFF_STATUS_HIDDEN_REJECTED,
    STAFF_STATUS_NOT_AVAILABLE,
]);

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
