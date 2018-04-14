<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\OutsourceCompany;

/**
 * @SWG\Tag(
 * name="outsourceCompany",
 * description="Outsource companies"
 * )
 */
class OutsourceCompanyController extends Controller
{

    /**
     *
     * @return \Illuminate\Http\JsonResponse @SWG\Post(
     *         path="/outsourceCompany",
     *         tags={"outsourceCompany"},
     *         description="Create an outsource comapny.",
     *         produces={"application/json"},
     *         @SWG\Parameter(
     *         name="cname",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="Company name eg. Demo Company",
     *         ),
     *         @SWG\Response(
     *         response=201,
     *         description="Outsource company created.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="1"),
     *         @SWG\Property(property="message", type="string", example="Company saved."),
     *         @SWG\Property(property="data", ref="#/definitions/OutsourceCompany"),
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
    public function create(Request $request)
    {
        $request->validate([
            'cname' => 'required|min:1|max:50|unique:tenant.outsource_companies'
        ], [
            'cname.required' => "Please enter a name for the external company.",
            'cname.unique' => "A company with the same name aleady exists."
        ]);
        
        $oc = new OutsourceCompany();
        $oc->cname = $request->cname;
        $oc->save();
        
        return response()->api([
            'data' => $oc,
            'message' => "Company saved."
        ], 201);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse @SWG\Delete(
     *         path="/outsourceCompany/{id}",
     *         tags={"outsourceCompany"},
     *         description="Delete an outsource company.",
     *         produces={"application/json"},
     *         @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Outsource company id",
     *         ),
     *         @SWG\Response(
     *         response=200,
     *         description="Company deleted.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="1"),
     *         @SWG\Property(property="message", type="string", example="Company deleted.")
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
    public function delete($id)
    {
        OutsourceCompany::destroy($id);
        return response()->api([
            'message' => "Company deleted."
        ]);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse @SWG\Get(
     *         path="/outsourceCompany/{id?}",
     *         tags={"outsourceCompany"},
     *         description="Get a specific or all outsource companies.",
     *         produces={"application/json"},
     *         @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=false,
     *         type="integer",
     *         description="Outsource company id",
     *         ),
     *         @SWG\Response(
     *         response=200,
     *         description="Array of outsource companies.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="1"),
     *         @SWG\Property(property="data", type="array",
     *         @SWG\Items(ref="#/definitions/OutsourceCompany")),
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
    public function get($id = 0)
    {
        if ($id) {
            $ocs = OutsourceCompany::findOrFail($id);
        } else {
            $ocs = OutsourceCompany::all();
        }
        return response()->api([
            'data' => $ocs
        ]);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse @SWG\Put(
     *         path="/outsourceCompany/{id}",
     *         tags={"outsourceCompany"},
     *         description="Update an outsource company.",
     *         produces={"application/json"},
     *         @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Outsource company id",
     *         ),
     *         @SWG\Parameter(
     *         name="cname",
     *         in="formData",
     *         required=false,
     *         type="string",
     *         description="Company name",
     *         ),
     *         @SWG\Response(
     *         response=200,
     *         description="Company updated.",
     *         @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="success", type="integer", example="1"),
     *         @SWG\Property(property="message", type="string", example="Company saved."),
     *         @SWG\Property(property="data", type="array",
     *         @SWG\Items(ref="#/definitions/OutsourceCompany")),
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
    public function update(Request $request, $id)
    {
        $oc = OutsourceCompany::findOrFail($id);
        
        if ($request->has('cname')) {
            if (OutsourceCompany::where([
                [
                    'id',
                    '!=',
                    $id
                ],
                [
                    'cname',
                    $request->cname
                ]
            ])->first()) {
                return response()->api([
                    'message' => "A company with the same name already exists."
                ], 400);
            }
            $oc->cname = $request->cname;
        }
        $oc->save();
        
        return response()->api([
            'data' => $oc,
            'message' => "Company saved."
        ]);
    }
}
