<?php

namespace App\Http\Controllers\Api;

use App\Flag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FlagController extends Controller
{
    /**
     * POST /flag
     */
    public function create(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|min:1|max:20|unique:tenant.flags',
            'color' => 'required|string|min:1|max:10|unique:tenant.flags',
        ], [
            'fname.required' => "Please enter a name for the flag.",
            'fname.unique' => "A flag with the same name aleady exists.",
            'color.unique' => "A flag with the same colour aleady exists.",
        ]);

        $f = new Flag();
        $f->fname = $request->fname;
        $f->color = $request->color;
        $f->save();

        return response()->api([
            'data' => $f,
            'message' => "Flag saved.",
        ], 201);
    }

    /**
     * DELETE /flag/{id}
     */
    public function delete($id)
    {
        Flag::destroy($id);
        return response()->api([
            'message' => "Flag deleted.",
        ]);
    }

    /**
     * GET /flag/{id?}
     */
    public function get($id = false)
    {
        if ($id) {
            $f = Flag::findOrFail($id);
        } else {
            $f = Flag::orderBy('fname')->get();
        }

        return response()->api($f);
    }

    /**
     * PUT /flag/{id}
     */
    public function update(Request $request, $id)
    {
        $f = Flag::findOrFail($id);

        $request->validate([
            'fname' => "sometimesd|string|min:1|max:20|unique:tenant.flags,fname,$id,id",
            'color' => "sometimes|string|min:1|max:10|unique:tenant.flags,fname,$id,id",
        ], [
            'fname.unique' => "A flag with the same name aleady exists.",
            'color.unique' => "A flag with the same colour aleady exists.",
        ]);

        if ($request->has('fname')) {
            $f->fname = $request->fname;
        }
        if ($request->has('color')) {
            $f->color = $request->color;
        }
        $f->save();

        return response()->api([
            'data' => $f,
            'message' => "Flag saved.",
        ]);
    }

    //shift flags
    /**
     * GET /shift/{id}/flag
     */
    public function shiftFlags($id)
    {
        $flags = Flag::orderBy('fname')->get();

        foreach ($flags as $f) {
            if ($f->shift()->where('shift_id', $id)->first()) {
                $f->set = 1;
            } else {
                $f->set = 0;
            }
        }

        return response()->api($flags);
    }

    /**
     * PUT /shift/{shiftId}/flag/{id}/{set}
     */
    public function set($shiftId, $id, $set)
    {
        $f = Flag::findOrFail($id);
        $s = \App\Shift::findOrFail($shiftId);

        if ($set == 1) {
            $s->flags()->syncWithoutDetaching($id);
        } else {
            $s->flags()->detach($id);
        }

        return response()->api(['message' => 'Saved.']);
    }
}
