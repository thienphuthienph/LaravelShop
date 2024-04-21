<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategory\CreateSubCategoiesRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{

     public function index(Request $request)
     {
          $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
               ->leftJoin('categories','categories.id','sub_categories.category_id');

          //Search keyword
          if (!empty($request->get("keyword"))) {
               $subCategories = $subCategories->where("sub_categories.name", "like", "%" . $request->get("keyword") . "%");
               $subCategories = $subCategories->orwhere("categories.name", "like", "%" . $request->get("keyword") . "%");
          }

          $subCategories = $subCategories->orderBy("created_at","asc")->paginate(10);
          return view("admin.subcategory.list",compact("subCategories"));
     }

     public function create()
     {
          $categories = Category::orderBy("name", "asc")->get();
          $data['categories'] = $categories;
          return view("admin.subcategory.create", $data);
     }

     public function store(Request $request)
     {
          $validate = Validator::make(
               $request->all(),
               [
                    "name" => "required",
                    "slug" => "required|unique:categories",

               ]
          );
          if ($validate->passes()) {
               $subcategory = new SubCategory();
               $subcategory->name = $request->name;
               $subcategory->slug = $request->status;
               $subcategory->status = $request->status;
               $subcategory->category_id = $request->category;
               $subcategory->save();

               $request->session()->flash("success", "Sub Category add successfully");
               return response()->json([
                    "status" => true,
                    "message" => "SubCategory add successfully"
               ]);
          } else {
               return response()->json([
                    "status" => false,
                    "message" => $validate->errors(),
               ]);
          }

     }

     public function edit($id,Request $request)
     {
          $subCategory = SubCategory::find($id);
          if(empty($subCategory))
          {
               $request->session()->flash('error','record not found');
               return redirect()->route('sub-categories.index');
          }


          $categories = Category::orderBy("name", "asc")->get();
          $data['categories'] = $categories;
          $data['subCategory'] = $subCategory;
          return view("admin.subcategory.edit", $data);
     }

     public function update($id, Request $request)
     {
          $subCategory = SubCategory::find($id);

          if(empty($subCategory))
          {
               $request->session()->flash("error","Record not found");
               return response()->json([
                    "status"=> false,
                    "notFound"=> true
                    ]);
          }

          $validator = Validator::make($request->all(), [
               "name"=> "required",
               "slug" => "required|unique:categories,slug,'.$subCategory->id.',id",
               "category" => "required",
               "status" => "required"
               ]);

          if ($validator->passes())
          {
               $subCategory->name = $request->name;
               $subCategory->slug = $request->slug;
               $subCategory->category_id = $request->category;
               $subCategory->status = $request->status;
               $subCategory->save();

               $request->session()->flash("success","SubCategory updated successfully");
               return response()->json([
                    "status"=> true,
                    "message"=> "SubCategory updated successfully"
                    ]);

          }
          
     }

     public function destroy($id, Request $request)
     {
          $subCategory = SubCategory::find($id);

          if(empty($subCategory))
          {
               $request->session()->flash("error","Record not found");
               return response()->json([
                    "status"=> false,
                    "notFound" => true
                    ]);

          }
          $subCategory->delete();
          $request->session()->flash("success","Delete subCategory successfully");
          return response()->json([
               "status"=> true,
               "notFound" => "Delete subCategory successfully"
               ]);
     }
}
