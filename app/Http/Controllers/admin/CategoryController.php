<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;   
use Illuminate\Support\Facades\File;
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();
        
        if (!empty($request->get("keyword"))) {
            $categories = $categories->where("name", "like", "%" . $request->get("keyword") . "%");
        }

        $categories = $categories->paginate(10);
        return view("admin.category.list",compact("categories"));
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

            //Save image
            if(!empty($request->image_id))
            {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode(".",$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.".".$ext;

                $sPath = public_path()."/temp/".$tempImage->name;
                $dPath = public_path()."/uploads/category/".$newImageName;

                File::copy($sPath,$dPath);

                $category->image = $newImageName;
                $category->save();
            }

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
