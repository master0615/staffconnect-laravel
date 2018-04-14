<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\ProfileAdminNote;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileAdminNoteController extends Controller
{
    // POST profile/{id}/adminNote
    public function create(Request $request, $id)
    {
        $u = User::findOrFail($id);
        if (!Auth::user()->loggedInAs()->canEditProfile($id)) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        $request->validate([
            'note' => 'required|string|min:1',
            'type' => 'required|in:info,interview,positive,negative',
        ]);

        $n = new ProfileAdminNote;
        $n->user_id = $id;
        $n->note = $request->note;
        $n->type = $request->type;
        $n->creator_id = Auth::user()->loggedInAsId();
        $n->save();

        return response()->api([
            'data' => $n,
            'message' => "Saved.",
        ], 201);
    }

    // PUT profile/adminNote/{id}
    public function update(Request $request, $id)
    {
        $n = ProfileAdminNote::findOrFail($id);
        $userId = $n->user->id;
        if (!Auth::user()->loggedInAs()->canEditProfile($userId)) {
            throw new \App\Exceptions\UnauthorisedException();
        }
        if ($n->creator_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        $request->validate([
            'note' => 'sometimes|string|min:1',
            'type' => 'sometimes|in:info,interview,positive,negative',
        ]);

        $n->note = $request->input('note', $n->note);
        $n->type = $request->input('type', $n->type);
        $n->save();

        unset($n->user);

        return response()->api([
            'data' => $n,
            'message' => "Saved.",
        ], 201);
    }

    // DELETE profile/adminNote/{id}
    public function delete($id)
    {
        $n = ProfileAdminNote::findOrFail($id);
        $userId = $n->user->id;
        if (!Auth::user()->loggedInAs()->canEditProfile($userId)) {
            throw new \App\Exceptions\UnauthorisedException();
        }
        if ($n->creator_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        $n->delete();

        return response()->api([
            'message' => "Deleted.",
        ]);
    }

    // GET profile/{id}/adminNote
    public function get($id)
    {
        $u = User::findOrFail($id);
        if (!Auth::user()->loggedInAs()->canViewProfile($id)) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        $ns = ProfileAdminNote::where('user_id', $id)->orderBy('created_at', 'desc')->get();

        foreach ($ns as $n) {
            $n->creator_ppic_a = $n->creator->tthumb();
            $n->creator_name = $n->creator->name();
            unset($n->creator);
            unset($n->user_id);
        }

        return response()->api($ns);
    }
}
