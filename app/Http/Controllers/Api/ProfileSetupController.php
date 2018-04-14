<?php
namespace App\Http\Controllers\Api;

use App\ProfileCategory;
use App\ProfileElement;
use App\ProfileListOption;
use Illuminate\Http\Request;

class ProfileSetupController extends Controller
{

    /**
     * POST profileStructure/element
     */
    public function createElement(Request $request)
    {
        $request->validate([
            'ename' => 'required|string|min:1|max:60',
            'etype' => 'sometimes|in:short,medium,long,date,list,number,listm',
            'visibility' => 'sometimes|in:short,optional,required,hidden,pay',
            'sex' => 'sometimes|nullable|in:male,female',
            'filter' => 'sometimes|in:equals,range',
            'profile_cat_id' => 'sometimes|numeric|exists:tenant.profile_categories,id',
        ], [
            'ename.required' => "Please enter a name for the element",
        ]);

        $pe = new ProfileElement();
        $pe->ename = $request->ename;
        $pe->etype = $request->input('etype', 'short');
        $pe->visibility = $request->input('visibility', 'optional');
        $pe->sex = $request->input('sex', null);
        $pe->filter = $request->input('filter', 'equals');
        $pe->profile_cat_id = $request->input('profile_cat_id', 1);
        $pe->save();

        return response()->api([
            'data' => $pe,
            'message' => "Element saved.",
        ], 201);
    }

    /**
     *  POST /profileStructure/element/{id}/option
     */
    public function createListOption(Request $request, $id)
    {
        $request->validate([
            'option' => 'required|min:1|max:40',
        ], [
            'option.required' => "Please enter a name for the option",
        ]);

        $pe = ProfileElement::findOrFail($id);
        if ($pe->etype != 'list' && $pe->etype != 'listm') {
            return response()->api([
                'message' => "Options can only be added to list type profile elements.",
            ], 405);
        }

        $plo = new ProfileListOption();
        $plo->profile_element_id = $id;
        $plo->option = $request->option;
        if ($request->has('display_order')) {
            $plo->display_order = $request->display_order;
        }
        $plo->save();

        return response()->api([
            'data' => $plo,
            'message' => "Option saved.",
        ], 201);
    }

    /**
     * POST /profileStructure/category
     */
    public function createCategory(Request $request)
    {
        $request->validate([
            'cname' => 'bail|required|min:1|max:40',
        ], [
            'cname.required' => "Please enter a name for the category",
        ]);

        $category = new ProfileCategory();
        $category->cname = $request->cname;
        $category->profile_cat_id = $request->input('profile_cat_id', null);
        $category->save();

        return response()->api([
            'data' => $category,
            'message' => "Category saved.",
        ], 201);
    }

    /**
     * DELETE profileStructure/element/{id}
     */
    public function deleteElement($id)
    {
        $pe = ProfileElement::findOrFail($id);
        if ($pe->deletable) {
            ProfileElement::destroy($id);
        } else {
            throw new \App\Exceptions\SystemProtected();
        }
        return response()->api([
            'message' => "Profile element deleted.",
        ]);
    }

    /**
     * DELETE /profileStructure/category/{id}
     */
    public function deleteCategory($id)
    {
        // update elements whose category id is this to 1
        $pc = ProfileCategory::findOrFail($id);
        if ($pc->deletable) {
            ProfileElement::where('profile_cat_id', $id)->update([
                'profile_cat_id' => 1,
            ]);
        } else {
            throw new \App\Exceptions\SystemProtected();
        }

        ProfileCategory::destroy($id);
        return response()->api([
            'message' => "Profile category deleted.",
        ]);
    }

    /**
     * DELETE /profileStructure/option/{id}
     */
    public function deleteListOption($id)
    {
        $plo = ProfileListOption::findOrFail($id);
        $plo->destroy($id);

        return response()->api([
            'message' => "Option deleted.",
        ]);
    }

    /**
     * GET /profileStructure/element/{id?}
     */
    public function getElements($id = false)
    {
        if ($id) {
            $pes = ProfileElement::findOrFail($id);
        } else {
            $pes = ProfileElement::all();
        }

        return response()->api($pes);
    }

    /**
     * GET /profileStructure/category/{id?}
     */
    public function getCategories($id = false)
    {
        if ($id) {
            $cats = ProfileCategory::findOrFail($id);
        } else {
            $cats = ProfileCategory::all();
        }

        return response()->api($cats);
    }

    /**
     * PUT /profileStructure/element/{id}
     */
    public function updateElement(Request $request, $id)
    {
        $pe = ProfileElement::findOrFail($id);

        $request->validate([
            'ename' => 'sometimes|string|min:1|max:60',
            'etype' => 'sometimes|in:short,medium,long,date,list,number,listm',
            'visibility' => 'sometimes|in:short,optional,required,hidden,pay',
            'sex' => 'sometimes|nullable|in:male,female',
            'filter' => 'sometimes|in:equals,range',
            'profile_cat_id' => 'sometimes|numeric|exists:tenant.profile_categories,id',
        ]);

        if ($request->has('ename')) {
            if (!$pe->editable) {
                throw new \App\Exceptions\SystemProtected();
            }
            $pe->ename = $request->ename;
        }
        if ($request->has('etype')) {
            if (!$pe->editable) {
                throw new \App\Exceptions\SystemProtected();
            }
            $pe->etype = $request->etype;
        }
        if ($request->has('visibility')) {
            if (!$pe->editable) {
                throw new \App\Exceptions\SystemProtected();
            }
            $pe->visibility = $request->visibility;
        }
        if ($request->has('sex')) {
            if (!$pe->editable) {
                throw new \App\Exceptions\SystemProtected();
            }
            $pe->sex = $request->sex;
        }
        if ($request->has('filter')) {
            if (!$pe->editable) {
                throw new \App\Exceptions\SystemProtected();
            }
            $pe->filter = $request->filter;
        }
        if ($request->has('profile_cat_id')) {
            $pe->profile_cat_id = $request->profile_cat_id;
        }
        $pe->save();

        return response()->api([
            'data' => $pe,
            'message' => "Profile element saved.",
        ]);
    }

    /**
     * PUT /profileStructure/category/{id}
     */
    public function updateCategory(Request $request, $id)
    {
        $pc = ProfileCategory::findOrFail($id);

        $request->validate([
            'profile_cat_id' => "sometimes|nullable|exists:tenant.profile_categories,id|not_in:$id",
        ]);

        if ($request->has('cname')) {
            if (!$pc->deletable || $pc->id == 1) {
                throw new \App\Exceptions\SystemProtected();
            }
            $pc->cname = $request->cname;
        }
        if ($request->has('profile_cat_id')) {
            $pc->profile_cat_id = $request->profile_cat_id;
        }
        $pc->save();

        return response()->api([
            'data' => $pc,
            'message' => "Profile category saved.",
        ]);
    }

    /**
     * PUT /profileStructure/option/{id}
     */
    public function updateListOption(Request $request, $id)
    {
        $plo = ProfileListOption::findOrFail($id);

        $request->validate([
            'option' => "sometimes|string|min:1",
            'display_order' => "sometimes|nullable|numeric",
        ]);

        if ($request->has('option')) {
            $plo->option = $request->option;
        }
        if ($request->has('display_order')) {
            $plo->display_order = $request->display_order;
        }
        $plo->save();

        return response()->api([
            'data' => $plo,
            'message' => "Option saved.",
        ]);
    }
}
