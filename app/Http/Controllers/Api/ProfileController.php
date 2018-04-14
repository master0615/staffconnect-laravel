<?php
namespace App\Http\Controllers\Api;

use App\Attribute;
use App\AttributeCategory;
use App\ProfileCategory;
use App\ProfileData;
use App\ProfileElement;
use App\ProfileListOption;
use App\User;
use App\WorkArea;
use App\WorkAreaCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// the following constants are stored in user table, whereas custom elements are stored in profile_data
// TODO - previously ID2 was in user table and was used for workmarket and also xero user_id link
define('PROFILE_ELEMENT_FNAME', '1');
define('PROFILE_ELEMENT_LNAME', '2');
define('PROFILE_ELEMENT_ALIAS', '22');
define('PROFILE_ELEMENT_DOB', '3');
define('PROFILE_ELEMENT_SEX', '4');
define('PROFILE_ELEMENT_EMAIL', '5');
define('PROFILE_ELEMENT_AGE', '6');
define('PROFILE_ELEMENT_MOB', '7');
define('PROFILE_ELEMENT_ADDRESS', '8');
define('PROFILE_ELEMENT_UNIT', '9');
define('PROFILE_ELEMENT_CITY', '10');
define('PROFILE_ELEMENT_STATE', '11');
define('PROFILE_ELEMENT_POSTCODE', '12');
define('PROFILE_ELEMENT_ID', '111');
define('PROFILE_ELEMENT_STATUS', '110');
define('PROFILE_ELEMENT_PERFORMANCE', '109');
define('PROFILE_ELEMENT_ID2', '108');

class ProfileController extends Controller
{

    private function profileElementToUserMap($profileElementId)
    {
        $column_name = '';
        switch ($profileElementId) {
            case PROFILE_ELEMENT_FNAME:
                $column_name = 'fname';
                break;
            case PROFILE_ELEMENT_LNAME:
                $column_name = 'lname';
                break;
            case PROFILE_ELEMENT_ALIAS:
                $column_name = 'alias';
                break;
            case PROFILE_ELEMENT_DOB:
                $column_name = 'dob';
                break;
            case PROFILE_ELEMENT_SEX:
                $column_name = 'sex';
                break;
            case PROFILE_ELEMENT_EMAIL:
                $column_name = 'email';
                break;
            case PROFILE_ELEMENT_AGE:
                $column_name = 'dob'; // TODO
                break;
            case PROFILE_ELEMENT_MOB:
                $column_name = 'mob';
                break;
            case PROFILE_ELEMENT_ADDRESS:
                $column_name = 'address';
                break;
            case PROFILE_ELEMENT_UNIT:
                $column_name = 'unit';
                break;
            case PROFILE_ELEMENT_CITY:
                $column_name = 'city';
                break;
            case PROFILE_ELEMENT_STATE:
                $column_name = 'state';
                break;
            case PROFILE_ELEMENT_POSTCODE:
                $column_name = 'postcode';
                break;
            case PROFILE_ELEMENT_ID:
                $column_name = 'id';
                break;
            case PROFILE_ELEMENT_STATUS:
                $column_name = 'status';
                break;
            case PROFILE_ELEMENT_PERFORMANCE:
                $column_name = 'id'; // TODO
                break;
            case PROFILE_ELEMENT_ID2:
                $column_name = 'id'; // combine elements and categories with same parent but cannot user merge() as it overwrites with same id
                break;
        }

        return $column_name;
    }

    /**
     * GET /profile/structure
     */
    public function getStructure($profile_cat_id = null)
    {
        $structure = $this->getElements($profile_cat_id, false);

        return response()->api($structure);
    }

    private function getElements($profile_cat_id = null, $user = false)
    {
        $structure = ProfileCategory::where('profile_cat_id', $profile_cat_id)
            ->orderBy('display_order')
            ->get();

        foreach ($structure as $i => $category) {

            $elems = collect();
            $elems_elems = ProfileElement::where('profile_cat_id', $category->id)
                ->orderBy('display_order')
                ->get();
            $elems_cats = $this->getElements($category->id, $user);
            foreach ($elems_elems as $elem) {
                if ($elem->etype == 'list' || $elem->etype == 'listm') {
                    if ($elem->id == PROFILE_ELEMENT_SEX) {
                        $elem['options'] = array(
                            array(
                                'id' => 'female',
                                'option' => 'female',
                            ),
                            array(
                                'id' => 'male',
                                'option' => 'male',
                            ),
                        );
                    } else {
                        $elem['options'] = ProfileListOption::select('id', 'option')->where('profile_element_id', $elem->id)->orderBy('display_order')->get();
                    }
                }
                if ($user) {
                    unset($elem['deletable']);
                    unset($elem['editable']);
                    unset($elem['filter']);
                    unset($elem['profile_cat_id']);
                    unset($elem['display_order']);
                    $data = '';
                    $user_column = $this->profileElementToUserMap($elem->id);
                    if ($user_column == '') {
                        if ($q = ProfileData::select('data')->where([
                            [
                                'user_id',
                                $user->id,
                            ],
                            [
                                'profile_element_id',
                                $elem->id,
                            ],
                        ])->first()) {
                            $data = $q->data;
                        }
                    } else {
                        $data = $user[$user_column] ?? '';
                        if ($data != '' && $elem->id == PROFILE_ELEMENT_DOB) {
                            $data = $data->toDateString();
                        } elseif ($elem->id == PROFILE_ELEMENT_AGE) {
                            $data = $user->age();
                        }
                    }
                    $elem->data = $data;
                }
                $elems->push($elem);
            }
            foreach ($elems_cats as $elem) {
                if ($user) {
                    unset($elem['deletable']);
                    unset($elem['profile_cat_id']);
                    unset($elem['display_order']);
                }
                $elems->push($elem);
            }
            if ($elems->isNotEmpty()) {
                $elems = $elems->sortBy('display_order');
                $elems = array_values($elems->toArray());
                $category['elements'] = $elems;
            } elseif ($user) {
                unset($structure[$i]);
            }
        }
        $structure = $structure->flatten();

        return $structure;
    }

    /**
     * GET /profile/{id}/attributes
     */
    public function getAttributes($userId)
    {
        $u = User::findOrFail($userId);
        $uAttrs = $u->attributes()->get()->pluck('id')->all();

        $arr = [];
        //unassigned
        if (Auth::user()->loggedInAs()->hasRole('owner|admin')) {
            $attrs = Attribute::select('id', 'aname')
                ->where('attribute_cat_id', null)
                ->orderBy('display_order')
                ->get();
        } else {
            $attrs = Attribute::select('id', 'aname')
                ->where('attribute_cat_id', null)
                ->where('visibility', 'staff')
                ->orderBy('display_order')
                ->get();
        }
        if ($attrs->count()) {
            $cat = [
                'id' => '0',
                'cname' => '',
                'attributes' => $attrs,
            ];
            $arr[] = $cat;
        }

        $cats = AttributeCategory::select('id', 'cname');

        if (Auth::user()->loggedInAs()->hasRole('owner|admin')) {
            $cats = $cats->with('attributes:id,aname,attribute_cat_id')
                ->has('attributes');

        } else {
            $cats = $cats->with(['attributes' => function ($query) {
                $query->where('visibility', 'staff');
            }])
                ->whereHas('attributes', function ($query) {
                    $query->where('visibility', 'staff');
                });
        }
        $cats = $cats->orderBy('display_order')
            ->get();

        foreach ($cats as $cat) {
            $arr[] = $cat;
        }

        foreach ($arr as $cat) {
            foreach ($cat['attributes'] as $attr) {
                if (in_array($attr->id, $uAttrs)) {
                    $attr->set = 1;
                } else {
                    $attr->set = 0;
                }
                unset($attr->visibility);
                unset($attr->role_default);
                unset($attr->display_order);
                unset($attr->attribute_cat_id);
            }
        }

        return response()->api($arr);
    }

    /**
     * PUT /profile/{id}/attribute
     */
    public function setAttribute(Request $request, $userId)
    {
        $u = User::findOrFail($userId);

        $request->validate([
            'attribute_id' => "required|numeric|exists:tenant.attributes,id",
            'set' => 'required|in:1,0',
        ]);

        if ($request->set == 1) {
            $u->attributes()->syncWithoutDetaching([$request->attribute_id => [
                'created_at' => date('Y-m-d H:i:s'),
                'setter_id' => Auth::id(),
            ]]);
        } else {
            $u->attributes()->detach($request->attribute_id);
        }

        return response()->api([
            'message' => 'Saved.',
        ]);
    }

    /**
     * GET /profile/{id}/workAreas
     */
    public function getWorkAreas($userId)
    {
        $u = User::findOrFail($userId);
        $uWAs = $u->workAreas()->get()->pluck('id')->all();

        $arr = [];
        //unassigned
        $wAs = WorkArea::select('id', 'aname')
            ->where('work_area_cat_id', null)
            ->get();
        if ($wAs->count()) {
            $cat = [
                'id' => '0',
                'cname' => '',
                'work_areas' => $wAs,
            ];
            $arr[] = $cat;
        }

        $cats = WorkAreaCategory::select('id', 'cname')
            ->with('workAreas:id,aname,work_area_cat_id')
            ->has('workAreas')
            ->get();

        foreach ($cats as $cat) {
            $arr[] = $cat;
        }

        foreach ($arr as $cat) {
            foreach ($cat['workAreas'] as $wA) {
                if (in_array($wA->id, $uWAs)) {
                    $wA->set = 1;
                } else {
                    $wA->set = 0;
                }
                unset($wA->work_area_cat_id);
            }
        }

        return response()->api($arr);
    }

    /**
     * PUT /profile/{id}/workArea
     */
    public function setWorkArea(Request $request, $userId)
    {
        $u = User::findOrFail($userId);

        $request->validate([
            'work_area_id' => "required|numeric|exists:tenant.work_areas,id",
            'set' => 'required|in:1,0',
        ]);

        if ($request->set == 1) {
            $u->workAreas()->syncWithoutDetaching($request->work_area_id);
        } else {
            $u->workAreas()->detach($request->work_area_id);
        }

        return response()->api([
            'message' => 'Saved.',
        ]);
    }

    /**
     * GET /profile/{id}
     */
    public function getProfile($userId)
    {
        $user = User::findOrFail($userId);

        $profile = $user;
        $profile->ppic_a = $user->tthumb();

        if (Auth::user()->loggedInAs()->canViewProfile($userId)) {

            $profile->age = $user->age();

            switch ($user->lvl) {
                case 'staff':
                case 'registrant1':
                case 'registrant2':
                case 'registrant3':
                case 'registrant4':
                case 'registrant5':
                case 'registrant6':
                case 'registrant7':
                case 'registrant8':
                    $profile->data = $this->getElements(null, $user);
                    break;
                default:
                    $profile = $profile->makeHidden(['fav', 'ustat', 'works_here']);
            }

        } else {
            //what to display?? just pic? set in settings?

            $profile = $profile->makeHidden(['fname', 'lname', 'email', 'mob', 'dob', 'sex', 'alias', 'address', 'unit', 'city', 'state', 'postcode', 'lvl', 'last_login', 'active', 'created_at', 'updated_at', 'fav', 'ustat', 'works_here']);
        }

        if (Auth::user()->loggedInAs()->hasRole('owner|admin')) {
            $profile->profile_admin_notes = $user->profileAdminNotes;
            foreach ($profile->profile_admin_notes as $n) {
                $n->creator_thumbnail = $n->creator->tthumb();
                $n->creator_name = $n->creator->fname . ' ' . $n->creator_lname;
                unset($n->creator);
            }
        }

        return response()->api($profile);
    }

    /**
     * GET /profile/{userId}/photos
     */
    public function getPhotos($userId)
    {
        $user = User::findOrFail($userId);

        if (Auth::user()->loggedInAs()->canViewProfile($userId)) {
            if (Auth::user()->loggedInAs()->hasRole('owner|admin')) {
                $photos = $user->profilePhotos()->select('id', 'ext', 'main', 'locked', 'admin_only', 'created_at')->orderBy('display_order')->get();
            } else {
                $photos = $user->profilePhotos()->select('id', 'ext', 'main', 'locked', 'admin_only', 'created_at')->where('admin_only', 0)->orderBy('display_order')->get();
            }

            foreach ($photos as $photo) {
                $photo->path = $photo->path();
                $photo->thumbnail = $photo->thumbnail();
            }

        } else {
            throw new \App\Exceptions\NotAllowedException();
        }

        return response()->api($photos);
    }

    /**
     * GET /profile/{userId}/videos
     */
    public function getVideos($userId)
    {
        $user = User::findOrFail($userId);

        if (Auth::user()->loggedInAs()->canViewProfile($userId)) {
            if (Auth::user()->loggedInAs()->hasRole('owner|admin')) {
                $videos = $user->profileVideos()->select('id', 'ext', 'locked', 'admin_only', 'created_at')->orderBy('display_order')->get();
            } else {
                $videos = $user->profileVideos()->select('id', 'ext', 'locked', 'admin_only', 'created_at')->where('admin_only', 0)->orderBy('display_order')->get();
            }

            foreach ($videos as $video) {
                $video->path = $video->path();
                $video->thumbnail = $video->thumbnail();
            }

        } else {
            throw new \App\Exceptions\NotAllowedException();
        }

        return response()->api($videos);
    }

    /**
     * GET /profile/{userId}/documents
     */
    public function getDocuments($userId)
    {
        $user = User::findOrFail($userId);

        if (Auth::user()->loggedInAs()->canViewProfile($userId)) {
            if (Auth::user()->loggedInAs()->hasRole('owner|admin')) {
                $documents = $user->profileDocuments()->select('id', 'ext', 'oname', 'locked', 'admin_only', 'created_at')->orderBy('display_order')->get();
            } else {
                $documents = $user->profileDocuments()->select('id', 'ext', 'oname', 'locked', 'admin_only', 'created_at')->where('admin_only', 0)->orderBy('display_order')->get();
            }

            foreach ($documents as $document) {
                $document->path = $document->path();
                $document->thumbnail = $document->thumbnail();
            }

        } else {
            throw new \App\Exceptions\NotAllowedException();
        }

        return response()->api($documents);
    }

    /**
     * PUT /profile/{userId}/{profileElementId}
     */
    public function update(Request $request, $userId, $profileElementId)
    {
        $user = User::findOrFail($userId);
        //TODO check allowed

        if (!is_numeric($profileElementId)) {
            //allow fname, lname etc
            $user_column = strtolower($profileElementId);
            if (defined('PROFILE_ELEMENT_' . strtoupper($user_column))) {
                $profileElementId = constant('PROFILE_ELEMENT_' . strtoupper($user_column));

            } else {
                throw new \App\Exceptions\NotAllowedException;
            }
        } else {
            $user_column = $this->profileElementToUserMap($profileElementId);
        }

        if ($user_column == '') {

            $request->validate([
                'data' => 'required|nullable',
            ]);

            $profileData = ProfileData::updateOrCreate([
                'user_id' => $userId,
                'profile_element_id' => $profileElementId,
            ], [
                'data' => $request->data,
            ]);

        } else {

            switch ($profileElementId) {

                case PROFILE_ELEMENT_FNAME:
                    $request->validate([
                        'data' => 'required|string|alpha|min:1|max:20',
                    ], [
                        'data.min' => "Your first name has to be at least one characater long.",
                    ]);
                    $request->data = title_case($request->data);
                    break;

                case PROFILE_ELEMENT_LNAME:
                    $request->validate([
                        'data' => 'required|string|alpha|min:1|max:20',
                    ], [
                        'data.min' => "Your last name has to be at least one characater long.",
                    ]);
                    $request->data = title_case($request->data);
                    break;

                case PROFILE_ELEMENT_ALIAS:
                    $request->validate([
                        'data' => 'required|string|max:20',
                    ]);
                    break;

                case PROFILE_ELEMENT_DOB:
                    $request->validate([
                        'data' => 'required|date|before:' . date('Y-m-d', strtotime("last month")) . '|after:' . date('Y-m-d', strtotime("- 120 years")),
                    ]);
                    break;

                case PROFILE_ELEMENT_SEX:
                    $request->validate([
                        'data' => 'required|in:female,male',
                    ], [
                        'data.in' => "Please specify your sex as female or male.",
                    ]);
                    $request->data = strtolower($request->data);
                    break;

                case PROFILE_ELEMENT_EMAIL:
                    $request->validate([
                        'data' => 'required|email|unique:tenant.users,email,' . $userId . ',',
                    ], [
                        'data.email' => "Please enter a valid email address.",
                        'data.unique' => "The email address is already registered on the system.",
                    ]);
                    $request->data = strtolower($request->data);
                    break;

                case PROFILE_ELEMENT_MOB:
                    $request->validate([
                        'data' => 'required|numeric|digits_between:8,12',
                    ], [
                        'data.numeric' => "Please specify a valid mobile phone number without any special characters.",
                    ]);
                    break;

                case PROFILE_ELEMENT_ADDRESS:
                    $request->validate([
                        'data' => 'required|string|min:5|max:40',
                    ], [
                        'data.min' => "Your address has to be at least 5 characaters long.",
                    ]);
                    $request->data = title_case($request->data);
                    break;

                case PROFILE_ELEMENT_UNIT:
                    $request->validate([
                        'data' => 'required|string|max:10',
                    ]);
                    break;

                case PROFILE_ELEMENT_CITY:
                    $request->validate([
                        'data' => 'required|string|min:1|max:20',
                    ]);
                    $request->data = title_case($request->data);
                    break;

                case PROFILE_ELEMENT_STATE:
                    $request->validate([
                        'data' => 'required|string|max:20',
                    ]);
                    $request->data = title_case($request->data);
                    break;

                case PROFILE_ELEMENT_POSTCODE:
                    $request->validate([
                        'data' => 'required|string|max:10',
                    ]);
                    $request->data = strtoupper($request->data);
                    break;
            }

            $user->$user_column = $request->data;
            $user->save();
        }

        return response()->api([
            'data' => $request->data,
            'message' => "Saved.",
        ]);
    }
}
