<?php
namespace App\Http\Controllers\Api;

use App\Unavailability;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnavailabilityController extends Controller
{
    // users can only create unavailability for themselves, not others
    /**
     *
     * @return \Illuminate\Http\JsonResponse @SWG\Post(
     *         path="/profile/unavailability",
     *         tags={"unavailability"},
     *         description="Create an unavailability.",
     *         produces={"application/json"},
     *         @SWG\Parameter(
     *         name="title",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="Unavailability title eg. University",
     *         ),
     *         @SWG\Parameter(
     *         name="ua_start",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="Start datetime eg. 2017-08-11 09:00:00",
     *         ),
     *         @SWG\Parameter(
     *         name="ua_end",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="End datetime eg. 2017-08-11 14:00:00",
     *         ),
     *         @SWG\Parameter(
     *         name="weekday",
     *         in="formData",
     *         required=false,
     *         type="integer",
     *         description="Null for irregular, 0-6 for regular to indicate day of week. CHECK",
     *         ),
     *         @SWG\Response(
     *         response=201,
     *         description="Unavailability created.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="1"),
     *         @SWG\Property(property="message", type="string", example="Unavailability saved."),
     *         @SWG\Property(property="data", ref="#/definitions/Unavailability"),
     *         ),
     *         ),
     *         @SWG\Response(
     *         response="default",
     *         description="An unexpected error.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="0"),
     *         @SWG\Property(property="message", type="string", example="Error"),
     *         ),
     *         )
     *         )
     */
    public function createUnavailability(Request $request)
    {
        $request->validate([
            'title' => 'required|min:1|max:30',
            'ua_start' => 'required|date|after_or_equal:today',
            'ua_end' => 'required|date|after:ua_start',
        ], [
            'title.required' => "Please enter a title for the unavailability.",
        ]);
        $ua = new Unavailability($request->all());
        $ua->user_id = Auth::user()->loggedInAsId();
        $ua->weekday = $request->input('weekday', null);
        $ua->save();

        return response()->api([
            'data' => $ua,
            'message' => "Unavailability saved.",
        ], 201);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse @SWG\Delete(
     *         path="/profile/unavailability/{id}",
     *         tags={"unavailability"},
     *         description="Delete an unavailability.",
     *         produces={"application/json"},
     *         @SWG\Parameter(
     *         name="id",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="Unavailability id",
     *         ),
     *         @SWG\Response(
     *         response=200,
     *         description="Deleted.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="1"),
     *         @SWG\Property(property="message", type="string", example="Unavailability deleted."),
     *         ),
     *         ),
     *         @SWG\Response(
     *         response="default",
     *         description="An unexpected error.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="0"),
     *         @SWG\Property(property="message", type="string", example="Error"),
     *         ),
     *         )
     *         )
     */
    public function deleteUnavailability($id)
    {
        Unavailability::destroy($id);
        return response()->api([
            'message' => "Unavailability deleted.",
        ]);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse @SWG\Get(
     *         path="/profile/{id}/unavailability",
     *         tags={"unavailability"},
     *         description="Get a user's unavailability.",
     *         produces={"application/json"},
     *         @SWG\Parameter(
     *         name="id",
     *         in="formData",
     *         required=true,
     *         type="integer",
     *         description="User id",
     *         ),
     *         @SWG\Response(
     *         response=200,
     *         description="Array of unavailabilities.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="1"),
     *         @SWG\Property(property="data", ref="#/definitions/Unavailability"),
     *         ),
     *         ),
     *         @SWG\Response(
     *         response="default",
     *         description="An unexpected error.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="0"),
     *         @SWG\Property(property="message", type="string", example="Error"),
     *         ),
     *         )
     *         )
     */
    public function getUnavailability($user_id)
    {
        $uas = Unavailability::where('user_id', $user_id)->get();
        return response()->api([
            'data' => $uas,
        ]);
    }
}
