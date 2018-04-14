<?php
namespace App\Http\Controllers\Api;

use App\RolePayItem;
use App\RoleRequirement;
use App\Shift;
use App\ShiftRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftRoleController extends Controller
{
    /**
     * POST /shift/{id}/role
     */
    public function create(Request $request, $shiftId)
    {
        $request->validate([
            'rname' => 'required|min:1|max:50',
            'outsource_company_id' => 'sometimes|nullable|exists:tenant.outsource_companies,id',
            'pay_category_id' => 'sometimes|nullable|exists:tenant.pay_categories,id',
            'role_start' => 'sometimes|nullable|date_format:H:i',
            'role_end' => 'sometimes|nullable|date_format:H:i',
            'application_deadline' => 'sometimes|nullable|date|after:yesterday',
            'num_required' => 'sometimes|numeric|min:1',
            'bill_rate' => 'sometimes|nullable|numeric|min:0',
            'bill_rate_type' => 'sometimes|nullable|in:phr,flat',
            'pay_rate' => 'sometimes|nullable|numeric|min:0',
            'pay_rate_type' => 'sometimes|nullable|in:phr,flat',
            'paid_break' => 'sometimes|nullable|numeric|min:0',
            'unpaid_break' => 'sometimes|nullable|numeric|min:0',
            'expense_limit' => 'sometimes|nullable|numeric|min:0',
            'requirements' => 'sometimes|array',
        ], [
            'rname.required' => "Please enter a name for the role.",
        ]);

        $shift = Shift::findOrFail($shiftId);

        $role = new ShiftRole();
        $role->shift_id = $shiftId;
        $role->rname = $request->rname;
        $role->outsource_company_id = $request->input('outsource_company_id', null);
        $role->pay_category_id = $request->input('pay_category_id', null);
        $role->notes = $request->input('notes', null);
        $role->completion_notes = $request->input('completion_notes', null);
        $role->num_required = $request->input('num_required', 1);
        $role->bill_rate = $request->input('bill_rate', null);
        $role->bill_rate_type = $request->input('bill_rate_type', null);
        $role->pay_rate = $request->input('pay_rate', null);
        $role->pay_rate_type = $request->input('pay_rate_type', null);
        $role->paid_break = $request->input('paid_break', null);
        $role->unpaid_break = $request->input('unpaid_break', null);
        $role->expense_limit = $request->input('expense_limit', null);

        // store role start and end times. date checked in shiftRole model save event for overnight shifts
        $date = date('Y-m-d', strtotime($role->shift->shift_start));
        if ($request->has('role_start')) {
            // should be time only
            $role->role_start = $date . ' ' . date('H:i:00', strtotime($request->role_start));
        }
        if ($request->has('role_end')) {
            // should be time only
            $role->role_end = $date . ' ' . date('H:i:00', strtotime($request->role_end));
        }

        // store application deadline
        if ($request->has('application_deadline')) {
            $role->application_deadline = $request->application_deadline;
        }

        $role->save();

        //requirements
        if ($request->has('requirements')) {
            foreach ($request->requirements as $req) {
                $arr = explode(':', $req);

                switch ($arr[0]) {
                    case 'sex':
                        $role->sex = $arr[2];
                        $role->save();
                        break;

                    case 'age':
                    case 'attr':
                    case 'rating':
                    case 'wa':
                        self::addRoleRequirement($role->id, $arr[0], $arr[1], $arr[2]);
                        break;

                    case 'pl':
                        self::addRoleRequirement($role->id, $arr[0], $arr[2], $arr[3], $arr[1]);
                        break;
                }
            }
        }

        return response()->api([
            'data' => $role,
            'message' => "Role created.",
        ], 201);
    }

    /**
     * DELETE /shift/role/{id}
     */
    public function delete($id)
    {
        ShiftRole::destroy($id);
        return response()->api([
            'message' => "Role deleted.",
        ]);
    }

    /**
     * POST /helpers/roles/edit
     */
    public function getEdit(Request $request)
    {
        $request->validate([
            'shift_ids' => 'required|array',
            'shift_ids.*' => 'numeric|exists:tenant.shifts,id',
        ]);

        $data = [];
        $data['roles'] = DB::connection('tenant')
            ->table('shift_roles')
            ->select('rname')
            ->whereIn('shift_id', $request->shift_ids)
            ->distinct()
            ->get()
            ->pluck('rname');

        return response()->api($data);
    }

    /**
     * PUT /shift/role/{id}/{direction}
     */
    public function order($id, $dir = 'up')
    {
        $sr = ShiftRole::findOrFail($id);
        $srs = $sr->shift->shiftRoles->sortBy('display_order')->values()->all();

        foreach ($srs as $i => $sr) {
            $sr->display_order = $i;
            $sr->save();
            if ($sr->id == $id) {
                $moveI = $i;
            }
        }

        if ($dir == 'up') {
            if (isset($srs[$moveI - 1])) {
                $srs[$moveI - 1]->display_order = $moveI;
                $srs[$moveI - 1]->save();
                $srs[$moveI]->display_order = $moveI - 1;
                $srs[$moveI]->save();
            }
        } else {
            if (isset($srs[$moveI + 1])) {
                $srs[$moveI + 1]->display_order = $moveI;
                $srs[$moveI + 1]->save();
                $srs[$moveI]->display_order = $moveI + 1;
                $srs[$moveI]->save();
            }
        }

        return response()->api([
            'message' => "Saved.",
        ]);
    }

    /**
     * GET /shift/role/{id}/{staff?}
     */
    public function get($id, $staff = 0)
    {

        if ($staff) {
            if ($staff == 'selected') {
                $role = ShiftRole::with('roleStaff.staffStatus:id,status,border_color')
                    ->with(['roleStaff' => function ($query) {
                        $query->whereIn('staff_status_id', STAFF_STATUSES_SELECTED);
                    }])->findOrFail($id);

            } elseif ($staff == 'standby') {
                $role = ShiftRole::with('roleStaff.staffStatus:id,status,border_color')
                    ->with(['roleStaff' => function ($query) {
                        $query->whereIn('staff_status_id', STAFF_STATUSES_STANDBY);
                    }])->findOrFail($id);

            } elseif ($staff == 'applicants') {
                $role = ShiftRole::with('roleStaff.staffStatus:id,status,border_color')
                    ->with(['roleStaff' => function ($query) {
                        $query->where('staff_status_id', STAFF_STATUS_APPLIED);
                    }])->findOrFail($id);

            } elseif ($staff == 'na') {
                $role = ShiftRole::with('roleStaff.staffStatus:id,status,border_color')
                    ->with(['roleStaff' => function ($query) {
                        $query->whereIn('staff_status_id', STAFF_STATUSES_NA);
                    }])->findOrFail($id);

            } elseif ($staff == 'counts') {
                $role = ShiftRole::findOrFail($id);

                return response()->api([
                    'num_selected' => $role->numSelected(),
                    'num_standby' => $role->numStandby(),
                    'num_applicants' => $role->numApplicants(),
                    'num_na' => $role->numNa(),
                ]);

            } else {
                throw new \App\Exceptions\NotAllowedException();
            }

            foreach ($role->roleStaff as $rs) {
                $rs->name = $rs->user->fname . ' ' . $rs->user->lname;
                $rs->ppic_a = $rs->user->tthumb();
                unset($rs->user);
            }

        } else {
            $role = ShiftRole::with('rolePayItems')->findOrFail($id);
        }

        return response()->api($role);
    }

    private function addRoleRequirement($shiftRoleId, $req, $operator, $value, $otherId = null)
    {
        $rr = new RoleRequirement();
        $rr->shift_role_id = $shiftRoleId;
        $rr->requirement = $req;
        $rr->operator = $operator;
        $rr->value = $value;
        $rr->other_id = $otherId;
        $rr->save();

        return $rr;
    }

    // Role Requirements -------
    /**
     * POST /role/{id}/roleRequirement
     */
    public function createRoleRequirement(Request $request, $id)
    {
        $sr = ShiftRole::findOrFail($id);
        $request->validate([
            'requirement' => 'required|in:age,custom_rating,rating,attr,pl,quiz',
            'operator' => 'required|in:=,!=,<,>',
            'other_id' => 'sometimes|nullable|numeric',
            'value' => 'required|alpha_num',
        ]);

        //check requirement with same operator doesnt already exist on role
        $rr = RoleRequirement::where([
            ['shift_role_id', $id],
            ['requirement', $request->requirement],
            ['operator', $request->operator],
        ])->first();
        if ($rr) {
            return response()->api([
                'data' => $rr,
                'message' => "A requirement of the same type already exists on the role.",
            ], 400);
        }

        $rr = new RoleRequirement();
        $rr->shift_role_id = $id;
        $rr->requirement = $request->requirement;
        $rr->operator = $request->operator;
        $rr->value = $request->value;
        $rr->other_id = $request->input('other_id', null);
        $rr->save();

        return response()->api([
            'data' => $rr,
            'message' => "Requirement saved.",
        ], 201);
    }

    /**
     * GET /role/{id}/roleRequirements
     */
    public function getRoleRequirements($id)
    {
        $rrs = RoleRequirement::where('shift_role_id', $id)->get();

        return response()->api($rrs);
    }

    /**
     * DELETE /role/roleRequirement/{id}
     */
    public function deleteRoleRequirement($id)
    {
        RoleRequirement::destroy($id);
        return response()->api([
            'message' => "Requirement deleted.",
        ]);
    }

    // Pay Items ----------
    /**
     * POST /role/{id}/payItem
     */
    public function createPayItem(Request $request, $id)
    {
        $sr = ShiftRole::findOrFail($id);
        $request->validate([
            'item_name' => 'required|min:1|max:30',
            'item_type' => 'required|in:bonus,expense,travel,other',
            'unit_rate' => 'required|numeric|min:0',
            'unit_rate_type' => 'required|in:pu,flat',
            'units' => 'required|nullable|numeric|min:0',
        ], [
            'item_name.required' => "Please enter a name for the item.",
        ]);

        $rpi = new RolePayItem();
        $rpi->item_name = $request->item_name;
        $rpi->item_type = $request->item_type;
        $rpi->shift_role_id = $id;
        $rpi->unit_rate = $request->unit_rate;
        $rpi->unit_rate_type = $request->unit_rate_type;
        $rpi->units = $request->units;
        $rpi->save();

        return response()->api([
            'data' => $rpi,
            'message' => "Item saved.",
        ], 201);
    }

    /**
     * GET /role/payItem/{id}
     */
    public function getPayItem($id)
    {
        $pi = RolePayItem::findOrFail($id);

        return response()->api($pi);
    }

    /**
     * GET /role/{id}/payItems
     */
    public function getPayItems($id)
    {
        $pis = RolePayItem::where('shift_role_id', $id)->orderBy('item_type')->get();

        return response()->api($pis);
    }

    /**
     * DELETE /role/payItem/{id}
     */
    public function deletePayItem($id)
    {
        RolePayItem::destroy($id);
        return response()->api([
            'message' => "Item deleted.",
        ]);
    }

    /**
     * PUT /role/payItem/{id}
     */
    public function updatePayItem(Request $request, $id)
    {
        $rpi = RolePayItem::findOrFail($id);

        $request->validate([
            'item_name' => 'sometimes|string|min:1|max:30',
            'item_type' => 'sometimes|in:bonus,expense,travel,other',
            'unit_rate' => 'sometimes|numeric|min:0',
            'unit_rate_type' => 'sometimes|in:pu,flat',
            'units' => 'sometimes|nullable|numeric|min:0',
        ]);

        if ($request->has('item_name')) {
            $rpi->item_name = $request->item_name;
        }
        if ($request->has('item_type')) {
            $rpi->item_type = $request->item_type;
        }
        if ($request->has('unit_rate')) {
            $rpi->unit_rate = $request->unit_rate;
        }
        if ($request->has('unit_rate_type')) {
            $rpi->unit_rate_type = $request->unit_rate_type;
        }
        if ($request->has('units')) {
            $rpi->units = $request->units;
        }
        $rpi->save();

        return response()->api([
            'data' => $rpi,
            'message' => 'Saved.',
        ]);
    }
}
