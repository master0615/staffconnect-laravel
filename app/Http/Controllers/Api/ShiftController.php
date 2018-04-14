<?php
namespace App\Http\Controllers\Api;

use App\Client;
use App\Flag;
use App\Location;
use App\OutsourceCompany;
use App\RoleStaff;
use App\Setting;
use App\Shift;
use App\ShiftManager;
use App\ShiftRole;
use App\ShiftStatus;
use App\ShiftTrackingOption;
use App\ShiftWorkArea;
use App\TrackingCategory;
use App\User;
use App\WorkArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    /**
     * POST /shift
     */
    public function create(Request $request)
    {
        $request->validate([
            'address' => 'sometimes|nullable|string|max:120',
            'client_id' => 'sometimes|nullable|exists:tenant.clients,id',
            'contact' => 'sometimes|nullable|string|max:50',
            'generic_title' => 'sometimes|nullable|string|max:50',
            'generic_location' => 'sometimes|nullable|string|max:70',
            'live' => 'sometimes|in:0,1',
            'location' => 'sometimes|nullable|string|max:70',
            'location_id' => 'sometimes|nullable|exists:tenant.locations,id',
            'manager_ids' => 'sometimes|nullable|array',
            'manager_ids.*' => 'numeric|exists:tenant.users,id',
            'notes' => 'sometimes|nullable|string',
            'shift_status_id' => 'sometimes|exists:tenant.shift_statuses,id',
            'shift_start' => 'required|array',
            'shift_end' => 'required|array',
            'shift_start.*' => 'after:last year',
            'shift_end.*' => 'after:last year',
            'shift_group_id' => 'sometimes|nullable|exists:tenant.shift_groups,id',
            'title' => 'required|string|min:1|max:70',
            'tracking_options_ids' => 'sometimes|nullable|array',
            'tracking_options_ids.*' => 'numeric|exists:tenant.tracking_options,id',
            'work_area_ids' => 'sometimes|nullable|array',
            'work_area_ids.*' => 'numeric|exists:tenant.work_areas,id',
        ], [
            'title.required' => "Please enter a title for the shift.",
        ]);

        $shifts = [];
        foreach ($request->shift_start as $i => $shift_start) {
            $shift_end = $request->shift_end[$i];

            $s = new Shift;

            $s->shift_start = date('Y-m-d H:i:00', strtotime($shift_start));
            $s->shift_end = date('Y-m-d H:i:00', strtotime($shift_end));

            //TODO validate dates

            if ($request->has('address')) {
                $s->address = title_case($request->address);
            }
            if ($request->has('client_id')) {
                $s->client_id = $request->client_id;
            }
            if ($request->has('contact')) {
                $s->contact = $request->contact;
            }
            if ($request->has('generic_title')) {
                $s->generic_title = $request->generic_title;
            }
            if ($request->has('generic_location')) {
                $s->generic_location = $request->generic_location;
            }
            if ($request->has('live')) {
                $s->live = $request->live;
            }
            if ($request->has('location')) {
                $s->location = $request->location;
            }
            if ($request->has('notes')) {
                $s->notes = $request->notes;
            }
            if ($request->has('shift_group_id')) {
                $s->shift_group_id = $request->shift_group_id;
            }
            if ($request->has('title')) {
                $s->title = $request->title;
            }
            if ($request->has('location_id')) {
                $s->location_id = $request->location_id;
                //update address contact lat lon if not passed in
                $loc = Location::find($s->location_id);
                if (strlen($s->address) < 5 && strlen($loc->address) > 5) {
                    $s->address = $loc->address;
                    $s->lat = $loc->lat;
                    $s->lon = $loc->lon;
                }
                if (strlen($s->location) < 2 && strlen($loc->location) > 2) {
                    $s->location = $loc->location;
                }

            } elseif (strlen($s->address) > 5) { //geocode
                if (!count($shifts)) { // only geocode once
                    $geo = \App\Helpers\Utilities::geocodeAddress($s->address);
                }
                if ($geo) {
                    $s->lat = $geo['lat'];
                    $s->lon = $geo['lon'];
                    $s->address = $geo['formatted_address'];
                }
            }

            $s->save();

            if ($request->has('manager_ids')) {
                if (is_array($request->manager_ids) && count($request->manager_ids)) {
                    $s->managers()->attach($request->manager_ids);
                }
            }
            if ($request->has('tracking_option_ids')) {
                if (is_array($request->tracking_option_ids) && count($request->tracking_option_ids)) {
                    $s->trackingOptions()->attach($request->tracking_option_ids);
                }
            }
            if ($request->has('work_area_ids')) {
                if (is_array($request->work_area_ids) && count($request->work_area_ids)) {
                    $s->workAreas()->attach($request->work_area_ids);
                }
            }

            $shifts[] = $s;
        }

        $count = count($shifts);
        if ($count == 1) {
            $msg = "Created.";

        } elseif ($count) {
            $msg = "$count shifts created.";

        } else { //shouldn't get here
            $msg = "Nothing was created.";
        }

        return response()->api([
            'data' => $shifts,
            'message' => $msg,
        ], 201);
    }

    /**
     * GET /shift/{id}/checks
     */
    public function checks($id)
    {
        $s = Shift::with('shiftRoles.roleStaff.staffChecks')->findOrFail($id);
        $checks = [];
        foreach ($s->shiftRoles as $sr) {
            foreach ($sr->roleStaff as $rs) {
                $u = $rs->user;
                foreach ($rs->staffChecks as $sc) {
                    $sc->name = $u->name();
                    $sc->ppic_a = $u->tthumb();
                    $checks[] = $sc;
                }
            }
        }

        return response()->api($checks);
    }

    /**
     * DELETE /shift/{id}
     */
    public function delete($id)
    {
        //TODO notify any selected staff?
        Shift::destroy($id);
        return response()->api([
            'message' => "Shift deleted.",
        ]);
    }

    /**
     * GET /shift/{id}
     */
    public function get($id)
    {
        switch (Auth::user()->loggedInAs()->lvl) {

            case 'owner':
            case 'admin':

                $shift = Shift::withTrashed()
                    ->with(['managers:users.id,fname,lname,email,mob,ppic_a,sex', 'shiftRoles.roleStaff.staffStatus:id,status,border_color', 'trackingOptions', 'workAreas:aname'])
                    ->with(['shiftRoles' => function ($query) {
                        $query->orderBy('display_order');
                    }])
                    ->with(['shiftRoles.roleStaff' => function ($query) {
                        $query->whereIn('staff_status_id', STAFF_STATUSES_SELECTED);
                    }])
                    ->findOrFail($id);

                //admin note types
                $shift->admin_note_types = \App\ShiftAdminNoteType::orderBy('tname')->get();
                if ($shift->admin_note_types->count()) {
                    $shift->admin_note_types->prepend(['id' => '', 'tname' => 'default', 'color' => '#ffffff']);
                }

                //flags
                $shift->flags = Flag::orderBy('fname')->get();
                foreach ($shift->flags as $f) {
                    if ($shift->flags()->where('flag_id', $f->id)->first()) {
                        $f->set = 1;
                    } else {
                        $f->set = 0;
                    }
                }

                //RoleStaff of user if in shift
                $selected = 0; //has user been selected for shift?
                $userRSs = $shift->userIn(Auth::user()->loggedInAsId());
                foreach ($userRSs as $rs) {
                    if ($rs->selected) {
                        $selected = 1;
                        break;
                    }
                }

                //can shift be edited
                $shift->editable = 0;
                if (!$shift->locked) {
                    $shift->editable = 1;

                } elseif ($shift->locked == Auth::user()->loggedInAsId()) {
                    $shift->editable = 1;

                } elseif (Auth::user()->loggedInAs()->lvl == 'owner') {
                    $locker = $shift->locker;
                    if ($locker->lvl != 'owner') {
                        $shift->editable = 1;
                    }
                }

                //show bill info?
                $shift->bill_info = 1;

                $shift->tracking_categories = \App\TrackingCategory::select('id', 'cname')
                    ->orderBy('cname')
                    ->get();

                // get user stuff
                $shift->shiftRoles = $shift->shiftRoles->sortBy('display_order')->values()->all();
                foreach ($shift->shiftRoles as $sr) {
                    $sr->num_selected = $sr->numSelected();
                    $sr->num_standby = $sr->numStandby();
                    $sr->num_applicants = $sr->numApplicants();
                    $sr->num_na = $sr->numNa();

                    //pay items
                    $sr->pay_items = $sr->rolePayItems()->orderBy('item_type')->get();
                    foreach ($sr->pay_items as $pi) {
                        if ($pi->unit_rate_type == 'flat') {
                            $pi->total = $pi->unit_rate;
                        } else {
                            $pi->total = $pi->unit_rate * $pi->units;
                        }
                        $pi->type = 'role';
                        unset($pi->shift_role_id);
                    }

                    foreach ($sr->roleStaff as $rs) {
                        $rs->name = $rs->user->fname . ' ' . $rs->user->lname;
                        $rs->ppic_a = $rs->user->tthumb();

                        //staff pay items
                        $rolePayItems = $sr->pay_items->all();
                        $staffPayItems = $rs->staffPayItems()->orderBy('item_type')->get();
                        foreach ($staffPayItems as $pi) {
                            if ($pi->unit_rate_type == 'flat') {
                                $pi->total = $pi->unit_rate;
                            } else {
                                $pi->total = $pi->unit_rate * $pi->units;
                            }
                            //unset role pay item if staff pay item replaces it
                            foreach ($rolePayItems as $i => $rpi) {
                                if ($rpi->id == $pi->role_pay_item_id) {
                                    unset($rolePayItems[$i]);
                                }
                            }
                            $pi->type = 'staff';
                            unset($pi->role_staff_id);
                            unset($pi->role_pay_item_id);
                        }
                        $rs->pay_items = array_merge($rolePayItems, $staffPayItems->all());

                        unset($rs->user);
                    }
                }
                $shift->shift_roles = $shift->shiftRoles;
                unset($shift->shiftRoles); //otherwise there is duplicate shiftRoles and shit_roles wtf?

                //tidy work areas
                $was = [];
                foreach ($shift->workAreas as $wa) {
                    $was[] = ['id' => $wa->pivot->work_area_id, 'aname' => $wa->aname];
                }
                unset($shift->workAreas);
                $shift->work_areas = $was;
                break;

            case 'staff':

                $shift = Shift::withTrashed()
                    ->with(['managers:users.id,fname,lname,email,mob,ppic_a,sex', 'shiftRoles', 'shiftRoles.roleStaff', 'shiftRoles.roleStaff.user', 'trackingOptions', 'workAreas:aname'])
                    ->findOrFail($id);

                //RoleStaff of user if in shift
                $selected = 0; //has user been selected for shift?
                $userRSs = $shift->userIn(Auth::user()->loggedInAsId());
                foreach ($userRSs as $rs) {
                    if ($rs->selected) {
                        $selected = 1;
                        break;
                    }
                }

                if (!$shift->live || $shift->deleted_at) {
                    response()->api([
                        'message' => "The shift is not available.",
                    ], 404);
                }

                if ($selected) {

                    $shift->check_in_photo = Setting::findOrFail(13)->value;
                    $shift->check_out_photo = Setting::findOrFail(14)->value;

                    $shift->tracking_categories = \App\TrackingCategory::select('id', 'cname')
                        ->where('staff_visibility', 'visible')
                        ->orWhere('staff_visibility', 'visible_after_selection')
                        ->orderBy('cname')
                        ->get();

                    //hide contact?
                    if (Setting::findOrFail(71)->value == 'never') {
                        $shift->contact = '';
                    }

                    //hide address?
                    if (Setting::findOrFail(70)->value == 'never') {
                        $shift->address = '';
                    }

                    //hide managers?
                    if (Setting::findOrFail(72)->value == 'never') {
                        $shift->managers = [];
                    }

                } else {

                    $shift->tracking_categories = \App\TrackingCategory::select('id', 'cname')
                        ->where('staff_visibility', 'visible_after_selection')
                        ->orderBy('cname')
                        ->get();

                    //hide title?
                    $shift->title = $shift->generic_title ?? $shift->title;

                    //hide location?
                    $shift->location = $shift->generic_location ?? $shift->location;

                    //hide contact?
                    if (Setting::findOrFail(71)->value != 'always') {
                        $shift->contact = '';
                    }

                    //hide address?
                    if (Setting::findOrFail(70)->value != 'always') {
                        $shift->address = '';
                    }

                    //hide managers?
                    if (Setting::findOrFail(72)->value != 'always') {
                        $shift->managers = [];
                    }

                    //hide notes?
                    if (Setting::findOrFail(73)->value != 'always') {
                        $shift->notes = '';
                    }

                    //convert shift_status for staff color
                }

                foreach ($shift->shiftRoles as $sr) {

                    $selectedRole = 0;
                    $sr->message = '';
                    $sr_actions = [];

                    if (count($userRSs) && isset($userRSs[$sr->id])) {
                        $rs = \App\RoleStaff::find($userRSs[$sr->id]->id);
                        $sr->role_staff_id = $rs->id;
                        $selectedRole = $rs->staffStatus()->first()->selected;
                        $sr->message = $rs->staffStatus()->first()->message;
                        $sr->actions = $rs->actions(Auth::user()->loggedInAsId());

                    } elseif ($sr->past()) {
                        $sr->message = "The role is no longer available.";

                    } elseif ($sr->applicationDeadlinePast()) {
                        $sr->message = "The application deadline has past.";

                    } else {
                        $canApply = $sr->userEligible(Auth::user()->loggedInAsId());
                        if ($canApply == '') {
                            $sr->message = "You are eligible for this role.";
                            $sr->actions = ['apply', 'not_available'];
                        } else {
                            //remove $canApply once all tested
                            $sr->message = "You are not eligible for this role. $canApply";
                        }
                    }

                    // sex thumbnail for applying
                    if ($sr->sex == 'female') {
                        $sr->role_thumb = 'https://staffconnect.net/images/nopic_thumb_female.jpg';
                    } elseif ($sr->sex == 'male') {
                        $sr->role_thumb = 'https://staffconnect.net/images/nopic_thumb_male.jpg';
                    } else {
                        $sr->role_thumb = 'https://staffconnect.net/images/nopic_thumb_either.jpg';
                    }

                    $os = []; //other selected staff
                    foreach ($sr->roleStaff as $rs) {
                        if ($rs->user_id == Auth::user()->loggedInAsId()) {
                            $sr->role_thumb = Auth::user()->loggedInAs()->tthumb();
                            continue;
                        }
                        if ($rs->staffStatus()->first()->selected) {
                            $u = $rs->user()->select('id', 'fname', 'lname', 'sex', 'ppic_a', 'mob')->first();
                            $u->ppic_a = $u->tthumb();
                            $u->name = $u->fname . ' ' . $u->lname;

                            unset($u->fname);
                            unset($u->lname);
                            unset($u->sex);
                            $os[] = $u;
                        }
                    }

                    if ($selected && Setting::findOrFail(66)->value == 'never' || !$selected && Setting::findOrFail(66)->value != 'always') {
                        $os = [];
                    } else {
                        if (!Setting::findOrFail(69)->value) {
                            foreach ($os as $u) {
                                $u->mob = '';
                            }
                        }
                        if (!Setting::findOrFail(68)->value) {
                            foreach ($os as $u) {
                                $u->name = '';
                            }
                        }
                        if (!Setting::findOrFail(67)->value) {
                            foreach ($os as $u) {
                                $u->pic_a = '';
                            }
                        }
                    }

                    //other staff
                    $sr->role_staff = $os;

                    //role pay items
                    $sr->pay_items = $sr->rolePayItems()->orderBy('item_type')->get();
                    foreach ($sr->pay_items as $pi) {
                        if ($pi->unit_rate_type == 'flat') {
                            $pi->total = $pi->unit_rate;
                        } else {
                            $pi->total = $pi->unit_rate * $pi->units;
                        }
                        $pi->type = 'role';
                        unset($pi->shift_role_id);
                    }

                    //overwrite pay rate from pay level
                    if ($sr->pay_category_id) {
                        $pl = Auth::user()->loggedInAs()->payLevels()->where('pay_cat_id', $sr->pay_category_id)->first();
                        if ($pl) {
                            $sr->pay_rate = $pl->pay_rate;
                            $sr->pay_rate_type = $pl->pay_rate_type;
                        }
                    }

                    if ($selectedRole) {
                        //overwrite pay rate from role staff
                        if ($rs->pay_rate != null) {
                            $sr->pay_rate = $rs->pay_rate;
                            $sr->pay_rate_type = $rs->pay_rate_type;
                        }

                        //staff pay items
                        $payItems = $rs->staffPayItems()->orderBy('item_type')->get();
                        foreach ($payItems as $pi) {
                            if ($pi->unit_rate_type == 'flat') {
                                $pi->total = $pi->unit_rate;
                            } else {
                                $pi->total = $pi->unit_rate * $pi->units;
                            }
                            //unset role pay item if staff pay item replaces it
                            foreach ($sr->pay_items as $i => $rpi) {
                                if ($rpi->id == $pi->role_pay_item_id) {
                                    unset($sr->pay_items[$i]);
                                }
                            }
                            $pi->type = 'staff';
                            unset($pi->role_staff_id);
                            unset($pi->role_pay_item_id);
                        }
                        $sr->pay_items = array_merge($sr->pay_items->all(), $payItems->all());

                    } else {
                        //hide notes?
                        if (Setting::findOrFail(74)->value != 'always') {
                            $sr->notes = '';
                        }
                    }

                    unset($sr->roleStaff);
                    unset($sr->shift_id);
                    unset($sr->shift);
                    unset($sr->outsource_company_id);
                    unset($sr->bill_rate);
                    unset($sr->bill_rate_type);
                    unset($sr->pay_category_id);
                    unset($sr->created_at);
                    unset($sr->updated_at);
                }

                //tidy work areas
                $was = [];
                foreach ($shift->workAreas as $wa) {
                    $was[] = $wa->aname;
                }
                unset($shift->workAreas);
                $shift->work_areas = $was;

                unset($shift->client_id);
                unset($shift->generic_location);
                unset($shift->generic_title);
                unset($shift->reports_completed);
                unset($shift->staff_paid);
                unset($shift->bill_status);
                unset($shift->locked);
                unset($shift->updated_at);
                unset($shift->deleted_at);
                break;

            default:
                throw new \App\Exceptions\NotAllowedException();
        }

        //tidy managers
        foreach ($shift->managers as $man) {
            $man->name = $man->fname . ' ' . $man->lname;
            $man->ppic_a = $man->tthumb();

            unset($man->sex);
            unset($man->fname);
            unset($man->lname);
            unset($man->pivot);
        }

        //tidy tracking options
        foreach ($shift->tracking_categories as $cat) {
            $options = [];
            foreach ($shift->trackingOptions as $opt) {
                if ($opt->tracking_cat_id == $cat->id) {
                    $options[] = ['id' => $opt->id, 'oname' => $opt->oname];
                }
            }
            $cat->options = $options;
        }
        unset($shift->trackingOptions);

        return response()->api($shift);
    }

    /**
     * GET /shifts/edit
     */
    public function getEdit()
    {
        $data = [];

        $data['clients'] = Client::select('id', 'cname')->orderBy('cname')->get();

        $data['flags'] = Flag::select('id', 'fname', 'color')->orderBy('fname')->get();

        $data['managers'] = User::select('id', DB::raw("CONCAT(fname,' ',lname) as name"))
            ->whereIn('lvl', ['owner', 'admin'])
            ->where('active', 'active')
            ->orderBy('name')->get();

        $data['outsource_companies'] = OutsourceCompany::select('id', 'cname')->orderBy('cname')->get();

        $data['tracking'] = TrackingCategory::select('id', 'cname')
            ->with(['trackingOptions' => function ($query) {
                $query->select('id', 'tracking_cat_id', 'oname')
                    ->where('active', 1)
                    ->orderBy('oname');
            }])->orderBy('cname')->get();

        $data['work_areas'] = WorkArea::select('id', 'aname')->orderBy('aname')->get();

        return response()->api($data);
    }

    /**
     * GET /shifts/{from}/{to}/{view}/{pageSize?}/{pageNumber?}/{filters?}/{sorts?}
     */
    public function getShifts($from, $to, $view = 'list', $pageSize = 20, $pageNumber = 0, $filters = null, $sorts = null)
    {

        switch (Auth::user()->loggedInAs()->lvl) {
            case 'owner':
            case 'admin':

                if ($view == 'calendar') {
                    $shifts = Shift::select('id', 'shift_group_id', 'title', 'shift_start as start', 'shift_end as end', 'location', 'shift_status_id')
                        ->whereBetween('shift_start', [
                            $from,
                            $to,
                        ]);

                } else { // list view
                    //get columns
                    $includes = \App\Setting::select('value')->where('id', 11)->first()->value;
                    $includes = explode(',', $includes);

                    /* examples of additional columns to add
                    $includes[] = 't|1'; // tracking option
                    $includes[] = DB::raw("'work_area'");
                    $includes[] = 'clients.cname';
                     */

                    $includes = array_merge(
                        [
                            DB::raw('SQL_CALC_FOUND_ROWS shifts.id AS id'),
                            'shift_start',
                            'shift_end',
                            'status',
                            'bg_color',
                            'border_color',
                            'font_color',
                        ], $includes);

                    $join_tracking_options = 0;
                    $join_clients = 0;

                    //get shift columns to display;
                    $columns = [];
                    foreach ($includes as $key) {
                        $key = trim($key, "'"); // for DB::raws
                        $display_name = '';
                        $sortable = 1;

                        switch ($key) {
                            case 'SQL_CALC_FOUND_ROWS shifts.id AS id':
                                $key = 'id';
                                $sortable = 0;
                                break;

                            case 'shift_start':
                                $display_name = 'Date';
                                break;

                            case 'shift_end':
                                $display_name = 'Times';
                                $sortable = 0;
                                break;

                            case 'clients.cname':
                                $display_name = 'Client';
                                $join_clients = 1;
                                break;

                            case 'work_area':
                                $display_name = 'Work Area';
                                $sortable = 0;
                                break;

                            default:
                                if (strpos($key, '|')) {
                                    $arr = explode('|', $key);
                                    $key = $arr[0] . $arr[1];
                                    $display_name = TrackingCategory::select('cname')->where('id', $arr[1])->first()->cname;
                                } else {
                                    $display_name = title_case($key);
                                }
                                break;
                        }

                        $columns[] = ['key_name' => $key, 'display_name' => $display_name, 'sortable' => $sortable, 'sorted' => ''];
                    }

                    // get any extra data not in shifts table
                    foreach ($includes as $i => $element) {
                        if (strpos($element, '|')) {
                            $arr = explode('|', $element);
                            $table = $arr[0];
                            $table_id = $arr[1];
                            switch ($table) {
                                case 't': // tracking
                                    $join_tracking_options = 1;
                                    $includes[$i] = DB::raw("min(case when shift_tracking_option.tracking_cat_id=$table_id THEN shift_tracking_option.oname END) as t$table_id");
                                    break;

                                case 'wa': // work area
                                    $includes[$i] = "'work_areas'";
                                    break;
                            }
                        }
                    }

                    $shifts = Shift::select($includes)
                        ->whereBetween('shift_start', [
                            $from,
                            $to,
                        ]);
                }

                //filters
                if (is_null($filters)) {
                    $filters = [];
                } else {
                    $filters = json_decode(urldecode($filters));
                }
                foreach ($filters as $filter) {
                    $filter = explode(':', $filter);

                    switch ($filter[0]) {
                        case 'deleted':
                            $shifts = $shifts->whereNotNull('deleted_at');
                            break;

                        case 'client_id':
                        case 'location':
                        case 'shift_status_id':
                            $shifts = $shifts->where($filter[0], $filter[1], $filter[2]);
                            break;

                        case 'manager':
                            if ($filter[1] == '=') {
                                $shifts = $shifts
                                    ->whereIn('shifts.id', ShiftManager::select('shift_id')
                                            ->where('user_id', $filter[2])
                                            ->get());
                            } else {
                                $shifts = $shifts
                                    ->whereNotIn('shifts.id', ShiftManager::select('shift_id')
                                            ->where('user_id', $filter[2])
                                            ->get());
                            }
                            break;

                        case 'noWorkArea':
                            $shifts = $shifts
                                ->whereNotIn('shifts.id', ShiftWorkArea::select('shift_id')
                                        ->get());
                            break;

                        case 'outsource_company_id':
                            //only checks role.. should we also check RoleStaff?
                            $shifts = $shifts
                                ->whereIn('shifts.id', ShiftRole::select('shift_id')
                                        ->whereNotNull('outsource_company_id')
                                        ->get());
                            break;

                        case 'selected': // selected staff
                            $shifts = $shifts
                                ->whereIn('shifts.id', ShiftRole::select('shift_id')
                                        ->whereIn('id', RoleStaff::select('shift_role_id')
                                                ->where('user_id', $filter[2])
                                                ->whereIn('staff_status_id', STAFF_STATUSES_SELECTED)
                                                ->get())
                                        ->get());
                            break;

                        case 'tracko': //tracking options
                            if ($filter[1] == '=') {
                                $shifts = $shifts
                                    ->whereIn('shifts.id', ShiftTrackingOption::select('shift_id')
                                            ->where('tracking_option_id', $filter[2])
                                            ->get());
                            } else {
                                $shifts = $shifts
                                    ->whereNotIn('shifts.id', ShiftTrackingOption::select('shift_id')
                                            ->where('tracking_option_id', $filter[2])
                                            ->get());
                            }
                            break;

                        case 'wa': //work area
                            if ($filter[1] == '=') {
                                $shifts = $shifts
                                    ->whereIn('shifts.id', ShiftWorkArea::select('shift_id')
                                            ->where('work_area_id', $filter[2])
                                            ->get());
                            } else {
                                $shifts = $shifts
                                    ->whereNotIn('shifts.id', ShiftWorkArea::select('shift_id')
                                            ->where('work_area_id', $filter[2])
                                            ->get());
                            }
                            break;
                    }
                }

                if ($view == 'calendar') {

                    $shifts = $shifts->with('shiftStatus')->get();

                    foreach ($shifts as $shift) {
                        $shift->eventBackgroundColor = $shift->shiftStatus->bg_color;
                        $shift->eventBorderColor = $shift->shiftStatus->border_color;
                        $shift->eventTextColor = $shift->shiftStatus->font_color;

                        unset($shift['location']);
                        unset($shift['shift_status_id']);
                        unset($shift['shiftStatus']);
                    }

                    return response()->api($shifts);

                } else {

                    if (is_null($sorts)) {
                        $sorts = [];
                        $sorts[] = 'shift_start:desc';
                    } else {
                        $sorts = json_decode(urldecode($sorts));
                    }
                    $sorts = array_slice($sorts, count($sorts) - 3, 3); //limit to 3 col sort even though ngx-datatables doesn't indicate

                    // join shift status. cannot use laravel's ->with('shiftStatus') or count rows doesn't work
                    $shifts = $shifts->join('shift_statuses', 'shift_statuses.id', 'shifts.shift_status_id');

                    if ($join_clients) {
                        $shifts = $shifts->leftJoin('clients', 'clients.id', '=', 'shifts.client_id');
                    }
                    if ($join_tracking_options) {
                        $shifts = $shifts->leftJoin(DB::raw("(SELECT tracking_option_id,oname,shift_id,tracking_cat_id FROM tracking_options tos JOIN shift_tracking_option stos ON (tos.id=stos.tracking_option_id)) as shift_tracking_option"), 'shift_tracking_option.shift_id', '=', 'shifts.id');
                    }
                    $shifts = $shifts->groupBy('shifts.id');

                    // sort
                    foreach ($sorts as $sort) {
                        $sort = explode(':', $sort);
                        $shifts = $shifts->orderBy($sort[0], $sort[1]);

                        if ($key = array_search($sort[0], array_column($columns, 'key_name'))) {
                            $columns[$key]['sorted'] = $sort[1];
                        }
                    }

                    // paginate
                    if (is_numeric($pageSize)) {
                        if ($pageNumber) {
                            $shifts = $shifts->skip($pageNumber * $pageSize);
                        }
                        $shifts = $shifts->take($pageSize);
                    }
                    $shifts = $shifts->get();

                    $totalCount = DB::connection('tenant')->table('shifts')->selectRaw('FOUND_ROWS() as totalCount')->first()->totalCount;

                    foreach ($shifts as $shift) {
                        $shift->shift_end = date('g:i a', strtotime($shift->shift_start)) . ' - ' . date('g:i a', strtotime($shift->shift_end));
                        $shift->shift_start = date('Y-m-d', strtotime($shift->shift_start));
                        if (isset($shift->work_area)) {
                            $work_area = '';
                            $was = $shift->workAreas()->select('aname')->get();
                            if ($was->count()) {
                                $work_area = implode(', ', $was->pluck('aname')->all());
                            }
                            $shift->work_area = $work_area;
                        }
                    }

                    return response()->api([
                        'data' => $shifts,
                        'columns' => $columns,
                        'page_number' => $pageNumber,
                        'page_size' => $pageSize,
                        'total_counts' => $totalCount,
                    ]);
                }

                break;

            case 'staff':
                // can staff see & apply for future shifts?
                $staff_apply_shifts = 0;
                if (Auth::user()->loggedInAs()->works_here) {
                    $staff_apply_shifts = 1;
                }

                $shifts_in = Shift::select('id', 'shift_group_id', 'title', 'shift_start as start', 'shift_end as end', 'location')
                    ->whereBetween('shift_start', [
                        $from,
                        $to,
                    ])
                    ->whereIn('id', function ($query) {
                        $query->select('shift_id')
                            ->from('shift_roles')
                            ->join('role_staff', 'shift_roles.id', '=', 'role_staff.shift_role_id')
                            ->join('staff_statuses', 'role_staff.staff_status_id', '=', 'staff_statuses.id')
                            ->where('user_id', Auth::user()->loggedInAsId());
                    })
                    ->orderBy('shift_start')
                    ->get();

                foreach ($shifts_in as $shift) {
                    $colors = $shift->userShiftColor(Auth::user()->loggedInAsId());
                    $shift->eventBackgroundColor = $colors->bg_color;
                    $shift->eventBorderColor = $colors->border_color;
                    $shift->eventTextColor = $colors->font_color;
                }

                $shifts_avail = collect();
                if ($staff_apply_shifts) {
                    $shifts_avail = Shift::select('id', 'shift_group_id', 'title', 'shift_start as start', 'shift_end as end', 'location')
                        ->whereBetween('shift_start', [
                            $from,
                            $to,
                        ])
                        ->where('live', '1')
                        ->whereNotIn('id', function ($query) {
                            $query->select('shift_id')
                                ->from('shift_roles')
                                ->join('role_staff', 'shift_roles.id', '=', 'role_staff.shift_role_id')
                                ->join('staff_statuses', 'role_staff.staff_status_id', '=', 'staff_statuses.id')
                                ->where('user_id', Auth::user()->loggedInAsId());
                        })
                        ->orderBy('shift_start')
                        ->get();

                    foreach ($shifts_avail as $i => $shift) {
                        $s = Shift::find($shift->id);
                        if (!$s->userEligible(Auth::user()->loggedInAsId())) {
                            unset($shifts_avail[$i]);
                        }
                        $shift->eventBackgroundColor = '#1E88E5';
                        $shift->eventBorderColor = '#1E88E5';
                        $shift->eventTextColor = '#FFF';
                    }
                }

                $shifts = $shifts_in->merge($shifts_avail);

                if ($view == 'calendar') {

                    foreach ($shifts as $shift) {
                        unset($shift['location']);
                        unset($shift['shift_status_id']);
                        unset($shift['shiftStatus']);
                    }

                    return response()->api($shifts);

                } else {
                    $count = count($shifts);

                    return response()->api([
                        'shifts' => $shifts,
                        'count' => $count,
                    ]);
                }

                break;

            default:
                $shifts = [];
        }
    }

    /**
     * PUT /shift/{id}/lock/{set}
     * lock shift to prevent edits
     */
    public function lock($id, $set)
    {
        $s = Shift::findOrFail($id);
        // not locked
        if (!$s->locked) {

            if ($set == 1) {
                $s->locked = Auth::user()->loggedInAsId();

            } else {
                return response()->api([
                    'message' => "The shift is already unlocked.",
                ], 400);
            }

            // locked by user
        } elseif ($s->locked == Auth::user()->loggedInAsId()) {

            if ($set == 1) {
                return response()->api([
                    'message' => "The shift is already locked.",
                ], 400);

            } else {
                $s->locked = null;
            }

            // user is owner and shift locked by an admin
        } elseif (Auth::user()->loggedInAs()->lvl == 'owner' && $s->locker->lvl != 'owner') {

            if ($set == 1) {
                $s->locked = Auth::user()->loggedInAsId();

            } else {
                $s->locked = null;
            }

        } else {
            return response()->api([
                'message' => "The shift has been locked by " . $locker->name(),
            ], 400);
        }

        if ($set == 1) {
            $m = "locked";
        } else {
            $m = "unlocked";
        }
        $s->save();

        return response()->api([
            'message' => "Shift $m.",
        ]);
    }

    /**
     * PUT /shift/{id}/publish/{set}
     * make shift visible or invisible to all besides admin / owner
     */
    public function publish($id, $set)
    {
        $s = Shift::findOrFail($id);
        if ($set == 1) {
            $s->live = 1;
            $m = "published";
        } else {
            $s->live = 0;
            $m = "un-published";
        }
        $s->save();

        return response()->api([
            'message' => "Shift $m.",
        ]);
    }

    // not added to routes
    public function restoreShift($id)
    {
        Shift::withTrashed()->findOrFail($id)->restore();
        return response()->api([
            'message' => "Shift restored.",
        ]);
    }

    /**
     * PUT /shift/{id}
     * single shift edit
     */
    public function update(Request $request, $id)
    {
        $s = Shift::findOrFail($id);
        $request->request->add(['ids' => [$id]]);
        return self::updateMultiple($request);
    }

    /**
     * POST /shifts/edit
     * multiple shift edit
     */
    public function updateMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'numeric|exists:tenant.shifts,id',
            'address' => 'sometimes|nullable|string|max:120',
            'bill_status' => 'sometimes|nullable|in:invoiced,paid',
            'client_id' => 'sometimes|nullable|exists:tenant.clients,id',
            'contact' => 'sometimes|nullable|string|max:50',
            'flags' => 'sometimes|nullable|array',
            'flags.id' => 'numeric|exists:tenant.flags,id',
            'flags.set' => 'in:0,1',
            'generic_title' => 'sometimes|nullable|string|max:50',
            'generic_location' => 'sometimes|nullable|string|max:70',
            'lat' => 'sometimes|numeric',
            'live' => 'sometimes|in:0,1',
            'location' => 'sometimes|nullable|string|max:70',
            'location_id' => 'sometimes|nullable|exists:tenant.locations,id',
            'locked' => 'sometimes|nullable|in:0,1',
            'lon' => 'sometimes|numeric',
            'manager_ids' => 'sometimes|nullable|array',
            'manager_ids.*' => 'numeric|exists:tenant.users,id',
            'notes' => 'sometimes|nullable|string',
            'shift_start' => 'sometimes|after:last year',
            'shift_end' => 'sometimes|after:shift_start',
            'shift_date' => 'sometimes|after:last year',
            'shift_end_time' => 'sometimes',
            'shift_group_id' => 'sometimes|nullable|exists:tenant.shift_groups,id',
            'shift_start_time' => 'sometimes',
            'timezone' => 'sometimes|string|in:' . implode(',', timezone_identifiers_list()),
            'title' => 'sometimes|string|min:1|max:70',
            'tracking' => 'sometimes|nullable|array',
            'tracking.cat_id' => 'numeric|exists:tenant.tracking_categories,id',
            'tracking.options' => 'nullable|array',
            'tracking.options.*' => 'numeric|exists:tenant.tracking_options,id',
            'work_area_ids' => 'sometimes|nullable|array',
            'work_area_ids.*' => 'numeric|exists:tenant.work_areas,id',
        ]);

        $count = 0; // count successful
        $locked = 0; //count how many cannot be edited due to lock status

        foreach ($request->ids as $id) {
            $s = Shift::findOrFail($id);

            if ($s->locked && $s->locked != Auth::user()->loggedInAsId() && (Auth::user()->loggedInAs()->lvl != 'owner' || $s->locker->lvl == 'owner')) {
                $locked++;
                continue;
            }

            if ($request->has('address')) {
                $s->address = title_case($request->address);
            }
            if ($request->has('bill_status')) {
                $s->bill_status = $request->bill_status;
            }
            if ($request->has('client_id')) {
                $s->client_id = $request->client_id;
            }
            if ($request->has('contact')) {
                $s->contact = $request->contact;
            }
            if ($request->has('flags')) {
                foreach ($request->flags as $f) {
                    if ($f['set']) {
                        $s->flags()->syncWithoutDetaching([$f['id']]);
                    } else {
                        $s->flags()->detach($f['id']);
                    }
                }
            }
            if ($request->has('generic_title')) {
                $s->generic_title = $request->generic_title;
            }
            if ($request->has('generic_location')) {
                $s->generic_location = $request->generic_location;
            }
            if ($request->has('lat')) {
                $s->lat = $request->lat;
            }
            if ($request->has('live')) {
                $s->live = $request->live;
            }
            if ($request->has('location')) {
                $s->location = $request->location;
            }
            if ($request->has('locked')) {
                //checked if allowed at beginning
                if ($request->locked) {
                    $s->locked = Auth::id();
                } else {
                    $s->locked = null;
                }
            }
            if ($request->has('lon')) {
                $s->lon = $request->lon;
            }
            if ($request->has('manager_ids')) {
                if (is_array($request->manager_ids)) {
                    $s->managers()->sync($request->manager_ids);
                } else {
                    $s->managers()->detach();
                }
            }
            if ($request->has('notes')) {
                $s->notes = $request->notes;
            }
            if ($request->has('shift_start')) {
                $s->shift_start = date('Y-m-d H:i:00', strtotime($request->shift_start));
            }
            if ($request->has('shift_end')) {
                $s->shift_start = date('Y-m-d H:i:00', strtotime($request->shift_end));
            }
            if ($request->has('shift_date')) {
                $start = date('H:i:00', strtotime($s->shift_start));
                $s->shift_start = date('Y-m-d ', strtotime($request->shift_date)) . $start;
            }
            if ($request->has('shift_end_time')) {
                if ($request->has('shift_end_time')) {
                    $date = date('Y-m-d', strtotime($s->shift_start));
                    $s->shift_end = $date . date(' H:i:00', strtotime($request->shift_end_time));
                }
            }
            if ($request->has('shift_group_id')) {
                $s->shift_group_id = $request->shift_group_id;
            }
            if ($request->has('shift_start_time')) {
                $date = date('Y-m-d', strtotime($s->shift_start));
                $s->shift_start = $date . date(' H:i:00', strtotime($request->shift_start_time));
            }
            if ($request->has('timezone')) {
                $s->timezone = $request->timezone;
            }
            if ($request->has('title')) {
                $s->title = $request->title;
            }
            if ($request->has('tracking')) {
                foreach ($request->tracking as $t) { //for each tracking category
                    DB::connection('tenant')
                        ->table('shift_tracking_option')
                        ->join('tracking_options', 'shift_tracking_option.tracking_option_id', '=', 'tracking_options.id')
                        ->where([['shift_id', $s->id], ['tracking_cat_id', $t['cat_id']]])
                        ->delete();
                    $s->trackingOptions()->syncWithoutDetaching($t['options']);
                }
            }
            if ($request->has('work_area_ids')) {
                if (is_array($request->work_area_ids)) {
                    $s->workAreas()->sync($request->work_area_ids);
                } else {
                    $s->workAreas()->detach();
                }
            }

            if ($request->has('location_id')) {
                $s->location_id = $request->location_id;
                //update address contact lat lon if not passed in
                $loc = Location::find($s->location_id);
                if (strlen($s->address) < 5 && strlen($loc->address) > 5) {
                    $s->address = $loc->address;
                    $s->lat = $loc->lat;
                    $s->lon = $loc->lon;
                }
                if (strlen($s->location) < 2 && strlen($loc->location) > 2) {
                    $s->location = $loc->location;
                }

            } elseif (strlen($s->address) > 5) { //geocode
                if (!$count) { // only geocode once
                    $geo = \App\Helpers\Utilities::geocodeAddress($s->address);
                }
                if ($geo) {
                    $s->lat = $geo['lat'];
                    $s->lon = $geo['lon'];
                    $s->address = $geo['formatted_address'];
                }
            }

            $s->save();
            $count++;
        }

        if ($count) {
            $msg = "$count " . str_plural('shift', $count) . " updated.";

        } else { //shouldn't get here
            $msg = "Nothing was updated.";
        }
        if ($locked) {
            $msg .= " $locked " . str_plural('shift', $locked) . ' ' . ($locked == 1 ? 'is' : 'are') . " locked and cannot be editted.";
        }

        return response()->api([
            'message' => $msg,
        ]);
    }

    // Managers --------------------

    /**
     * PUT /shift/{shiftId}/manager/{userIds}
     */
    public function setManagers($shiftId, $userIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->managers()->sync($userIds);
        return response()->api([
            'message' => "Managers saved.",
        ]);
    }

    /**
     * GET /shift/{id}/manager
     */
    public function getManagers($shiftId)
    {
        $mans = Shift::findOrFail($shiftId)->managers;
        return response()->api($mans);
    }

    /**
     * POST /shift/{shiftId}/manager/{userIds}
     */
    public function addManagers($shiftId, $userIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->managers()->syncWithoutDetaching($userIds);
        return response()->api([
            'message' => "Managers added.",
        ]);
    }

    /**
     * DELETE /shift/{shiftId}/manager/{userIds}
     */
    public function unsetManagers($shiftId, $userIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->managers()->detach($userIds);
        return response()->api([
            'message' => "Managers removed.",
        ]);
    }

    // Tracking Options --------------------
    // assign options to shifts - deletes all others not in array
    /**
     * PUT shift/{shiftId}/tracking/{trackingOptionIds}
     */
    public function setTrackingOptions($shiftId, $trackingOptionIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->trackingOptions()->sync($trackingOptionIds);
        return response()->api([
            'message' => "Tracking options saved.",
        ]);
    }

    /**
     * GET /shift/{shiftId}/tracking/{trackingCatId?}
     */
    public function getTrackingOptions($shiftId, $trackingCatId = false)
    {
        if ($trackingCatId) {
            $tos = Shift::findOrFail($shiftId)->trackingOptions->where('tracking_cat_id', $trackingCatId);
        } else {
            $tos = Shift::findOrFail($shiftId)->trackingOptions;
        }
        return response()->api($tos);
    }

    /**
     * POST /shift/{shiftId}/tracking/{trackingOptionIds}
     */
    public function addTrackingOptions($shiftId, $trackingOptionIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->trackingOptions()->syncWithoutDetaching($trackingOptionIds);
        return response()->api([
            'message' => "Tracking options added.",
        ]);
    }

    /**
     * DELETE shift/{shiftId}/tracking/{trackingOptionIds}
     */
    public function unsetTrackingOptions($shiftId, $trackingOptionIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->trackingOptions()->detach($trackingOptionIds);
        return response()->api([
            'message' => "Tracking options unset.",
        ]);
    }

    // Work Areas --------------------
    // assign work areas to shifts - deletes all others not in work_area_ids
    /**
     * PUT /shift/{shiftId}/workArea/{workAreaIds}
     */
    public function setWorkAreas($shiftId, $workAreaIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->workAreas()->sync($workAreaIds);
        return response()->api([
            'message' => "Work areas saved.",
        ]);
    }

    /**
     * GET /shift/{shiftId}/workArea
     */
    public function getWorkAreas($shiftId)
    {
        $was = Shift::findOrFail($shiftId)->workAreas;
        return response()->api($was);
    }

    /**
     * POST /shift/{shiftId}/workArea/{workAreaIds}
     */
    public function addWorkAreas($shiftId, $workAreaIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->workAreas()->syncWithoutDetaching($workAreaIds);
        return response()->api([
            'message' => "Work areas saved.",
        ]);
    }

    /**
     * DELETE /shift/{shiftId}/workArea/{workAreaIds}
     */
    public function unsetWorkAreas($shiftId, $workAreaIds)
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->workAreas()->detach($workAreaIds);
        return response()->api([
            'message' => "Work areas unset.",
        ]);
    }
    // -----------------------------
}
