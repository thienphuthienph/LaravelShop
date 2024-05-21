<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\TempImage;
use App\Models\ProductImage;    
use Intervention\Image\ImageManager;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::latest("id")->with('product_images');
        if (!empty($request->get("keyword"))) {
            $products = $products->where("title", "like", "%" . $request->get("keyword") . "%");
        }
        $products = $products->paginate();

        $data["products"] = $products;
        return view("admin.products.list",$data);
    }
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
                    $productImage->image      = 'NULL';
                    $productImage->save();
                    
                    $imageName                = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image      = $imageName;
                    $productImage->save();

                    //Generated Product Thumbnail

                    //Large Image
                    $manager = new ImageManager(new Driver());
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $desPath = public_path().'/uploads/product/large/'.$imageName;
                    $image = $manager->read($sourcePath);
                    $image->scale(1400, null);
                    $image->save($desPath);

                    //Small Image
                    $desPath = public_path().'/uploads/product/small/'.$imageName;
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

    public function edit($id,Request $request)
    {
        $product = Product::find($id);

        //Tra ve index neu khong tim thay product
        if(empty($product))
        {   
            return redirect()->route("products.index")->with("error","Product not found");
        }

        //Fetch Product images
        $productImages = ProductImage::where("product_id", $product->id)->get();
        $subCategories = SubCategory::where("category_id", $product->category_id)->get();

        $data = [];
        $categories = Category::orderBy("name","asc")->get();
        $brands = Brand::orderBy("name","asc")->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data["product"] = $product;
        $data["subCategories"] = $subCategories;
        $data["productImages"] = $productImages;
        return view('admin.products.edit',$data);
    }

    public function update( $id,Request $request)
    {
        $product = Product::find($id);

        $rule = ["title"=> "required ",
        "slug"=> "required|unique:categories,slug,".$product->id. ",id",
        "price"=> "required|numeric ",
        "sku"=> "required|unique:products,sku,".$product->id,

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

            $request->session()->flash("success","Product updated successfully");
            
            return response()->json([
                "status"=> true,
                "message" => "product updated successfully"
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

    public function destroy($id, Request $request)
    {
        $product = Product::find($id);

        if(empty($product))
        {
            
            $request->session()->flash("error","Product not found");
            return response()->json([
                "status"=> false,
                "notFound"=> true,
            ]);
        }

        $productImages = ProductImage::where("product_id", $id)->get();
        if(!empty($productImages))
        {
            foreach($productImages as $productImage)
            {
                //Xoa hinh trong file
                File::delete(public_path("/uploads/product/large/". $productImage->image));
                File::delete(public_path("/uploads/product/small/". $productImage->image));
            }
            //Xoa duong dan trong database
            ProductImage::where("product_id", $id)->delete();
        }
        $product->delete();

        $request->session()->flash("success","Product deleted successfully");

        return response()->json([
            "status"=> true,
            "message"=> "Product deleted successfully",
        ]);

    }

    public function productRating()
    {
        $rating = Review::select("reviews.*","products.title as productTitle")->orderBy("created_at","desc");
        $rating = $rating->leftJoin("products","products.id","reviews.product_id");
        $rating = $rating->paginate(10);
        return view("admin.products.rating",[
            "ratings" => $rating
        ]);
    }

    public function changeRatingStatus(Request $request)
    {
        $productRate = Review::find($request->id);
        $productRate->status = $request->status;
        $productRate->save();

        return response()->json(
            [
                "status" => true,
                "message" => "success"
            ]
            );
    }
}
