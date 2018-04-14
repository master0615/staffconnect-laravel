<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shift;
use App\ShiftAdminNote;
use App\ShiftAdminNoteType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftAdminNoteController extends Controller
{
    // POST shift/{id}/adminNote
    public function create(Request $request, $id)
    {
        $s = Shift::findOrFail($id);

        $request->validate([
            'note' => 'required|string|min:1',
            'type_id' => 'sometimes|nullable|numeric|exists:tenant.shift_admin_note_types,id',
            'client_visible' => 'sometimes|in:0,1',
        ]);

        $n = new ShiftAdminNote;
        $n->shift_id = $id;
        $n->note = $request->note;
        $n->type_id = $request->input('type_id', null);
        $n->client_visible = $request->input('client_visible', 0);
        $n->creator_id = Auth::user()->loggedInAsId();
        $n->save();

        return response()->api([
            'data' => $n,
            'message' => "Saved.",
        ], 201);
    }

    // PUT shift/adminNote/{id}
    public function update(Request $request, $id)
    {
        $n = ShiftAdminNote::findOrFail($id);
        $shiftId = $n->shift->id;
        if ($n->creator_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        $request->validate([
            'note' => 'sometimes|string|min:1',
            'type_id' => 'sometimes|nullable|numeric|exists:tenant.shift_admin_note_types,id',
            'client_visible' => 'sometimes|in:0,1',
        ]);

        if ($request->has('note')) {
            $n->note = $request->note;
        }
        if ($request->has('type_id')) {
            $n->type_id = $request->type_id;
        }
        if ($request->has('client_visible')) {
            $n->client_visible = $request->client_visible;
        }
        $n->save();

        unset($n->shift);

        return response()->api([
            'data' => $n,
            'message' => "Saved.",
        ], 201);
    }

    // DELETE shift/adminNote/{id}
    public function delete($id)
    {
        $n = ShiftAdminNote::findOrFail($id);
        $shiftId = $n->shift->id;
        if ($n->creator_id != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        $n->delete();

        return response()->api([
            'message' => "Deleted.",
        ]);
    }

    // GET shift/{id}/adminNote
    public function get($id)
    {
        $ns = ShiftAdminNote::with('type')->where('shift_id', $id)->orderBy('created_at', 'desc')->get();

        foreach ($ns as $n) {
            $n->creator_ppic_a = $n->creator->tthumb();
            $n->creator_name = $n->creator->name();
            unset($n->creator);

            if ($n->type) {
                $n->tname = $n->type->tname;
                $n->color = $n->type->color;
            } else {
                $n->tname = 'default';
                $n->color = '#ffffff';
            }
            unset($n->type);
            unset($n->shift_id);
        }

        return response()->api($ns);
    }

    //note types
    /**
     * POST /shiftAdminNoteType
     */
    public function createType(Request $request)
    {
        $request->validate([
            'tname' => 'required|min:1|max:20|unique:tenant.shift_admin_note_types',
            'color' => 'required|string',
        ], [
            'tname.required' => "Please enter a name for the note type.",
            'tname.unique' => "A note type with the same name aleady exists.",
        ]);

        $t = new ShiftAdminNoteType();
        $t->tname = $request->tname;
        $t->color = $request->color;
        $t->save();

        return response()->api([
            'data' => $t,
            'message' => "Note type saved.",
        ], 201);
    }

    /**
     * DELETE /shiftAdminNoteType/{id}
     */
    public function deleteType($id)
    {
        ShiftAdminNoteType::destroy($id);
        return response()->api([
            'message' => "Note type deleted.",
        ]);
    }

    /**
     * GET /shiftAdminNoteType/{id?}
     */
    public function getType($id = false)
    {
        if ($id) {
            $t = ShiftAdminNoteType::findOrFail($id);
        } else {
            $t = ShiftAdminNoteType::orderBy('tname')->get();
        }

        return response()->api($t);
    }

    /**
     * PUT /shiftAdminNoteType/{id}
     */
    public function updateType(Request $request, $id)
    {
        $t = ShiftAdminNoteType::findOrFail($id);

        $request->validate([
            'tname' => "sometimes|min:1|max:20|unique:tenant.shift_admin_note_types,tname,$id,id",
            'color' => 'sometimes|string',
        ], [
            'tname.unique' => "A note type with the same name aleady exists.",
        ]);

        if ($request->has('tname')) {
            $t->tname = $request->tname;
        }
        if ($request->has('color')) {
            $t->color = $request->color;
        }
        $t->save();

        return response()->api([
            'data' => $t,
            'message' => "Note type saved.",
        ]);
    }
}
