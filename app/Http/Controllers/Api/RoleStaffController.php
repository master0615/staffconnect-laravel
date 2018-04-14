<?php
namespace App\Http\Controllers\Api;

use App\Helpers\Utilities;
use App\Notifications\ShiftSelect;
use App\RoleStaff;
use App\Setting;
use App\ShiftRole;
use App\StaffCheck;
use App\StaffPayItem;
use App\StaffStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Glide\GlideImage;

class RoleStaffController extends Controller
{
    /**
     * POST path="/role/{id}/apply
     * staff applying to role
     */
    public function apply(Request $request, $id)
    {
        $sr = ShiftRole::findOrFail($id);
        $request->validate([
            'reason' => 'sometimes|nullable|string|max:200',
        ]);

        if (!$sr->shift->live) {
            return response()->api([
                'message' => "The shift is no longer available.",
            ], 400);
        }

        if ($sr->past()) {
            return response()->api([
                'message' => "The role is no longer available.",
            ], 400);
        }

        if ($sr->applicationDeadlinePast()) {
            return response()->api([
                'message' => "The application deadline has past.",
            ], 400);
        }

        $canApply = $sr->userEligible(Auth::user()->loggedInAsId());
        if ($canApply != '') {
            return response()->api([
                'message' => "You are not eligible for this role.",
            ], 400);
        }

        if (Setting::findOrFail(50)->value) {
            if (strlen($request->reason) < 5) {
                return response()->api([
                    'message' => "Please enter the reason for your application.",
                ], 400);
            }
        }

        $rs = RoleStaff::where([['shift_role_id', $id], ['user_id', Auth::user()->loggedInAsId()]])->first();
        if ($rs) {
            if ($rs->staff_status_id != STAFF_STATUS_NOT_AVAILABLE) {
                return response()->api([
                    'message' => "You have already applied or been selected for this role.",
                ], 400);
            }

        } else {
            $rs = new RoleStaff;
            $rs->user_id = Auth::user()->loggedInAsId();
            $rs->shift_role_id = $id;
        }

        $rs->staff_status_id = STAFF_STATUS_APPLIED;
        $rs->save();

        if (strlen($request->reason) > 1) {
            $ar = new \App\ApplicationReason(['reason' => $request->reason]);
            $rs->applicationReason()->save($ar);
        }

        return response()->api([
            'id' => $rs->id,
            'message' => "You have applied for this role.",
            'role_message' => "You have applied for this role.",
            'actions' => ['cancel_application'],
        ]);
    }

    /**
     * POST path="/role/applyCancel/{id}
     * staff cancel application to role
     */
    public function applyCancel($id)
    {
        $rs = RoleStaff::findOrFail($id);

        if ($rs->user_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\NotAllowedException();
        }

        switch ($rs->staff_status_id) {
            case STAFF_STATUS_APPLIED:
            case STAFF_STATUS_HIDDEN_REJECTED:
                $rs->delete();
                break;

            default:
                throw new \App\Exceptions\NotAllowedException();
        }

        $actions = [];
        if ($rs->shiftRole->past()) {
            $roleMessage = "The role is no longer available.";

        } elseif ($rs->shiftRole->applicationDeadlinePast()) {
            $roleMessage = "The application deadline has past.";

        } elseif ($rs->shiftRole->userEligible(Auth::user()->loggedInAsId()) == '') {
            $roleMessage = "You are eligible for this role.";
            $actions = ['apply', 'not_available'];
        } else {
            $roleMessage = "You are not eligible for this role.";
        }

        $rs->applicationReason()->delete();

        return response()->api([
            'message' => "Application cancelled.",
            'role_message' => $roleMessage,
            'actions' => $actions,
        ]);
    }

    /**
     * POST path="/role/{id}/assign
     * admin assigns staff to role TODO add reject reject2 nopay other?
     */
    public function assign(Request $request, $id)
    {
        $r = ShiftRole::findOrFail($id);

        $request->validate([
            'user_id' => 'required|numeric|exists:tenant.users,id',
            'staff_status_id' => 'required|string', //exists:tenant.staff_statuses,id
            'outsource_company_id' => 'sometimes|numeric|exists:tenant.outsource_companies,id',
        ]);

        $staffStatusId = constant($request->staff_status_id);
        if (!$staffStatusId) {
            throw new \App\Exceptions\NotAllowedException();
        }
        $ss = StaffStatus::findOrFail($staffStatusId);

        $rs = RoleStaff::where([
            [
                'user_id',
                $request->user_id,
            ],
            [
                'shift_role_id',
                $id,
            ],
        ])->first();

        if ($rs) {
            if ($rs->staff_status_id == $staffStatusId) {
                return response()->api([
                    'message' => "The user is already assigned to the role.",
                    'data' => $rs->id,
                ], 400);
            }
            // already exists but some other status so call update
            $request = new \Illuminate\Http\Request();
            $request->replace(['staff_status_id' => $staffStatusId]);
            return $this->update($request, $rs->id);
        }

        $rs = new RoleStaff();
        $rs->user_id = $request->user_id;
        $rs->shift_role_id = $id;
        $rs->staff_status_id = $staffStatusId;

        // outsourced?
        if ($request->has('outsource_company_id')) {
            if ($rs->user->outsourceCompanies()
                ->where('outsource_company_id', $request->outsource_company_id)
                ->first()) {
                $rs->outsource_company_id = $request->outsource_company_id;
            } else {
                return response()->api([
                    "message" => "The user does not work for the outsource company.",
                ], 400);
            }
        } else {
            if (!$rs->user->works_here) {
                return response()->api([
                    "message" => "The user does not work for this company. Please assign the outsource company or change the user to allow working for this company.",
                ], 400);
            }
        }

        $rs->save();

        switch ($rs->staff_status_id) {
            case STAFF_STATUS_CONFIRMED:
                $rs->user->notify(new ShiftSelect($rs));
                break;
            case STAFF_STATUS_SELECTED:
                $rs->user->notify(new ShiftSelect($rs));
                break;
            case STAFF_STATUS_STANDBY:
                $rs->user->notify(new ShiftSelect($rs));
                break;
        }

        return response()->api([
            'data' => $rs->id,
            'message' => "User assigned.", // TODO more detail what status
        ]);
    }

    /**
     * POST path="/role/checkIn/{id}
     * staff checkin
     */
    public function checkIn(Request $request, $id)
    {
        $rs = RoleStaff::with('ShiftRole.Shift')->findOrFail($id);

        if ($rs->user_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\NotAllowedException();
        }

        if ($rs->checkInOpen()) {

            if (in_array($rs->staff_status_id, [
                STAFF_STATUS_SELECTED,
                STAFF_STATUS_CONFIRMED,
                STAFF_STATUS_CHECK_IN_ATTEMPTED,
            ])) {

                $checkInPhoto = Setting::findOrFail(13)->value;
                if ($checkInPhoto) {
                    $checkInPhoto = 'required';
                } else {
                    $checkInPhoto = 'sometimes';
                }

                $request->validate([
                    'lat' => 'required|numeric',
                    'lon' => 'required|numeric',
                    'photo' => "$checkInPhoto|mimes:jpg,jpeg,png|dimensions:min_width=500,min_height=500|max:10240
                    ",
                ]);

                $distance = null;
                $type = 'in';
                $staffStatusId = STAFF_STATUS_CHECKED_IN;
                $shiftLat = $rs->ShiftRole->Shift->lat;
                $shiftLon = $rs->ShiftRole->Shift->lon;

                if ($shiftLat) {
                    $unit = Setting::find(SETTING_DISTANCE_SHORT)->value;
                    $max_distance = 1;
                    //check distance
                    $distance = Utilities::distance($request->lat, $request->lon, $shiftLat, $shiftLon, $unit);

                    if ($distance <= $max_distance) {
                        $type = 'in';
                    } else {
                        $type = 'in_attempt';
                        $staffStatusId = STAFF_STATUS_CHECK_IN_ATTEMPTED;
                    }
                }

                $sc = new StaffCheck;
                $sc->role_staff_id = $id;
                $sc->check_time = date('Y-m-d H:i:s');
                $sc->checker_id = Auth::user()->loggedInAsId();
                $sc->type = $type;
                $sc->lat = $request->lat;
                $sc->lon = $request->lon;
                $sc->distance = $distance;
                $sc->save();

                if ($request->has('photo')) {
                    $photo = $request->photo;
                    $mimeType = $photo->getMimeType();
                    switch ($mimeType) {
                        case 'image/jpeg':
                            $ext = 'jpg';
                            break;
                        case 'image/png':
                            $ext = 'png';
                            break;
                        default:
                            throw new \App\Exceptions\InvalidMimeException();
                    }

                    // store on disk
                    $targetFile = $sc->id . '.' . $ext;
                    $path = Storage::disk('tenant')->putFileAs('check_photos', $request->file('photo'), $targetFile, 'public');

                    // resize and correct orientation
                    GlideImage::create(TENANT_DIR . $path)->modify([
                        'w' => PHOTO_SIZE,
                        'h' => PHOTO_SIZE,
                        'or' => 'auto',
                    ])->save(TENANT_DIR . $path);

                    // create medium thumbnail
                    $targetThumb = TENANT_DIR . 'check_photos/thumbs/' . $targetFile;
                    GlideImage::create(TENANT_DIR . $path)->modify([
                        'w' => PHOTO_SIZE_THUMB,
                        'h' => PHOTO_SIZE_THUMB,
                        'fit' => 'crop',
                    ])->save($targetThumb);

                    $meta = Utilities::getImageMeta($request->file('photo'));
                    if ($meta) {
                        $sc->photo_lat = $meta['lat'];
                        $sc->photo_lon = $meta['lon'];
                        $sc->photo_created_at = $meta['created'];
                    }

                    $sc->ext = $ext;
                    $sc->save();
                }

                $rs->staff_status_id = $staffStatusId;
                $rs->save();

                if ($type == 'in') {
                    $ss = StaffStatus::find($staffStatusId);
                    return response()->api([
                        'message' => $ss->status,
                        'role_message' => $ss->message,
                        'actions' => [],
                    ]);

                } else {
                    $ss = StaffStatus::find($staffStatusId);
                    return response()->api([
                        'message' => $ss->status,
                        'role_message' => $ss->message,
                        'actions' => ['check_in'],
                    ]);
                }
            }
        }
        // this should never happen but just incase
        return response()->api([
            'message' => "You cannot check-in. Status: " . $rs->staff_status_id,
        ], 400);
    }

    /**
     * POST path="/role/checkOut/{id}
     * staff checkout
     */
    public function checkOut(Request $request, $id)
    {
        $rs = RoleStaff::with('ShiftRole.Shift')->findOrFail($id);

        if ($rs->user_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\NotAllowedException();
        }

        if (in_array($rs->staff_status_id, [
            STAFF_STATUS_CHECK_IN_ATTEMPTED,
            STAFF_STATUS_CHECKED_IN,
            STAFF_STATUS_CHECK_OUT_ATTEMPTED,
        ])) {

            $checkOutPhoto = Setting::findOrFail(14)->value;
            if ($checkOutPhoto) {
                $checkOutPhoto = 'required';
            } else {
                $checkOutPhoto = 'sometimes';
            }

            $request->validate([
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
                'photo' => "$checkOutPhoto|mimes:jpg,jpeg,png|dimensions:min_width=500,min_height=500|max:10240
                    ",
            ]);

            $distance = null;
            $type = 'in';
            $staffStatusId = STAFF_STATUS_CHECKED_OUT;
            $shiftLat = $rs->ShiftRole->Shift->lat;
            $shiftLon = $rs->ShiftRole->Shift->lon;

            if ($shiftLat) {
                $unit = Setting::find(SETTING_DISTANCE_SHORT)->value;
                $max_distance = 1;
                //check distance
                $distance = Utilities::distance($request->lat, $request->lon, $shiftLat, $shiftLon, $unit);

                if ($distance <= $max_distance) {
                    $type = 'out';
                } else {
                    $type = 'out_attempt';
                    $staffStatusId = STAFF_STATUS_CHECK_OUT_ATTEMPTED;
                }
            }

            $sc = new StaffCheck;
            $sc->role_staff_id = $id;
            $sc->check_time = date('Y-m-d H:i:s');
            $sc->checker_id = Auth::user()->loggedInAsId();
            $sc->type = $type;
            $sc->lat = $request->lat;
            $sc->lon = $request->lon;
            $sc->distance = $distance;
            $sc->save();

            if ($request->has('photo')) {
                $photo = $request->photo;
                $mimeType = $photo->getMimeType();
                switch ($mimeType) {
                    case 'image/jpeg':
                        $ext = 'jpg';
                        break;
                    case 'image/png':
                        $ext = 'png';
                        break;
                    default:
                        throw new \App\Exceptions\InvalidMimeException();
                }

                // store on disk
                $targetFile = $sc->id . '.' . $ext;
                $path = Storage::disk('tenant')->putFileAs('check_photos', $request->file('photo'), $targetFile, 'public');

                // resize and correct orientation
                GlideImage::create(TENANT_DIR . $path)->modify([
                    'w' => PHOTO_SIZE,
                    'h' => PHOTO_SIZE,
                    'or' => 'auto',
                ])->save(TENANT_DIR . $path);

                // create medium thumbnail
                $targetThumb = TENANT_DIR . 'check_photos/thumbs/' . $targetFile;
                GlideImage::create(TENANT_DIR . $path)->modify([
                    'w' => PHOTO_SIZE_THUMB,
                    'h' => PHOTO_SIZE_THUMB,
                    'fit' => 'crop',
                ])->save($targetThumb);

                $meta = Utilities::getImageMeta($request->file('photo'));
                if ($meta) {
                    $sc->photo_lat = $meta['lat'];
                    $sc->photo_lon = $meta['lon'];
                    $sc->photo_created_at = $meta['created'];
                }

                $sc->ext = $ext;
                $sc->save();
            }

            $rs->staff_status_id = $staffStatusId;
            $rs->save();

            if ($type == 'out') {
                $ss = StaffStatus::find($staffStatusId);
                return response()->api([
                    'message' => $ss->status,
                    'role_message' => $ss->message,
                    'actions' => [],
                ]);

            } else {
                $ss = StaffStatus::find($staffStatusId);
                return response()->api([
                    'message' => $ss->status,
                    'role_message' => $ss->message,
                    'actions' => ['check_out'],
                ]);
            }
        }
        // this should never happen but just incase
        return response()->api([
            'message' => "You cannot check-out. Status: " . $rs->staff_status_id,
        ], 400);
    }

    /**
     * POST path="/role/confirm/{id}
     * staff confirm selection
     */
    public function confirm($id)
    {
        $rs = RoleStaff::findOrFail($id);

        if ($rs->user_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\NotAllowedException();
        }

        switch ($rs->staff_status_id) {
            case STAFF_STATUS_SELECTED:
                $rs->staff_status_id = STAFF_STATUS_CONFIRMED;
                $rs->save();
                break;

            default:
                throw new \App\Exceptions\NotAllowedException();
        }

        return response()->api([
            'message' => "Confirmed.",
            'role_message' => "You are confirmed for this role.",
            'actions' => ['replace'],
        ]);
    }

    /**
     * POST path="/role/{id}/notAvailable
     * staff applying to role
     */
    public function notAvailable($id)
    {
        $sr = ShiftRole::findOrFail($id);
        $actions = [];

        $rs = RoleStaff::where([['shift_role_id', $id], ['user_id', Auth::user()->loggedInAsId()]])->first();
        if (!$rs) {
            $rs = new RoleStaff;
            $rs->staff_status_id = STAFF_STATUS_APPLIED;
            $rs->user_id = Auth::user()->loggedInAsId();
            $rs->shift_role_id = $id;
        }
        if ($rs->staff_status_id == STAFF_STATUS_APPLIED || $rs->staff_status_id == STAFF_STATUS_HIDDEN_REJECTED) {
            $rs->staff_status_id = STAFF_STATUS_NOT_AVAILABLE;
            $rs->save();

            if (!$rs->shiftRole->past() && !$rs->shiftRole->applicationDeadlinePast() && $rs->shiftRole->userEligible(Auth::user()->loggedInAsId()) == '') {
                $actions = ['apply'];
            }

        } else {
            return response()->api([
                'message' => "You have already been selected for this role.",
            ], 400);
        }

        return response()->api([
            'id' => $rs->id,
            'message' => "Not available.",
            'role_message' => "You are not available for this role.",
            'actions' => $actions,
        ]);
    }

    /**
     * POST path="/role/replace/{id}
     * staff confirm selection
     */
    public function replace(Request $request, $id)
    {
        $rs = RoleStaff::findOrFail($id);

        if ($rs->user_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\NotAllowedException();
        }

        $request->validate([
            'reason' => 'required|string|min:5|max:200',
        ]);

        $canReplace = 1; //TODO check replace window, save reason

        if ($canReplace) {

            switch ($rs->staff_status_id) {
                case STAFF_STATUS_SELECTED:
                case STAFF_STATUS_CONFIRMED:
                    $rs->staff_status_id = STAFF_STATUS_REPLACEMENT_REQUESTED;
                    $rs->save();
                    break;
                case STAFF_STATUS_STANDBY:
                    $rs->staff_status_id = STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED;
                    $rs->save();
                    break;

                default:
                    throw new \App\Exceptions\NotAllowedException();
            }

            $rr = new \App\ReplacementReason(['reason' => $request->reason]);
            $rs->replacementReason()->save($rr);

            return response()->api([
                'message' => "Replacement requested.",
                'role_message' => "You have requested a replacement for this role.",
                'actions' => ['cancel_replace'],
                'reason' => $request->reason,
            ]);

        } else {
            return response()->api([
                'message' => "You cannot request a replacement online. Please contact us.",
            ], 400);

        }
    }

    /**
     * POST role/replaceCancel/{id}
     * staff confirm selection
     */
    public function replaceCancel($id)
    {
        $rs = RoleStaff::findOrFail($id);

        if ($rs->user_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\NotAllowedException();
        }

        switch ($rs->staff_status_id) {
            case STAFF_STATUS_REPLACEMENT_REQUESTED:
                $rs->staff_status_id = STAFF_STATUS_CONFIRMED;
                $rs->save();
                $roleMessage = "You are confirmed for this role.";
                break;
            case STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED:
                $rs->staff_status_id = STAFF_STATUS_STANDBY;
                $rs->save();
                $roleMessage = "You are on standby for this role.";
                break;

            default:
                throw new \App\Exceptions\NotAllowedException();
        }

        $rs->replacementReason()->delete();

        return response()->api([
            'message' => "Replacement request cancelled.",
            'role_message' => $roleMessage,
            'actions' => ['replace'],
        ]);
    }

    /**
     * PUT role/update/{id}
     */
    public function update(Request $request, $id)
    {
        $rs = RoleStaff::findOrFail($id);

        $request->validate([
            'expense_limit' => 'sometimes|nullable|numeric|min:0',
            'bill_rate' => 'sometimes|nullable|numeric|min:0',
            'bill_rate_type' => 'sometimes|nullable|in:phr,flat',
            'outsource_company_id' => 'sometimes|nullable|numeric|exists:tenant.outsource_companies,id',
            'pay_rate' => 'sometimes|nullable|numeric|min:0',
            'pay_rate_type' => 'sometimes|nullable|in:phr,flat',
            'pay_status' => 'sometimes|in:no_pay,held,released,submitted,paid',
            'staff_status_id' => 'sometimes|string',
            'staff_start' => 'sometimes|date_format:"H:i"',
            'staff_end' => 'sometimes|date_format:"H:i"',
            'team_leader' => 'sometimes|in:0,1',
            'times_locked' => 'sometimes|in:0,1',
            'unpaid_break' => 'sometimes|nullable|numeric|min:0',
            'paid_break' => 'sometimes|nullable|numeric|min:0',
        ]);

        if ($request->has('bill_rate')) {
            $rs->bill_rate = $request->bill_rate;
        }

        if ($request->has('bill_rate_type')) {
            $rs->bill_rate_type = $request->bill_rate_type;
        }

        if ($request->has('outsource_company_id')) {
            if ($request->outsource_company_id) {
                if ($rs->user->outsourceCompanies()
                    ->where('outsource_company_id', $request->outsource_company_id)
                    ->first()) {
                    $rs->outsource_company_id = $request->outsource_company_id;
                } else {
                    return response()->api([
                        "message" => "The user does not work for the outsource company.",
                    ], 400);
                }
            } else {
                if (!$rs->user->works_here) {
                    return response()->api([
                        "message" => "The user does not work for this company. Please assign the outsource company or change the user to allow working for this company.",
                    ], 400);
                }
            }
        }

        if ($request->has('paid_break')) {
            $rs->paid_break = $request->paid_break;
        }

        if ($request->has('pay_rate')) {
            $rs->pay_rate = $request->pay_rate;
        }

        if ($request->has('pay_rate_type')) {
            $rs->pay_rate_type = $request->pay_rate_type;
        }

        if ($request->has('pay_status')) {
            $rs->pay_status = $request->pay_status;
        }

        if ($request->has('staff_status_id')) {
            $staffStatusId = constant($request->staff_status_id);
            $rs->staff_status_id = $staffStatusId;
            // log activity
        }

        if ($request->has('staff_start')) {
            $rs->loadMissing('shiftRole.shift');
            $start = $rs->shiftRole->role_start ?: $rs->shiftRole->shift->shift_start;
            $date = date('Y-m-d', strtotime($start));
            $rs->staff_start = date("Y-m-d H:i:00", strtotime($date . ' ' . $request->staff_start));
        }

        if ($request->has('staff_end')) {
            $rs->loadMissing('shiftRole.shift');
            $end = $rs->shiftRole->role_end ?: $rs->shiftRole->shift->shift_end;
            $date = date('Y-m-d', strtotime($end));
            $rs->staff_end = date("Y-m-d H:i:00", strtotime($date . ' ' . $request->staff_end));
        }

        if ($request->has('team_leader')) {
            $rs->team_leader = $request->team_leader;
        }

        if ($request->has('times_locked')) {
            $rs->times_locked = $request->times_locked;
        }

        if ($request->has('unpaid_break')) {
            $rs->unpaid_break = $request->unpaid_break;
        }

        $rs->save();

        return response()->api([
            'message' => "Saved.",
        ]);
    }

    // Pay Items ----------
    /**
     * POST /roleStaff/{id}/payItem
     */
    public function createPayItem(Request $request, $id)
    {
        $rs = RoleStaff::findOrFail($id);
        $request->validate([
            'item_name' => 'required|min:1|max:30',
            'item_type' => 'required|in:bonus,deduction,expense,travel,other',
            'unit_rate' => 'required|numeric',
            'unit_rate_type' => 'required|in:pu,flat',
            'units' => 'required|nullable|numeric|min:0',
        ], [
            'item_name.required' => "Please enter a name for the item.",
        ]);

        $pi = new StaffPayItem();
        $pi->item_name = $request->item_name;
        $pi->item_type = $request->item_type;
        $pi->role_staff_id = $id;
        $pi->unit_rate = $request->unit_rate;
        $pi->unit_rate_type = $request->unit_rate_type;
        $pi->units = $request->units;
        $pi->save();

        return response()->api([
            'data' => $pi,
            'message' => "Item saved.",
        ], 201);
    }

    /**
     * GET /roleStaff/payItem/{id}
     */
    public function getPayItem($id)
    {
        $pi = StaffPayItem::findOrFail($id);

        return response()->api($pi);
    }

    /**
     * GET /roleStaff/{id}/payItems
     */
    public function getPayItems($id)
    {
        $pis = StaffPayItem::where('role_staff_id', $id)->orderBy('item_type')->get();

        return response()->api($pis);
    }

    /**
     * DELETE /roleStaff/payItem/{id}
     */
    public function deletePayItem($id)
    {
        StaffPayItem::destroy($id);
        return response()->api([
            'message' => "Item deleted.",
        ]);
    }

    /**
     * PUT /roleStaff/payItem/{id}
     */
    public function updatePayItem(Request $request, $id)
    {
        $pi = StaffPayItem::findOrFail($id);

        $request->validate([
            'item_name' => 'sometimes|string|min:1|max:30',
            'item_type' => 'sometimes|in:bonus,deduction,expense,travel,other',
            'unit_rate' => 'sometimes|numeric',
            'unit_rate_type' => 'sometimes|in:pu,flat',
            'units' => 'sometimes|nullable|numeric|min:0',
        ]);

        if ($request->has('item_name')) {
            $pi->item_name = $request->item_name;
        }
        if ($request->has('item_type')) {
            $pi->item_type = $request->item_type;
        }
        if ($request->has('unit_rate')) {
            $pi->unit_rate = $request->unit_rate;
        }
        if ($request->has('unit_rate_type')) {
            $pi->unit_rate_type = $request->unit_rate_type;
        }
        if ($request->has('units')) {
            $pi->units = $request->units;
        }
        $pi->save();

        return response()->api([
            'data' => $pi,
            'message' => 'Saved.',
        ]);
    }
}
