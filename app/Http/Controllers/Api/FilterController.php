<?php

namespace App\Http\Controllers\Api;

use App\Attribute;
use App\Client;
use App\Http\Controllers\Controller;
use App\Location;
use App\OutsourceCompany;
use App\ProfileElement;
use App\Shift;
use App\ShiftStatus;
use App\TrackingCategory;
use App\TrackingOption;
use App\User;
use App\WorkArea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    public function chatThreads($param = '')
    {
        $limit = 10;
        $omit_user_ids = [];

        $threads = Auth::user()->loggedInAs()->threads()
            ->select('threads.id', 'tname', 'participant_names');
        if (strlen($param)) {
            $threads = $threads->where(function ($query) use ($param) {
                $query->where('tname', 'like', $param . '%')
                    ->orWhere('participant_names', 'like', '%' . $param . '%');
            });
        }
        $threads = $threads->orderBy('threads.updated_at', 'desc')
            ->take($limit / 2)
            ->get();

        foreach ($threads as $thread) {
            if ($thread->tname == '') {
                $thread->name = str_ireplace(Auth::user()->loggedInAs()->name(), '', $thread->participant_names);
                $thread->name = ltrim($thread->name, ', ');
                $thread->name = str_ireplace(', ,', ', ', $thread->name);
                $thread->name = 'You, ' . substr($thread->name, 0, 40);
            } else {
                $$thread->name = $thread->tname;
            }

            //omit users from union below if thread is only between 2 people
            $participants = $thread->users()->select('users.id')->where('user_id', '!=', Auth::user()->loggedInAsId())->get();
            if ($participants->count() == 1) {
                $omit_user_ids[] = $participants[0]->id;
            }

            $thread->type = 'thread';
            unset($thread->tname);
            unset($thread->participant_names);
            unset($thread->pivot);
        };

        $num = $threads->count();

        $users = User::select('id', 'fname', 'lname')
            ->whereIn('lvl', ['owner', 'admin', 'staff'])
            ->where('active', 'active')
            ->whereNotIn('id', array_merge([Auth::user()->loggedInAsId()], $omit_user_ids));
        if (strlen($param)) {
            $users = $users->where('fname', 'like', $param . '%')
                ->orWhere('lname', 'like', $param . '%');
        }
        $users = $users->take($limit - $num)->get();

        $users = $users->map(function ($item, $key) {
            $item->name = $item->fname . ' ' . $item->lname;
            $item->type = 'user';
            unset($item->fname);
            unset($item->lname);
            return $item;
        });

        $combined = array_merge($threads->all(), $users->all());

        return response()->api($combined);
    }

    public function clients($param = '')
    {
        $cs = Client::select('id', 'cname');
        if (strlen($param)) {
            $cs = $cs->where('cname', 'like', $param . '%');
        }
        $cs = $cs->get();
        return response()->api($cs);
    }

    public function userTypes()
    {
        $data = [];

        $children = [];
        $children[] = ['id' => 'utype:=:incomplete', 'text' => 'Incomplete Registrants'];
        $children[] = ['id' => 'utype:=:complete', 'text' => 'Complete Registrants'];
        $children[] = ['id' => 'utype:=:rejected', 'text' => 'Rejected Registrants'];
        $children[] = ['id' => 'utype:=:client', 'text' => 'Clients'];
        $data[] = array('children' => $children, 'text' => 'External');

        $children = [];
        $children[] = ['id' => 'utype:=:all', 'text' => 'All Active Users'];
        $children[] = ['id' => 'utype:=:staff', 'text' => 'Staff'];
        $children[] = ['id' => 'utype:=:administrator', 'text' => 'Administrators'];
        $children[] = ['id' => 'utype:=:owner', 'text' => 'Owners'];
        $data[] = array('children' => $children, 'text' => 'Active');

        $children = [];
        $children[] = ['id' => 'active:=:inactive', 'text' => 'Inactive'];
        $children[] = ['id' => 'active:=:blacklisted', 'text' => 'Blacklisted'];
        $data[] = array('children' => $children, 'text' => 'Inactive');

        return response()->api($data);
    }

    public function roleRequirements($param = '')
    {
        $data = [];

        $dbParam = $param;
        $operator = '';
        $val = '';

        if ($operatorPos = \App\Helpers\Utilities::strposArr($param, ['<', '>', '='])) {
            $dbParam = substr($dbParam, 0, $operatorPos);
            $operator = $param[$operatorPos];
            $val = substr($param, ($operatorPos + 1));
        }

        if (@strstr('male', $param) || @strstr('female', $param) || $param == '') {
            $children = array();
            if (@strstr('male', $param) || $param == '') {
                $children[] = array('id' => 'sex:=:male', 'text' => 'Male');
            }
            if (@strstr('female', $param) || $param == '') {
                $children[] = array('id' => 'sex:=:female', 'text' => 'Female');
            }
            $data[] = array('children' => $children, 'text' => 'Sex');
        }

        if (@strstr('age>', $param) || @strstr('age<', $param) || $param == '') {
            $children = array();
            if (@strstr('age>', $param) || $param == '') {
                $children[] = array('id' => 'age:>:18', 'text' => 'Age > 18');
                $children[] = array('id' => 'age:>:19', 'text' => 'Age > 19');
                $children[] = array('id' => 'age:>:20', 'text' => 'Age > 20');
                $children[] = array('id' => 'age:>:21', 'text' => 'Age > 21');
                $children[] = array('id' => 'age:>:22', 'text' => 'Age > 22');
                $children[] = array('id' => 'age:>:23', 'text' => 'Age > 23');
                $children[] = array('id' => 'age:>:24', 'text' => 'Age > 24');
                $children[] = array('id' => 'age:>:25', 'text' => 'Age > 25');
                $children[] = array('id' => 'age:>:26', 'text' => 'Age > 26');
                $children[] = array('id' => 'age:>:27', 'text' => 'Age > 27');
                $children[] = array('id' => 'age:>:28', 'text' => 'Age > 28');
                $children[] = array('id' => 'age:>:29', 'text' => 'Age > 29');
                $children[] = array('id' => 'age:>:30', 'text' => 'Age > 30');
                $children[] = array('id' => 'age:>:31', 'text' => 'Age > 31');
                $children[] = array('id' => 'age:>:32', 'text' => 'Age > 32');
                $children[] = array('id' => 'age:>:33', 'text' => 'Age > 33');
                $children[] = array('id' => 'age:>:34', 'text' => 'Age > 34');
                $children[] = array('id' => 'age:>:35', 'text' => 'Age > 35');
                $children[] = array('id' => 'age:>:36', 'text' => 'Age > 36');
                $children[] = array('id' => 'age:>:37', 'text' => 'Age > 37');
                $children[] = array('id' => 'age:>:38', 'text' => 'Age > 38');
                $children[] = array('id' => 'age:>:39', 'text' => 'Age > 39');
                $children[] = array('id' => 'age:>:40', 'text' => 'Age > 40');
                $children[] = array('id' => 'age:>:41', 'text' => 'Age > 41');
                $children[] = array('id' => 'age:>:42', 'text' => 'Age > 42');
                $children[] = array('id' => 'age:>:43', 'text' => 'Age > 43');
                $children[] = array('id' => 'age:>:44', 'text' => 'Age > 44');
                $children[] = array('id' => 'age:>:45', 'text' => 'Age > 45');
                $children[] = array('id' => 'age:>:46', 'text' => 'Age > 46');
                $children[] = array('id' => 'age:>:47', 'text' => 'Age > 47');
                $children[] = array('id' => 'age:>:48', 'text' => 'Age > 48');
                $children[] = array('id' => 'age:>:49', 'text' => 'Age > 49');
                $children[] = array('id' => 'age:>:50', 'text' => 'Age > 50');
            }
            if (@strstr('age<', $param) || $param == '') {
                $children[] = array('id' => 'age:<:50', 'text' => 'Age < 50');
                $children[] = array('id' => 'age:<:49', 'text' => 'Age < 49');
                $children[] = array('id' => 'age:<:48', 'text' => 'Age < 48');
                $children[] = array('id' => 'age:<:47', 'text' => 'Age < 47');
                $children[] = array('id' => 'age:<:46', 'text' => 'Age < 46');
                $children[] = array('id' => 'age:<:45', 'text' => 'Age < 45');
                $children[] = array('id' => 'age:<:44', 'text' => 'Age < 44');
                $children[] = array('id' => 'age:<:43', 'text' => 'Age < 43');
                $children[] = array('id' => 'age:<:42', 'text' => 'Age < 42');
                $children[] = array('id' => 'age:<:41', 'text' => 'Age < 41');
                $children[] = array('id' => 'age:<:40', 'text' => 'Age < 40');
                $children[] = array('id' => 'age:<:39', 'text' => 'Age < 39');
                $children[] = array('id' => 'age:<:38', 'text' => 'Age < 38');
                $children[] = array('id' => 'age:<:37', 'text' => 'Age < 37');
                $children[] = array('id' => 'age:<:36', 'text' => 'Age < 36');
                $children[] = array('id' => 'age:<:35', 'text' => 'Age < 35');
                $children[] = array('id' => 'age:<:34', 'text' => 'Age < 34');
                $children[] = array('id' => 'age:<:33', 'text' => 'Age < 33');
                $children[] = array('id' => 'age:<:32', 'text' => 'Age < 32');
                $children[] = array('id' => 'age:<:31', 'text' => 'Age < 31');
                $children[] = array('id' => 'age:<:30', 'text' => 'Age < 30');
                $children[] = array('id' => 'age:<:29', 'text' => 'Age < 29');
                $children[] = array('id' => 'age:<:28', 'text' => 'Age < 28');
                $children[] = array('id' => 'age:<:27', 'text' => 'Age < 27');
                $children[] = array('id' => 'age:<:26', 'text' => 'Age < 26');
                $children[] = array('id' => 'age:<:25', 'text' => 'Age < 25');
                $children[] = array('id' => 'age:<:24', 'text' => 'Age < 24');
                $children[] = array('id' => 'age:<:23', 'text' => 'Age < 23');
                $children[] = array('id' => 'age:<:22', 'text' => 'Age < 22');
                $children[] = array('id' => 'age:<:21', 'text' => 'Age < 21');
                $children[] = array('id' => 'age:<:20', 'text' => 'Age < 20');
                $children[] = array('id' => 'age:<:19', 'text' => 'Age < 19');
                $children[] = array('id' => 'age:<:18', 'text' => 'Age < 18');
            }
            $data[] = array('children' => $children, 'text' => 'Age');
        }

        if (@strstr('performance', $param)) {
            $children = array();
            if (@strstr('performance>', $param) || $param == '') {
                $children[] = array('id' => 'rating:>:0', 'text' => 'Performance > 0');
                $children[] = array('id' => 'rating:>:1', 'text' => 'Performance > 1');
                $children[] = array('id' => 'rating:>:2', 'text' => 'Performance > 2');
                $children[] = array('id' => 'rating:>:3', 'text' => 'Performance > 3');
                $children[] = array('id' => 'rating:>:4', 'text' => 'Performance > 4');
                $children[] = array('id' => 'rating:>:5', 'text' => 'Performance > 5');
                $children[] = array('id' => 'rating:>:6', 'text' => 'Performance > 6');
                $children[] = array('id' => 'rating:>:7', 'text' => 'Performance > 7');
                $children[] = array('id' => 'rating:>:8', 'text' => 'Performance > 8');
                $children[] = array('id' => 'rating:>:9', 'text' => 'Performance > 9');
            }
            if (@strstr('performance<', $param) || $param == '') {
                $children[] = array('id' => 'rating:<:10', 'text' => 'Performance < 10');
                $children[] = array('id' => 'rating:<:9', 'text' => 'Performance < 9');
                $children[] = array('id' => 'rating:<:8', 'text' => 'Performance < 8');
                $children[] = array('id' => 'rating:<:7', 'text' => 'Performance < 7');
                $children[] = array('id' => 'rating:<:6', 'text' => 'Performance < 6');
                $children[] = array('id' => 'rating:<:5', 'text' => 'Performance < 5');
                $children[] = array('id' => 'rating:<:4', 'text' => 'Performance < 4');
                $children[] = array('id' => 'rating:<:3', 'text' => 'Performance < 3');
                $children[] = array('id' => 'rating:<:2', 'text' => 'Performance < 2');
                $children[] = array('id' => 'rating:<:1', 'text' => 'Performance < 1');
            }
            $data[] = array('children' => $children, 'text' => 'Performance');
        }

        //attributes
        $attrs = Attribute::select('id', 'aname')
            ->where('aname', 'like', "$param%")
            ->get();
        if ($attrs->count()) {
            $children = array();
            foreach ($attrs as $attr) {
                $children[] = array('id' => 'attr:=:' . $attr->id, 'text' => $attr->aname . ': Yes');
                $children[] = array('id' => 'attr:!=:' . $attr->id, 'text' => $attr->aname . ': No');
            }
            $data[] = array('children' => $children, 'text' => 'Attributes');
        }

        //work areas
        $wAs = WorkArea::select('id', 'aname')
            ->where('aname', 'like', "$param%")
            ->get();
        if ($wAs->count()) {
            $children = array();
            foreach ($wAs as $wA) {
                $children[] = array('id' => 'wa:=:' . $wA->id, 'text' => $wA->aname . ': Yes');
            }
            $data[] = array('children' => $children, 'text' => 'Work Areas');
        }

        // profile element range
        $pEs = ProfileElement::select('id', 'ename')
            ->where([['etype', 'list'], ['filter', 'range']])
            ->where("ename", 'like', "$dbParam%")
            ->with('profileListOptions')
            ->get();
        if ($pEs->count()) {
            foreach ($pEs as $pE) {
                $children = array();
                $lOs = $pE->profileListOptions()->orderBy('display_order')->get();
                foreach ($lOs as $lO) {
                    if ($operator == '' || $operator == '>') {
                        if ($val == '' || @strstr($lO->option, $val)) {
                            $children[] = array('id' => 'pl:' . $pE->id . ':>:' . $lO->display_order, 'text' => '> ' . $lO->option);
                        }

                    }
                }
                $lOs = $lOs->reverse();
                foreach ($lOs as $lO) {
                    if ($operator == '' || $operator == '<') {
                        if ($val == '' || @strstr($lO->option, $val)) {
                            $children[] = array('id' => 'pl:' . $pE->id . ':<:' . $lO->display_order, 'text' => '< ' . $lO->option);
                        }
                    }
                }
                $data[] = array('children' => $children, 'text' => $pE->ename);
            }
        }

        // profile element range
        $pEs = ProfileElement::select('id', 'ename')
            ->where([['etype', 'list'], ['filter', 'equals']])
            ->where("ename", 'like', "$dbParam%")
            ->with('profileListOptions')
            ->get();
        if ($pEs->count()) {
            foreach ($pEs as $pE) {
                $children = array();
                $lOs = $pE->profileListOptions()->orderBy('display_order')->get();
                foreach ($lOs as $lO) {
                    if ($operator == '' || $operator == '=') {
                        if ($val == '' || @strstr($lO->option, $val)) {
                            $children[] = array('id' => 'pl:' . $pE->id . ':=:' . $lO->id, 'text' => $lO->option);
                        }
                    }
                }
                $data[] = array('children' => $children, 'text' => $pE->ename);
            }
        }

/*
//payutypes
$q = "SELECT payutype_id,cname,pname,rate FROM pay_utype l JOIN pay_cat c ON (l.paycat_id=c.paycat_id) WHERE pname$qR ORDER BY cname,rate";
$result = mysqli_query($conn, $q);
if (mysqli_num_rows($result)) {
$children = array();
while ($row = mysqli_fetch_assoc($result)) {
$children[] = array('id' => 'p:' . $row['payutype_id'], 'text' => stripslashes($row['cname']) . ' - ' . stripslashes($row['pname'] . ' ' . $_SESSION['curs'] . $row['rate'] . '/hr'));
}
$data[] = array('children' => $children, 'text' => 'Pay Levels');
}

//Agencies
$q = "SELECT agency_id,aname FROM agency WHERE aname$qR";
$result = mysqli_query($conn, $q);
if (mysqli_num_rows($result)) {
$children = array();
while ($row = mysqli_fetch_assoc($result)) {
$children[] = array('id' => "agency:" . $row['agency_id'], 'text' => stripslashes($row['aname']));
}
$data[] = array('children' => $children, 'text' => 'Agencies');
}

//Ratings
$q = "SELECT rt_id,rname FROM rating WHERE rname$qR";
$result = mysqli_query($conn, $q);
if (mysqli_num_rows($result)) {
$children = array();
while ($row = mysqli_fetch_assoc($result)) {
for ($i = 0; $i <= 9; $i++) {
$children[] = array('id' => 'rate:' . $row['rt_id'] . ":" . ($i + 1), 'text' => stripslashes($row['rname']) . " > $i");
}
}
$data[] = array('children' => $children, 'text' => 'Ratings');
}

if (strlen($param) > 1 && SITE != 'dem') {
if (SITE == 'dem' || SITE == '360') {
$tbl == '';
} else {
$tbl = 'jjproj_sc_shared.';
}

$q = "SELECT ccode FROM settings";
$result = mysqli_query($conn, $q);
$row = mysqli_fetch_row($result);
$ccode = stripslashes($row[0]);

$q = "SELECT gl_id,formatted_address FROM " . $tbl . "geocodeLocs WHERE country_short='$ccode' AND formatted_address$qR";
$result = mysqli_query($conn, $q);
if (mysqli_num_rows($result)) {
$children = array();
while ($row = mysqli_fetch_row($result)) {
$children[] = array('id' => 'gl:' . $row[0] . ':5', 'text' => "Within 5 " . $_SESSION['dists'] . " of: " . stripslashes($row[1]));
$children[] = array('id' => 'gl:' . $row[0] . ':10', 'text' => "Within 10 " . $_SESSION['dists'] . " of: " . stripslashes($row[1]));
$children[] = array('id' => 'gl:' . $row[0] . ':20', 'text' => "Within 20 " . $_SESSION['dists'] . " of: " . stripslashes($row[1]));
$children[] = array('id' => 'gl:' . $row[0] . ':30', 'text' => "Within 30 " . $_SESSION['dists'] . " of: " . stripslashes($row[1]));
$children[] = array('id' => 'gl:' . $row[0] . ':50', 'text' => "Within 50 " . $_SESSION['dists'] . " of: " . stripslashes($row[1]));
$children[] = array('id' => 'gl:' . $row[0] . ':100', 'text' => "Within 100 " . $_SESSION['dists'] . " of: " . stripslashes($row[1]));
}
$data[] = array('children' => $children, 'text' => 'Location');
}
}
 */
        return response()->api($data);
    }

    public function shifts($from, $to, $param = '')
    {
        $data = [];

        $from = date('Y-m-d', strtotime($from));
        $to = date('Y-m-d', strtotime($to));
        //validate dates?

        if (@strstr('deleted', $param) || $param == '') {
            $data[] = array('id' => 'deleted', 'text' => 'Deleted');
        }

        //TODO if mod work areas
        if (@strstr('no work area', $param) || $param == '') {
            $data[] = array('id' => 'noWorkArea', 'text' => 'No Work Area Assigned');
        }

        if (strlen($param)) {

            //shift status
            $stuff = ShiftStatus::select('id', 'status')
                ->where('status', 'like', $param . '%')
                ->get();
            if ($stuff->count()) {
                $children = array();
                foreach ($stuff as $s) {
                    $children[] = array('id' => 'shift_status_id:=:' . $s->id, 'text' => $s->status);
                    $children[] = array('id' => 'shift_status_id:!=:' . $s->id, 'text' => 'Not ' . $s->status);
                }
                $data[] = array('children' => $children, 'text' => 'Status');
            }

            //location
            if (strlen($param) > 2) {
                $stuff = Shift::select('location')
                    ->whereBetween('shift_start', [$from, $to])
                    ->where('location', 'like', $param . '%')
                    ->distinct()
                    ->get();
                if ($stuff->count()) {
                    $children = array();
                    foreach ($stuff as $s) {
                        $children[] = array('id' => 'location:=:' . $s->location, 'text' => $s->location);
                    }
                    $data[] = array('children' => $children, 'text' => 'Location');
                }
            }

            //work area
            if (strlen($param) > 2) {
                $stuff = WorkArea::select('id', 'aname')
                    ->where('aname', 'like', "$param%")
                    ->get();
                if ($stuff->count()) {
                    $children = array();
                    foreach ($stuff as $s) {
                        $children[] = array('id' => 'wa:=:' . $s->id, 'text' => $s->aname);
                        $children[] = array('id' => 'wa:!=:' . $s->id, 'text' => 'Not ' . $s->aname);
                    }
                    $data[] = array('children' => $children, 'text' => 'Work Area');
                }
            }

            //tracking
            if (strlen($param) > 2) {
                $tracks = TrackingCategory::select('id', 'cname')->get();
                foreach ($tracks as $t) {
                    $stuff = TrackingOption::select('id', 'oname')
                        ->where('tracking_cat_id', $t->id)
                        ->where('oname', 'like', "$param%")
                        ->get();
                    if ($stuff->count()) {
                        $children = array();
                        foreach ($stuff as $s) {
                            $children[] = array('id' => 'tracko:=:' . $s->id, 'text' => $s->oname);
                            $children[] = array('id' => 'tracko:!=:' . $s->id, 'text' => 'Not ' . $s->oname);
                        }
                        $data[] = array('children' => $children, 'text' => $t->cname);
                    }
                }
            }

            //clients
            if (strlen($param) > 2) {
                $stuff = Client::select('id', 'cname')
                    ->where('cname', 'like', "$param%")
                    ->get();
                if ($stuff->count()) {
                    $children = array();
                    foreach ($stuff as $s) {
                        $children[] = array('id' => 'client_id:=:' . $s->id, 'text' => $s->cname);
                        $children[] = array('id' => 'client_id:!=:' . $s->id, 'text' => 'Not ' . $s->cname);
                    }
                    $data[] = array('children' => $children, 'text' => 'Client');
                }
            }

            //manager
            if (strlen($param) > 2) {
                $stuff = User::select('id', DB::raw("CONCAT(fname,' ',lname) as name"))
                    ->where('active', 'active')
                    ->whereIn('lvl', ['owner', 'admin'])
                    ->whereRaw("CONCAT(fname,' ',lname) LIKE '%$param%'")
                    ->get();
                if ($stuff->count()) {
                    $children = array();
                    foreach ($stuff as $s) {
                        $children[] = array('id' => 'man:=:' . $s->id, 'text' => $s->name);
                        $children[] = array('id' => 'man:!=:' . $s->id, 'text' => 'Not ' . $s->name);
                    }
                    $data[] = array('children' => $children, 'text' => 'Manager');
                }
            }

            //outsource company
            if (strlen($param) > 2) {
                $stuff = OutsourceCompany::select('id', 'cname')
                    ->where('cname', 'like', "$param%")
                    ->get();
                if ($stuff->count()) {
                    $children = array();
                    foreach ($stuff as $s) {
                        $children[] = array('id' => 'outsource_company_id:=:' . $s->id, 'text' => $s->cname);
                        $children[] = array('id' => 'outsource_company_id:!=:' . $s->id, 'text' => 'Not ' . $s->cname);
                    }
                    $data[] = array('children' => $children, 'text' => 'Outsource Company');
                }
            }

            //selected staff
            if (strlen($param) > 2) {
                $stuff = User::select('id', DB::raw("CONCAT(fname,' ',lname) as name"))
                    ->where('active', 'active')
                    ->whereIn('lvl', ['admin', 'staff'])
                    ->whereRaw("CONCAT(fname,' ',lname) LIKE '%$param%'")
                    ->get();
                if ($stuff->count()) {
                    $children = array();
                    foreach ($stuff as $s) {
                        $children[] = array('id' => 'selected:=:' . $s->id, 'text' => $s->name);
                    }
                    $data[] = array('children' => $children, 'text' => 'Selected');
                }
            }
        }

        return response()->api($data);
    }

    public function users($param = '')
    {
        $data = [];

        $dbParam = $param;
        $operator = '';
        $val = '';

        if (substr($param, 0, 5) == 'near:') {

            $arr = explode(':', $param);
            $addr = $arr[1];
            if (isset($arr[2]) && is_numeric($arr[2])) {
                $radius = $arr[2];
            } else {
                $radius = 100;
            }

            if (strlen($addr) > 3) {

                $children = \App\Helpers\Utilities::geoSearchForFilter($addr, $radius);
                $data[] = array('children' => $children, 'text' => 'Geographic search');
            }

        } else {

            if ($operatorPos = \App\Helpers\Utilities::strposArr($param, ['<', '>', '='])) {
                $dbParam = substr($dbParam, 0, $operatorPos);
                $operator = $param[$operatorPos];
                $val = substr($param, ($operatorPos + 1));
            }

            if (@strstr('videos', $param)) {
                $data[] = array('id' => 'videos', 'text' => 'Has videos');
            }

            if (@strstr('new', $param) || @strstr('existing', $param) || @strstr('interviewed', $param) || $param == '') {
                $children = array();
                if (@strstr('new', $param) || $param == '') {
                    $children[] = array('id' => 'ustat:=:New', 'text' => 'New');
                }
                if (@strstr('existing', $param) || $param == '') {
                    $children[] = array('id' => 'ustat:=:Existing', 'text' => 'Existing');
                }
                if (@strstr('interviewed', $param) || $param == '') {
                    $children[] = array('id' => 'ustat:=:Interviewed', 'text' => 'Interviewed');
                }
                $data[] = array('children' => $children, 'text' => 'Status');
            }

            $more = json_decode(self::roleRequirements($param)->content());
            if (count($more)) {
                $data = array_merge($more, $data);
            }
        }

        return response()->api($data);
    }

    public function locations($param = '')
    {
        $locs = Location::select('id', 'lname', 'generic_lname', 'address');
        if (strlen($param)) {
            $locs = $locs->where('lname', 'like', $param . '%');
        }
        $locs = $locs->get();
        return response()->api($locs);
    }

    public function managers($param = '')
    {
        $users = User::select('id', 'fname', 'lname')->whereIn('lvl', ['owner', 'admin']);
        if (strlen($param)) {
            $users = $users->where('fname', 'like', $param . '%')
                ->orWhere('lname', 'like', $param . '%');
        }
        $users = $users->get();
        $users = $users->map(function ($item, $key) {
            $item->name = $item->fname . ' ' . $item->lname;
            unset($item->fname);
            unset($item->lname);
            return $item;
        });
        return response()->api($users);
    }

    //TODO limit based on permissions?
    public function trackingOptions($catId, $param = '')
    {
        $opts = TrackingOption::select('id', 'oname')
            ->where('tracking_cat_id', $catId);
        if (strlen($param)) {
            $opts = $opts->where('oname', 'like', $param . '%');
        }
        $opts = $opts->get();
        return response()->api($opts);
    }

    public function workAreas($param = '')
    {
        $was = WorkArea::select('id', 'aname');
        if (strlen($param)) {
            $was = $was->where('aname', 'like', $param . '%');
        }
        $was = $was->get();
        return response()->api($was);
    }
}
