<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {

    }

    public function create()
    {
        return view("admin.category.create");
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            "name"=> "required",
            "slug"=> "required|unique:categories",
        ]);

        if($validator->passes())
        {
            //Truy van Eloquent ORM
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            $request->session()->flash("success","Category added successfully");

            return response()->json([
                "status"=> true,
                "message"=> "Category add successfully"
            ]);

            //Truy van bang querry builder
            /*DB::table("categories")->insert([
                "name"=> $request->name,
                "slug"=> $request->slug,
                "status"=> $request->status,
                ]);
            return response()->json([
                "status"=> true,
                "errors"=> $validator->errors()
            ]);
            */

        }
        else
        {
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
                ]);
        }
    }

    public function edit()
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy(Request $request, $id)
    {


    }
}
