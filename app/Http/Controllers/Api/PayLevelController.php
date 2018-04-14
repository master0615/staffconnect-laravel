<?php
namespace App\Http\Controllers\Api;

use App\PayCategory;
use App\PayLevel;
use Illuminate\Http\Request;

class PayLevelController extends Controller
{

    /**
     * POST payLevel
     */
    public function createLevel(Request $request)
    {
        $request->validate([
            'pname' => 'required|string|min:1|max:20|unique:tenant.pay_levels', // TODO allow same pname if in different category?
            'pay_cat_id' => 'required|numeric|exists:tenant.pay_categories,id',
            'pay_rate' => 'required|numeric|min:0',
            'pay_rate_type' => 'required|in:phr,flat',
        ], [
            'pname.required' => "Please enter a name for the pay level",
            'pname.unique' => "A pay level with the same name aleady exists.",
        ]);

        $plvl = new PayLevel();
        $plvl->pname = $request->pname;
        $plvl->pay_cat_id = $request->pay_cat_id;
        $plvl->pay_rate = $request->pay_rate;
        $plvl->pay_rate_Type = $request->pay_rate_type;
        $plvl->save();

        return response()->api([
            'data' => $plvl,
            'message' => "Pay level saved.",
        ], 201);
    }

    /**
     * POST payLevel/category
     */
    public function createCategory(Request $request)
    {
        $request->validate([
            'cname' => 'required|string|min:1|max:20|unique:tenant.pay_categories',
        ], [
            'cname.required' => "Please enter a name for the category",
            'cname.unique' => "A category with the same name aleady exists.",
        ]);

        $cat = new PayCategory;
        $cat->cname = $request->cname;
        $cat->save();

        return response()->api([
            'data' => $cat,
            'message' => "Category saved.",
        ], 201);
    }

    /**
     * DELETE payLevel/{id}
     */
    public function deleteLevel($id)
    {
        PayLevel::destroy($id);
        return response()->api([
            'message' => "Pay level deleted.",
        ]);
    }

    /**
     * DELETE payLevel/category/{id}
     */
    public function deleteCategory($id)
    {
        PayCategory::destroy($id);
        return response()->api([
            'message' => "Category deleted.",
        ]);
    }

    /**
     * GET payLevel/{catId?}/{lvlId?}
     */
    public function getLevels($catId = false, $lvlId = false)
    {
        if ($catId) {
            $lvls = PayLevel::where('pay_cat_id', $catId)->get();
        } elseif ($lvlId) {
            $lvls = PayLevel::findOrFail($lvlId);
        } else {
            $lvls = PayLevel::all();
        }
        return response()->api($lvls);
    }
    /**
     * GET payLevel/category/{catId?}
     */
    public function getCategories($catId = false)
    {
        if ($catId) {
            $cats = PayCategory::findOrFail($catId);
        } else {
            $cats = PayCategory::all();
        }
        return response()->api($cats);
    }

    /**
     * PUT payLevel/{id}
     */
    public function updateLevel(Request $request, $id)
    {
        $lvl = PayLevel::findOrFail($id);

        $request->validate([
            'pname' => 'sometimes|string|min:1|max:20',
            'pay_cat_id' => 'sometimes|numeric|exists:tenant.pay_categories,id',
            'pay_rate' => 'sometimes|numeric|min:0',
            'pay_rate_type' => 'sometimes|in:phr,flat',
        ]);

        if ($request->has('pname')) {
            if (PayLevel::where([
                [
                    'pname',
                    $request->pname,
                ],
                [
                    'id',
                    '!=',
                    $id,
                ],
                [
                    'pay_cat_id',
                    '!=',
                    $request->input('pay_cat_id', $lvl->pay_cat_id),
                ],
            ])->first()) {
                return response()->api([
                    'message' => "A pay level with the same name already exists.",
                ], 400);
            }
            $lvl->pname = $request->pname;
        }
        $lvl->pay_rate = $request->input('pay_rate', $lvl->pay_rate);
        $lvl->pay_rate_type = $request->input('pay_rate_type', $lvl->pay_rate_type);
        $lvl->pay_cat_id = $request->input('pay_cat_id', $lvl->pay_cat_id);
        $lvl->save();

        return response()->api([
            'data' => $lvl,
            'message' => "Pay level saved.",
        ]);
    }
    /**
     * PUT payLevel/category/{id}
     */
    public function updateCategory(Request $request, $id)
    {
        $cat = PayCategory::findOrFail($id);

        $request->validate([
            'cname' => "required|string|min:1|max:20|unique:tenant.pay_categories,cname,$id,id",
        ], [
            'cname.required' => "Please enter a name for the category",
            'cname.unique' => "A category with the same name aleady exists.",
        ]);

        $cat->cname = $request->cname;
        $cat->save();

        return response()->api([
            'data' => $cat,
            'message' => "Category saved.",
        ]);
    }
}
