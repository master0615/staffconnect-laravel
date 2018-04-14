<?php
namespace App\Http\Controllers\Api;

use App\TrackingCategory;
use App\TrackingOption;
use Illuminate\Http\Request;

class TrackingCategoryController extends Controller
{

    /**
     * POST /tracking/category
     */
    public function createCategory(Request $request)
    {
        $request->validate([
            'cname' => 'bail|required|unique:tenant.tracking_categories|min:1|max:30',
            'client_visibility' => 'sometimes|in:hidden,visible',
            'staff_visibility' => 'sometimes|in:hidden,visible,visible_after_selection',
            'required ' => 'sometimes|in:0,1',
        ], [
            'cname.required' => "Please enter a name for the category",
            'cname.unique' => "A category with the same name aleady exists.",
        ]);

        $cat = new TrackingCategory;
        $cat->cname = $request->cname;
        $cat->staff_visibility = $request->input('staff_visibility', 'hidden');
        $cat->client_visibility = $request->input('client_visibility', 'hidden');
        $cat->required = $request->input('required', 0);
        $cat->save();

        return response()->api([
            'data' => $cat,
            'message' => "Category saved.",
        ], 201);
    }

    /**
     * POST /tracking/option
     */
    public function createOption(Request $request)
    {
        $request->validate([
            'oname' => 'required|unique:tenant.tracking_options|min:1|max:60',
            'tracking_cat_id' => 'required|numeric|exists:tenant.tracking_categories,id',
        ], [
            'oname.required' => "Please enter a name for the tracking option.",
            'oname.unique' => "A tracking option with the same name aleady exists.",
        ]);

        $opt = new TrackingOption($request->all());
        $opt->save();

        return response()->api([
            'data' => $opt,
            'message' => "Option saved.",
        ], 201);
    }

    /**
     * DELETE /tracking/category/{id}
     */
    public function deleteCategory($id)
    {
        TrackingCategory::destroy($id);
        return response()->api([
            'message' => "Category deleted.",
        ]);
    }

    /**
     * DELETE /tracking/option/{id}
     */
    public function deleteOption($id)
    {
        TrackingOption::destroy($id);
        return response()->api([
            'message' => "Option deleted.",
        ]);
    }

    /**
     * GET /tracking/category/{id?}
     */
    public function getCategories($id = false)
    {
        if ($id) {
            $cats = TrackingCategory::findOrFail($id);
        } else {
            $cats = TrackingCategory::all();
        }
        return response()->api($cats);
    }

    /**
     * GET /tracking/option/{catId?}/{id?}
     */
    public function getOptions($catId = false, $id = false)
    {
        if ($id) {
            $opts = TrackingOption::findOrFail($id);
        } elseif ($catId) {
            $opts = TrackingOption::where('tracking_cat_id', $catId)->get();
        } else {
            $opts = TrackingOption::all();
        }
        return response()->api($opts);
    }

    /**
     * PUT /tracking/option/{id}
     */
    public function updateOption(Request $request, $id)
    {
        $opt = TrackingOption::findOrFail($id);

        $request->validate([
            //'aname' => 'sometimes|unique:tenant.work_areas',
            'tracking_cat_id' => 'sometimes|exists:tenant.tracking_categories,id',
        ], [
            'aname.unique' => "A work area with the same name aleady exists.",
        ]);

        if ($request->has('oname')) {
            if (TrackingOption::where('id', '!=', $id)->where('oname', $request->oname)->first()) {
                return response()->api([
                    'message' => "A tracking option with the same name already exists.",
                ], 400);
            }
            $opt->oname = $request->oname;
        }
        $opt->staff_visibility = $request->input('staff_visibility', $opt->staff_visibility);
        $opt->tracking_cat_id = $request->input('tracking_cat_id', $opt->tracking_cat_id);
        $opt->active = $request->input('active', $opt->active);
        $opt->save();

        return response()->api([
            'data' => $opt,
            'message' => "Option saved.",
        ]);
    }

    /**
     * PUT /tracking/category/{id}
     */
    public function updateCategory(Request $request, $id)
    {
        $cat = TrackingCategory::findOrFail($id);

        $request->validate([
            'client_visibility' => 'sometimes|in:hidden,visible',
            'staff_visibility' => 'sometimes|in:hidden,visible,visible_after_selection',
            'required ' => 'sometimes|in:0,1',
        ]);

        if ($request->has('cname')) {
            if (TrackingCategory::where('id', '!=', $id)->where('cname', $request->cname)->first()) {
                return response()->api([
                    'message' => "A category with the same name already exists.",
                ], 400);
            }
            $cat->cname = $request->cname;
        }
        $cat->staff_visibility = $request->input('staff_visibility', $cat->staff_visibility);
        $cat->client_visibility = $request->input('client_visibility', $cat->client_visibility);
        $cat->required = $request->input('required', $cat->required);
        $cat->save();

        return response()->api([
            'data' => $cat,
            'message' => "Category saved.",
        ]);
    }
}
