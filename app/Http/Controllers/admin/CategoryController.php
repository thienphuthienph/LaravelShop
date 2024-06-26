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
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use PHPUnit\Framework\Constraint\IsEmpty;
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query();
        
        if (!empty($request->get("keyword"))) {
            $categories = $categories->where("name", "like", "%" . $request->get("keyword") . "%");
        }
        $categories = $categories->orderBy('created_at','desc')->paginate(10);
        return view("admin.category.list",compact("categories"));
    }

    public function create()
    {
        return view("admin.category.create");
    }

    public function store(Request $request)
    {
        $manager = new ImageManager(new Driver());
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
            $category->showHome = $request->showHome;
            $category->save();


            $tempImage = TempImage::find($request->image_id);
            $extArray = explode(".",$tempImage->name);
            $ext = last($extArray);

            $newImageName = $category->id.".".$ext;
            $sPath = public_path()."/temp/".$tempImage->name;
            $dPath = public_path()."/uploads/category/".$newImageName;
            File::copy($sPath,$dPath);

            //Tao thumbnail
            $dPath = public_path()."/uploads/category/thumb/".$newImageName;
            $img = $manager->read($sPath);
            $img->resize(450,600);
            $img->save($dPath);

            $category->image = $newImageName;
            $category->save();
            //Save image
            /*if(!empty($request->image_id))
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
            }*/

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

    public function edit($categoryId,Request $request)
    {
        $category = Category::find($categoryId);

        if(empty($category))
        {
           return redirect()->route("categories.index");
        }

        return view("admin.category.edit",compact("category"));
    }

    public function update($categoryId,Request $request)
    {
        $category = Category::find($categoryId);

        if(empty($category))
        {
           return response()->json([
            "status" => false,
            "notFound" => "true",
            "message" => "Category not found"
           ]);        
        }

        $manager = new ImageManager(new Driver());
        $validator = Validator::make($request->all(),
        [
            "name"=> "required",
            "slug"=> "required|unique:categories,slug,".$category->id . ",id",
        ]);

        if($validator->passes())
        {
            //Truy van Eloquent ORM
            //$category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            $oldImage = $category->image;


            $tempImage = TempImage::find($request->image_id);
            $extArray = explode(".",$tempImage->name);
            $ext = last($extArray);

            $newImageName = $category->id."-".time().'.'.$ext;
            $sPath = public_path()."/temp/".$tempImage->name;
            $dPath = public_path()."/uploads/category/".$newImageName;
            File::copy($sPath,$dPath);

            //Tao thumbnail
            $dPath = public_path()."/uploads/category/thumb/".$newImageName;
            $img = $manager->read($sPath);
            $img->resize(450,600);
            $img->save($dPath);

            $category->image = $newImageName;
            $category->save();
            //Save image
            /*if(!empty($request->image_id))
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
            }*/

            $request->session()->flash("success","Category updated successfully");

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

    public function destroy($categoryId, Request $request)
    {
        $category = Category::find($categoryId);

        if(empty($category))
        {
            return response()->json([
                "status"=> false,
                "message"=> "Category not exist"
                ]);
        }
        File::delete(public_path("/uploads/category/thumb/".$category->image));
        File::delete(public_path("/uploads/category/".$category->image));
        $category->delete();
       
        $request->session()->flash("success","Category delete successfully");
    
        return redirect()->route("categories.index");

    }
}
