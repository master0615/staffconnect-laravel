<?php
namespace App\Http\Controllers\Api;

use App\Attribute;
use App\AttributeCategory;
use Illuminate\Http\Request;

class AttributeController extends Controller
{

    /**
     * POST/attribute
     */
    public function createAttribute(Request $request)
    {
        $request->validate([
            'aname' => 'required|min:1|max:50|unique:tenant.attributes',
            'visibility' => 'sometimes|in:staff,admin',
            'role_default' => 'sometimes|nullable|in:yes,no',
            'display_order' => 'sometimes|nullable|numeric',
            'attribute_cat_id' => 'sometimes|nullable|numeric|exists:tenant.attribute_categories,id',
        ], [
            'aname.required' => "Please enter a name for the attribute",
            'aname.unique' => "An attribute with the same name aleady exists.",
        ]);

        $attribute = new Attribute();
        $attribute->aname = $request->aname;
        $attribute->visibility = $request->input('visibility', 'staff');
        $attribute->role_default = $request->input('role_default', null);
        $attribute->display_order = $request->input('display_order', null);
        $attribute->attribute_cat_id = $request->input('attribute_cat_id', null);
        $attribute->save();

        return response()->api([
            'data' => $attribute,
            'message' => "Attribute saved.",
        ], 201);
    }

    /**
     * POST /attribute/category
     */
    public function createCategory(Request $request)
    {
        $request->validate([
            'cname' => 'required|unique:tenant.attribute_categories|min:1|max:50',
            'display_order' => 'sometimes|nullable|numeric',
        ], [
            'cname.required' => "Please enter a name for the category",
            'cname.unique' => "A category with the same name aleady exists.",
        ]);

        $cat = new AttributeCategory();
        $cat->cname = $request->cname;
        $cat->display_order = $request->input('display_order', $cat->display_order);
        $cat->save();

        return response()->api([
            'data' => $cat,
            'message' => "Category saved.",
        ], 201);
    }

    /**
     * DELETE /attribute/{id}
     */
    public function deleteAttribute($id)
    {
        Attribute::destroy($id);
        return response()->api([
            'message' => "Attribute deleted.",
        ]);
    }

    /**
     * DELETE /attribute/category/{id}
     */
    public function deleteCategory($id)
    {
        AttributeCategory::destroy($id);
        return response()->api([
            'message' => "Category deleted.",
        ]);
    }

    /**
     * GET /attribute/{id?}
     */
    public function getAttributes($id = 0)
    {
        if ($id) {
            $attrs = Attribute::findOrFail($id);
        } else {
            $attrs = Attribute::all();
        }
        return response()->api($attrs);
    }

    /**
     * GET /attribute/category/{id?}
     */
    public function getCategories($id = 0)
    {
        if ($id) {
            $cats = AttributeCategory::findOrFail($id);
        } else {
            $cats = AttributeCategory::all();
        }
        return response()->api($cats);
    }

    /**
     * PUT /attribute/{id}
     */
    public function updateAttribute(Request $request, $id)
    {
        $attr = Attribute::findOrFail($id);

        $request->validate([
            'aname' => "sometimes|min:1|max:50|unique:tenant.attributes,aname,$id,id",
            'visibility' => 'sometimes|in:staff,admin',
            'role_default' => 'sometimes|nullable|in:yes,no',
            'display_order' => 'sometimes|nullable|numeric',
            'attribute_cat_id' => 'sometimes|nullable|numeric|exists:tenant.attribute_categories,id',
        ], [
            'aname.unique' => "An attribute with the same name aleady exists.",
        ]);

        $attr->aname = $request->input('aname', $attr->aname);
        $attr->visibility = $request->input('visibility', $attr->visibility);
        $attr->role_default = $request->input('role_default', $attr->role_default);
        $attr->display_order = $request->input('display_order', null);
        $attr->attribute_cat_id = $request->input('attribute_cat_id', $attr->attribute_cat_id);
        $attr->save();

        return response()->api([
            'data' => $attr,
            'message' => "Attribute saved.",
        ]);
    }

    /**
     * PUT /attribute/category/{id}
     */
    public function updateCategory(Request $request, $id)
    {
        $cat = AttributeCategory::findOrFail($id);

        $request->validate([
            'cname' => "sometimes|min:1|max:50|unique:tenant.attribute_categories,cname,$id,id",
            'display_order' => 'sometimes|nullable|numeric',
        ], [
            'cname.unique' => "A category with the same name aleady exists.",
        ]);

        $cat->cname = $request->input('cname', $cat->cname);
        $cat->display_order = $request->input('display_order', $cat->display_order);
        $cat->save();

        return response()->api([
            'data' => $cat,
            'message' => "Category saved.",
        ]);
    }
}
