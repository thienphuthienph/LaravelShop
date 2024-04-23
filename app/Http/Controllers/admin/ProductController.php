<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\TempImage;
use App\Models\ProductImage;    
use Intervention\Image\ImageManager;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy("name","asc")->get();
        $brands = Brand::orderBy("name","asc")->get();

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view("admin.products.create", $data);
    }

    public function store(Request $request)
    {

        $rule = ["title"=> "required ",
        "slug"=> "required|unique:products",
        "price"=> "required|numeric ",
        "sku"=> "required |unique:products",
        "track_qty"=> "required|in:Yes,No ",
        "category"=> "required|numeric ",
        "is_featured"=> "required|in:Yes,No ",];

        if(!empty($request->track_qty) && $request->track_qty == "Yes")
        {
            $rule["qty"] = "required|numeric";
        }
        $validator = Validator::make($request->all(), $rule);

        if($validator->passes())
        {
            $product = new Product();
            $product->title         =     $request->title;
            $product->slug          =     $request->slug;
            $product->description   =     $request->description;
            $product->price         =     $request->price;
            $product->compare_price =     $request->compare_price;
            $product->sku           =     $request->sku;
            $product->barcode       =     $request->barcode;
            $product->track_qty     =     $request->track_qty;
            $product->qty           =     $request->qty;
            $product->status        =     $request->status;
            $product->category_id   =     $request->category;
            $product->track_qty     =     $request->track_qty;
            $product->sub_category_id =   $request->sub_category;
            $product->brand_id      =     $request->brand;
            $product->is_featured   =     $request->is_featured;
            $product->save();


            //Save product pictures
            if(!empty($request->image_array))
            {
                foreach ($request->image_array as $temp_image_id)
                {
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode(".",$tempImageInfo->name);
                    $ext = last($extArray);


                    $productImage             = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image      = null;
                    $imageName                = $product->id.'-'.$productImage->id.'.'.time().'.'.$ext;
                    $productImage->image      = $imageName;
                    $productImage->save();

                    //Generated Product Thumbnail

                    //Large Image
                    $manager = new ImageManager(new Driver());
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $desPath = public_path().'/uploads/product/large/'.$tempImageInfo->name;
                    $image = $manager->read($sourcePath);
                    $image->scale(1400, null);
                    $image->save($desPath);

                    //Small Image
                    $manager = new ImageManager(new Driver());
                    $desPath = public_path().'/uploads/product/small/'.$tempImageInfo->name;
                    $image = $manager->read($sourcePath);
                    $image->resize(300,300);
                    $image->save($desPath);
                } 
            }

            $request->session()->flash("success","Product added successfully");
            
            return response()->json([
                "status"=> true,
                "message" => "product added successfully"
                ]);
            
        }
        else
        {
            return response()->json([
                "status" => false,
                "message"=> $validator->errors(),
            ]) ;	
        }
    }
}
