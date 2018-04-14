<?php
namespace App\Http\Controllers\Api;

use App\AttributeUser;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 *  @SWG\Tag(
 *      name="user",
 *      description="User"
 *  )
 */
class UserController extends Controller
{

    public function __construct()
    {
        // $this->middleware( 'cors' );
    }

    /**
     * GET users/{pageSize}/{pageNumber}/{filters}/{sorts?}
     * users table can request profile_data using pd|[profile_element_id]
     */
    public function index($pageSize = 20, $pageNumber = 0, $filters = null, $sorts = null)
    {
        $dist_symbol = \App\Setting::select('value')->where('id', 7)->first()->value;
        $join_profile_data = 0;
        $includes_added = []; //need to get extra columns sometimes to work out other columns vals.
        $columns = [];

        if (is_null($filters)) {
            $filters = [];
        } else {
            $filters = json_decode(urldecode($filters));
        }

        if (is_null($sorts)) {
            $sorts = [];
            $sorts[] = 'created_at:desc';
        } else {
            $sorts = json_decode(urldecode($sorts));
        }
        $sorts = array_slice($sorts, count($sorts) - 3, 3); //limit to 3 col sort even though ngx-datatables doesn't indicate

        //get user columns to display;
        $includes = \App\Setting::select('value')->where('id', 9)->first()->value;
        $includes = explode(',', $includes);

        //check if distance column should be injected
        // have to call distance function again in filter because function alias can't be used in where cluase :( maybe mysql supports in future?
        $dist_i = 1; // used to process distance result also
        $includes_distances = [];
        foreach ($filters as $filter) {
            $filter = explode(':', $filter);
            if ($filter[0] == 'near') {
                $lat = $filter[1];
                $lon = $filter[2];

                $includes_distances[] = DB::raw("distance(lat,lon,$lat,$lon) as d$dist_i");
                ++$dist_i;
            }
        }

        $includes = array_merge(array_merge([DB::raw('SQL_CALC_FOUND_ROWS users.id AS id'), 'ppic_a', 'fav'], $includes_distances), $includes);

        /* example of additional columns to add
        $includes[] = 'pd|24'; // profile element 24 = height
        $includes[] = 'age';
         */

        //sex needed for ppic_a male or female when no profile photo uploaded
        if (!in_array('sex', $includes)) {
            $includes_added[] = 'sex';
        }

        foreach ($includes as $key) {
            $display_name = '';
            $sortable = 1;

            switch ($key) {
                case 'SQL_CALC_FOUND_ROWS users.id AS id':
                    $key = 'id';
                    $sortable = 0;
                    break;

                case 'age':
                    $display_name = 'Age';
                    if (!in_array('dob', $includes)) {
                        //need dob to get age
                        $includes_added[] = 'dob';
                    }
                    break;

                case 'fname':
                    $display_name = 'First Name';
                    break;

                case 'lname':
                    $display_name = 'Last Name';
                    break;

                case 'email':
                    $display_name = 'Email';
                    break;

                case 'last_login':
                    $display_name = 'Last Login';
                    break;

                case 'mob':
                    $display_name = 'Mobile';
                    break;

                default:
                    if (strpos($key, 'distance(') === 0) {
                        $key = substr($key, -2);
                        $display_name = $dist_symbol;

                    } elseif (strpos($key, '|')) {
                        $arr = explode('|', $key);
                        $key = $arr[0] . $arr[1];
                        $display_name = \App\ProfileElement::select('ename')->where('id', $arr[1])->first()->ename;
                    }
                    break;
            }

            $columns[] = ['key_name' => $key, 'display_name' => $display_name, 'sortable' => $sortable, 'sorted' => ''];
        }

        $includes = array_merge($includes, $includes_added);

        // get any extra data not in user table
        foreach ($includes as $i => $element) {
            if ($element == 'age') {
                $includes[$i] = 'dob as age';
            }
            if (strpos($element, '|')) {
                $arr = explode('|', $element);
                $table = $arr[0];
                $table_id = $arr[1];
                switch ($table) {
                    case 'pd': // profile_data
                        $join_profile_data = 1;
                        $includes[$i] = DB::raw("min(case when profile_data.profile_element_id=$table_id THEN profile_data.data END) as pd$table_id");
                        break;
                    case 'a': // attributes?
                        break;
                }
            }
        }
        $users = User::select($includes);

        // filters
        $gotActive = 0;
        $lvls = '';

        foreach ($filters as $filter) {
            // if filter['key'] has '|' then it is not in user table
            $filter = explode(':', $filter);

            switch ($filter[0]) {
                case 'active':
                    $gotActive = 1;
                case 'sex':
                    $users = $users->where($filter[0], $filter[1], $filter[2]);
                    break;

                case 'age':
                    $users = $users->whereRaw("TIMESTAMPDIFF(YEAR,users.dob,CURDATE()) " . $filter[1] . $filter[2]);
                    break;

                case 'attr': //attribute
                    if ($filter[1] == '=') {
                        $users = $users
                            ->whereIn('users.id', AttributeUser::select('user_id')
                                    ->where('attribute_id', $filter[2])
                                    ->get());
                    } else {
                        $users = $users
                            ->whereNotIn('users.id', AttributeUser::select('user_id')
                                    ->where('attribute_id', $filter[2])
                                    ->get());
                    }
                    break;

                case 'near':
                    $lat = $filter[1];
                    $lon = $filter[2];
                    $radius = $filter[3];
                    $place_id = $filter[4];

                    if ($dist_symbol == 'km') {
                        $radius /= 1.60934;
                    }

                    $users = $users
                        ->whereNotNull('lat')
                        ->whereRaw("distance(lat,lon,$lat,$lon) <= $radius");
                    //dispatch save location job?

                    break;

                case 'pl': // profile list. note profile_element id is filter[1]
                    break;

                case 'utype':
                    if ($filter[2] == 'all') {
                        $lvls = ['staff', 'administrator', 'owner'];

                    } elseif ($filter[2] == 'incomplete') {
                        $lvls = ['registrant1', 'registrant2'];

                    } else {
                        $lvls = [$filter[2]];
                    }
                    break;

                case 'wa': //work area
                    $users = $users
                        ->whereIn('users.id', UserWorkArea::select('user_id')
                                ->where('work_area_id', $filter[2])
                                ->get());
                    break;
            }
        }
        if (!$gotActive) {
            $users = $users->where('active', 'active');
        }
        if ($lvls != '') {
            $users = $users->whereIn('lvl', $lvls);
        }

        if ($join_profile_data) {
            $users = $users->leftJoin('profile_data', 'users.id', '=', 'profile_data.user_id');
        }
        $users = $users->groupBy('users.id');

        // sort
        foreach ($sorts as $sort) {
            $sort = explode(':', $sort);

            //remove any sorts that may be from distance cols that have been removed
            if (preg_match('/d[0-9]/', $sort[0])) {
                $num = ltrim($sort[0], 'd');
                if ($num >= $dist_i) {
                    continue;
                }
            }

            $users = $users->orderBy($sort[0], $sort[1]);

            if ($key = array_search($sort[0], array_column($columns, 'key_name'))) {
                $columns[$key]['sorted'] = $sort[1];
            }
        }

        // paginate
        if (is_numeric($pageSize)) {
            if ($pageNumber) {
                $users = $users->skip($pageNumber * $pageSize);
            }
            $users = $users->take($pageSize);
        }

        $users = $users->get();

        foreach ($users as $user) {
            $user->ppic_a = $user->tthumb();
            if (in_array('age', $includes)) {
                $user->age = $user->age();
            }

            //remove extra included - not needed anymore
            foreach ($includes_added as $key) {
                unset($user->$key);
            }
        }

        if ($dist_i > 1) {

            foreach ($users as $user) {
                for ($i = 1; $i < $dist_i; $i++) {
                    if ($dist_symbol == 'km') {
                        $user['d' . $i] *= 1.60934;
                    }
                    $user['d' . $i] = round($user['d' . $i], 1);
                }
            }
        }

        $totalCount = DB::connection('tenant')->table('users')->selectRaw('FOUND_ROWS() as totalCount')->first()->totalCount;

        $response['data'] = $users;
        $response['columns'] = $columns;
        $response['page_number'] = $pageNumber;
        $response['page_size'] = $pageSize;
        $response['total_counts'] = $totalCount;

        return response()->api($response);
    }

    /**
     *  POST /user
     */
    public function create(Request $request)
    {
        $request->validate([
            'fname' => 'required|min:2|max:20',
            'lname' => 'required|min:2|max:20',
            'email' => 'required|email|unique:tenant.users|min:5',
            'password' => 'required|min:7',
        ], [
            'fname.required' => "Please enter the user's first name.",
            'lname.required' => "Please enter the user's last name.",
            'email.unique' => "The email address is already registered on the system",
        ]);

        $u = new User();
        $u->fname = title_case($request->fname);
        $u->lname = title_case($request->lname);
        $u->mob = $request->input('mob', null);
        $u->email = strtolower($request->email);
        if ($request->has('dob')) {
            $dob = $request->dob;
            if (strlen($dob > 6)) {
                $u->dob = date('Y-m-d', strtotime($dob));
            }
        }
        $u->sex = strtolower($request->input('sex', null));
        $u->lvl = $request->input('lvl', 'staff');
        $u->password = bcrypt($request->password);
        $u->save();

        if ($request->has('welcome_email')) {
            $u->notify(new \App\Notifications\NewUser($request->password));
        }

        return response()->api([
            'data' => $u,
            'message' => "User created.",
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->all());

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function delete(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }

    // Attributes --------------------
    public function getAttributes($userId)
    {
        $attrs = User::findOrFail($userId)->attributes;
        return response()->api([
            'data' => $attrs,
        ]);
    }

    public function setAttribute($attributeId, $userId)
    {
        $user = User::findOrFail($userId);
        $user->attributes()->syncWithoutDetaching([
            $attributeId => [
                'created_at' => date('Y-m-d H:i:s'),
                'setter_id' => Auth::id(),
            ],
        ]);
        return response()->api([
            'message' => "Saved.",
        ]);
    }

    public function unsetAttribute($attributeId, $userId)
    {
        $user = User::findOrFail($userId);
        $user->attributes()->detach($attributeId);
        return response()->api([
            'message' => "Saved.",
        ]);
    }

    // Outsource Companies --------------------
    public function getOutsourceCompanies($userId)
    {
        $ocs = User::findOrFail($userId)->outsourceCompanies;
        return response()->api([
            'data' => $ocs,
        ]);
    }

    public function setOutsourceCompany($outsourceCompanyId, $userId)
    {
        $user = User::findOrFail($userId);
        if ($outsourceCompanyId == 0) {
            // this company
            $user->works_here = 1;
            $user->save();
        } else {
            $user->outsourceCompanies()->syncWithoutDetaching([
                $outsourceCompanyId,
            ]);
        }
        return response()->api([
            'message' => "Company set.",
        ]);
    }

    public function unsetOutsourceCompany($outsourceCompanyId, $userId)
    {
        $user = User::findOrFail($userId);
        if ($outsourceCompanyId == 0) {
            // this company
            $user->works_here = 0;
            $user->save();
        } else {
            $user->outsourceCompanies()->detach($outsourceCompanyId);
        }
        return response()->api(['message' => "Company unset."]);
    }

    // Pay Levels --------------------
    public function getPayLevels($userId)
    {
        $u = User::findOrFail($userId);
        $cats = \App\PayCategory::orderBy('cname')->get();
        foreach ($cats as $cat) {
            $lvl = $u->payLevels()->where('pay_cat_id', $cat->id)->first();
            if ($lvl) {
                unset($lvl->pay_cat_id);
                unset($lvl->pivot);
            }
            $cat->pay_level = $lvl;
        }

        return response()->api($cats);
    }

    public function setPayLevel($userId, $payLevelId)
    {
        $user = User::findOrFail($userId);
        $plvl = \App\PayLevel::findOrFail($payLevelId);

        //ensure only one level per category can be set
        $lvls = $user->payLevels()->where('pay_cat_id', $plvl->pay_cat_id)->get();
        foreach ($lvls as $lvl) {
            $user->payLevels()->detach($lvl->id);
        }

        $user->payLevels()->syncWithoutDetaching([
            $payLevelId,
        ]);

        return response()->api([
            'message' => "Level set.",
        ]);
    }

    public function unsetPayLevel($userId, $payLevelId)
    {
        $user = User::findOrFail($userId);
        $user->payLevels()->detach($payLevelId);
        return response()->api([
            'message' => "Level unset.",
        ]);
    }

    // Ratings --------------------
    // 1 - 5 star rating - db stores as 1-10 so divide by 2
    public function getRatings($userId)
    {
        $rats = \App\Rating::orderBy('rname')->get();
        foreach ($rats as $rat) {
            $ur = $rat->users()->where('user_id', $userId)->first();
            if ($ur) {
                $rat->score = $ur->pivot->score / 2;
            } else {
                $rat->score = 0;
            }
        }
        return response()->api($rats);
    }

    public function setRating($userId, $ratingId, $score)
    {
        $user = User::findOrFail($userId);
        $score *= 2;
        if ($score > 10) {
            $score = 10;
        } elseif ($score < 1) {
            $score = 0;
        }
        if ($score) {
            $user->ratings()->syncWithoutDetaching([
                $ratingId => [
                    'score' => $score,
                ],
            ]);
        } else {
            $user->ratings()->detach($ratingId);
        }
        return response()->api([
            'message' => "Rating set.",
        ]);
    }

    // Work Areas --------------------
    public function getWorkAreas($userId)
    {
        $was = User::findOrFail($userId)->workAreas;
        return response()->api([
            'data' => $was,
        ]);
    }

    public function setWorkArea($userId, $workAreaId)
    {
        $user = User::findOrFail($userId);
        $user->workAreas()->syncWithoutDetaching([
            $workAreaId,
        ]);
        return response()->api([
            'message' => "Work area set.",
        ]);
    }

    public function unsetWorkArea($userId, $workAreaId)
    {
        $user = User::findOrFail($userId);
        $user->workAreas()->detach($workAreaId);
        return response()->api([
            'message' => "Work area unset.",
        ]);
    }
    // -----------------------------
}
