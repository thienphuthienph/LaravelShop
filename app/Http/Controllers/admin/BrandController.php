<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
class BrandController extends Controller
{

    public function index(Request $request)
    {
        // $brands = Brand::query();
        
        // if (!empty($request->get("keyword"))) {
        //     $brands = $brands->where("name", "like", "%" . $request->get("keyword") . "%");
        // }

        // $brands = Brand::orderBy("created_at","desc")->paginate(10);

        // return view("admin.brands.list", compact("brands"));


        $brands = Brand::latest('id');

        if (!empty($request->get("keyword"))) {
                $brands = $brands->where("name", "like", "%" . $request->get("keyword") . "%");
             }

        $brands = $brands->paginate(10);
        return view("admin.brands.list", compact("brands"));
        
    }
    public function create()
    {
        return view("admin.brands.create");
    }

    public function store(Request $request)
    {    
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "slug" => "required|unique:brands",
        ]);

        if ($validator->passes()) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();          
        } else {
            return response()->json([
                "status" => false,
                "message"=> $validator->errors(),	
                ]);
        }
    }

    public function edit($id , Request $request)
    {
        $brand = Brand::find($id);  

        if (empty($brand))
        {
            $request->session()->flash("error","Record not found");
            return redirect()->route("brands.index");
        }
        
        $data["brand"] = $brand;
        return view("admin.brands.edit", $data);
    }

    public function update($id , Request $request)
    {
        $brand = Brand::find($id);  
       
        if(empty($brand))
        {
            $request->session()->flash("error","Record not found");

            return response()->json([
                "status"=> false,
                "notFound"=> true
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:brands,slug,".$brand->id.",id",
            "status" => "required",
            ]);
        
            if($validator->passes())
            {
                $brand->name = $request->name;
                $brand->slug = $request->slug;
                $brand->status = $request->status;
                $brand->save();

                $request->session()->flash("success","Brand update successfully");

                return redirect()->route("brands.index");
            }
            else
            {
                return response()->json([
                    "status"=> false,
                    "errors"=> $validator->errors()
                    ]);
            }
        
    }

    public function destroy($id, Request $request)
    {
        $brand = Brand::find($id);
        if(empty($brand))
        {
            $request->session()->flash("error","Record not found");
            return response()->json([
                "status"=> false,
                "notFound"=> true
                ]);
        }
        $brand->delete();
    }
}
