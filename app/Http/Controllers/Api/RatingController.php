<?php
namespace App\Http\Controllers\Api;

use App\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{

    /**
     * POST /rating
     */
    public function create(Request $request)
    {
        $request->validate([
            'rname' => 'required|min:1|max:30|unique:tenant.ratings',
        ], [
            'rname.required' => "Please enter a name for the rating.",
            'rname.unique' => "A rating with the same name aleady exists.",
        ]);

        $r = new Rating();
        $r->rname = $request->rname;
        $r->save();

        return response()->api([
            'data' => $r,
            'message' => "Rating saved.",
        ], 201);
    }

    /**
     * DELETE /rating/{id}
     */
    public function delete($id)
    {
        Rating::destroy($id);
        return response()->api([
            'message' => "Rating deleted.",
        ]);
    }

    /**
     * GET /rating/{id?}
     */
    public function get($id = false)
    {
        if ($id) {
            $rs = Rating::findOrFail($id);
        } else {
            $rs = Rating::all();
        }

        return response()->api($rs);
    }

    /**
     * PUT /rating/{id}
     */
    public function update(Request $request, $id)
    {
        $r = Rating::findOrFail($id);

        $request->validate([
            'rname' => "required|min:1|max:30|unique:tenant.ratings,id,$id",
        ], [
            'rname.required' => "Please enter a name for the rating.",
            'rname.unique' => "A rating with the same name aleady exists.",
        ]);

        $r->rname = $request->rname;
        $r->save();

        return response()->api([
            'data' => $r,
            'message' => "Rating saved.",
        ]);
    }
}
