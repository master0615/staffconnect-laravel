<?php
namespace App\Http\Controllers\Api;

use App\WorkArea;
use App\WorkAreaCategory;
use Illuminate\Http\Request;

class WorkAreaController extends Controller
{
    /**
     * POST /workArea/category
     */
    public function createCategory(Request $request)
    {
        $request->validate([
            'cname' => 'bail|required|unique:tenant.work_area_categories|min:1|max:50',
        ], [
            'cname.required' => "Please enter a name for the category",
            'cname.unique' => "A category with the same name aleady exists.",
        ]);

        $workAreaCat = new WorkAreaCategory();
        $workAreaCat->cname = $request->cname;
        $workAreaCat->save();

        return response()->api([
            'data' => $workAreaCat,
            'message' => "Category saved.",
        ], 201);
    }

    /**
     * POST /workArea
     */
    public function createWorkArea(Request $request)
    {
        $request->validate([
            'aname' => 'bail|required|unique:tenant.work_areas|min:1|max:50',
        ], [
            'aname.required' => "Please enter a name for the work area.",
            'aname.unique' => "A work area with the same name aleady exists.",
        ]);

        $workArea = new WorkArea();
        $workArea->aname = $request->aname;
        $workArea->php_tz = $request->input('php_tz', null);
        $workArea->lat = $request->input('lat', null);
        $workArea->lon = $request->input('lon', null);
        $workArea->work_area_cat_id = $request->input('work_area_cat_id', null);
        $workArea->save();

        return response()->api([
            'data' => $workArea,
            'message' => "Work area saved.",
        ], 201);
    }

    /**
     * DELETE /workArea/category/{id}
     */
    public function deleteCategory($id)
    {
        WorkAreaCategory::destroy($id);
        return response()->api([
            'message' => "Category deleted.",
        ]);
    }

    /**
     * DELETE /workArea/{id}
     */
    public function deleteWorkArea($id)
    {
        WorkArea::destroy($id);
        return response()->api([
            'message' => "Work area deleted.",
        ]);
    }

    /**
     * GET /workArea/category/{id?}
     */
    public function getCategories($id = false)
    {
        if ($id) {
            $cats = WorkAreaCategory::findOrFail($id);
        } else {
            $cats = WorkAreaCategory::all();
        }
        return response()->api($cats);
    }

    /**
     * GET /workArea/{id?}
     */
    public function getWorkAreas($id = false)
    {
        if ($id) {
            $workAreas = WorkArea::findOrFail($id);
        } else {
            $workAreas = WorkArea::all();
        }

        return response()->api($workAreas);
    }

    /**
     * PUT /workArea/{id}
     */
    public function updateWorkArea(Request $request, $id)
    {
        $workArea = WorkArea::findOrFail($id);

        $request->validate([
            'aname' => "sometimes|unique:tenant.work_areas,id,$id",
            'work_area_cat_id' => 'sometimes|nullable|exists:tenant.work_area_categories,id',
        ], [
            'aname.unique' => "A work area with the same name aleady exists.",
        ]);

        $workArea->aname = $request->input('aname', $workArea->aname);
        $workArea->php_tz = $request->input('php_tz', $workArea->php_tz);
        $workArea->lat = $request->input('lat', $workArea->lat);
        $workArea->lon = $request->input('lon', $workArea->lon);
        $workArea->work_area_cat_id = $request->input('work_area_cat_id', $workArea->work_area_cat_id);
        $workArea->save();

        return response()->api([
            'data' => $workArea,
            'message' => "Work area updated.",
        ]);
    }

    /**
     * PUT /workArea/category{id}
     */
    public function updateCategory(Request $request, $id)
    {
        $cat = WorkAreaCategory::findOrFail($id);

        $request->validate([
            'cname' => "sometimes|unique:tenant.work_area_categories,id,$id",
        ], [
            'cname.unique' => "A category with the same name aleady exists.",
        ]);

        $cat->cname = $request->cname;
        $cat->save();

        return response()->api([
            'data' => $cat,
            'message' => "Category updated.",
        ]);
    }
}
