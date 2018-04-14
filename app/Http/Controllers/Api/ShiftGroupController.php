<?php
namespace App\Http\Controllers\Api;

use App\Shift;
use App\ShiftGroup;
use Illuminate\Http\Request;

class ShiftGroupController extends Controller {

	// not added to routes
	public function groupShifts(Request $request) {}

	/**
	 *
	 * @return \Illuminate\Http\JsonResponse @SWG\Post(
	 *         path="/shiftGroup",
	 *         tags={"shift group"},
	 *         description="Create a shift group.",
	 *         produces={"application/json"},
	 *         @SWG\Parameter(
	 *         name="gname",
	 *         in="formData",
	 *         required=true,
	 *         type="string",
	 *         description="Group name eg. Presentation",
	 *         ),
	 *         @SWG\Parameter(
	 *         name="shift_ids",
	 *         in="formData",
	 *         required=true,
	 *         type="array",
	 *         @SWG\Items(
	 *         type="integer"
	 *         ),
	 *         description="Array of shift ids",
	 *         ),
	 *         @SWG\Response(
	 *         response=201,
	 *         description="Rating created.",
	 *         @SWG\Schema(
	 *         type="object",
	 *         @SWG\Property(property="success", type="integer", example="1"),
	 *         @SWG\Property(property="message", type="string", example="Rating saved."),
	 *         @SWG\Property(property="data", type="object",
	 *             @SWG\Property(property="num_shifts", type="integer", example="2"),
	 *             @SWG\Property(property="shift_group", ref="#/definitions/ShiftGroup"),
	 *         ),
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
	public function create(Request $request) {
		$request->validate([
			'gname' => 'required|min:1|max:70',
			'shift_ids' => 'required|array|min:2',
		], [
			'gname.required' => "Please enter a name for the group",
		]);

		$sg = new ShiftGroup();
		$sg->gname = $request->gname;
		$sg->save();

		$num_shifts = 0;
		foreach ($request->shift_ids as $shift_id) {
			$shift = Shift::find($shift_id);
			$shift->shift_group_id = $sg->id;
			// log activity?
			$shift->save();
			$num_shifts++;
		}

		return response()->api([
			'data' => [
				'shift_group' => $sg,
				'num_shifts' => $num_shifts,
			],
			'message' => "Saved.",
		], 201);
	}

	/**
	 *
	 * @return \Illuminate\Http\JsonResponse @SWG\Delete(
	 *         path="/shiftGroup/{id}",
	 *         tags={"shift group"},
	 *         description="Delete a shift group.",
	 *         produces={"application/json"},
	 *         @SWG\Parameter(
	 *         name="id",
	 *         in="path",
	 *         required=true,
	 *         type="integer",
	 *         description="Shift group id",
	 *         ),
	 *         @SWG\Response(
	 *         response=200,
	 *         description="Group deleted.",
	 *         @SWG\Schema(
	 *         type="object",
	 *         @SWG\Property(property="success", type="integer", example="1"),
	 *         @SWG\Property(property="message", type="string", example="Group deleted.")
	 *         ),
	 *         ),
	 *         @SWG\Response(
	 *         response="default",
	 *         description="An unexpected error.",
	 *         @SWG\Schema(
	 *         type="object",
	 *         @SWG\Property(property="success", type="integer", example="0"),
	 *         @SWG\Property(property="message", type="string", example="Error")
	 *         ),
	 *         )
	 *         )
	 */
	public function delete($id) {
		ShiftGroup::destroy($id);
		return response()->api([
			'message' => "Group deleted.",
		]);
	}

	/**
	 *
	 * @return \Illuminate\Http\JsonResponse @SWG\Get(
	 *         path="/shiftGroup/{id}",
	 *         tags={"shift group"},
	 *         description="Get shift group.",
	 *         produces={"application/json"},
	 *         @SWG\Parameter(
	 *         name="id",
	 *         in="path",
	 *         required=true,
	 *         type="integer",
	 *         description="Shift group id",
	 *         ),
	 *         @SWG\Response(
	 *         response=200,
	 *         description="Shift group and shifts.",
	 *         @SWG\Schema(
	 *         type="object",
	 *         @SWG\Property(property="success", type="integer", example="1"),
	 *          @SWG\Property(property="data", type="object",
	 *             @SWG\Property(property="id", type="integer", example="1"),
	 *               @SWG\Property(property="gname", type="string", example="5day Event"),
	 *               @SWG\Property(property="apply_all_or_nothing", type="string", example="false"),
	 *              @SWG\Property(property="shifts", type="array",
	 *             @SWG\Items(ref="#/definitions/Shift"),),
	 *         ),
	 *         ),
	 *         ),
	 *         @SWG\Response(
	 *         response="default",
	 *         description="An unexpected error.",
	 *         @SWG\Schema(
	 *         type="object",
	 *         @SWG\Property(property="success", type="integer", example="0"),
	 *         @SWG\Property(property="message", type="string", example="Error")
	 *         ),
	 *         )
	 *         )
	 */
	public function get($id) {
		$sg = ShiftGroup::findOrFail($id);
		$sg->shifts = Shift::where('shift_group_id', $sg->id)->get();

		return response()->api([
			'data' => $sg,
		]);
	}
}
