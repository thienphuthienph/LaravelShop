<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];

        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
        }


        $categories = Category::orderBy("name", "desc")->with("sub_category")->where("status", 1)->get();
        $brands = Brand::orderBy("name", "desc")->where("status", 1)->get();
        $products = Product::where("status", 1);

        //Apply Filter
        if (!empty($categorySlug)) {
            $category = Category::where("slug", $categorySlug)->first();
            $products = $products->where("category_id", $category->id);
            $categorySelected = $category->id;
        }

        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where("slug", $subCategorySlug)->first();
            $products = $products->where("sub_category_id", $subCategory->id);
            $subCategorySelected = $subCategory->id;
        }

        $products = $products->orderBy("id", "desc");
        $products = $products->get();
        $data['categories'] = $categories;
        $data['products'] = $products;
        $data['brands'] = $brands;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        return view("front.shop", $data);
    }

    //Product detail page
    public function product($id)
    {
        if(Auth::check())
        {
            $userId = Auth::user()->id;
            $user = User::find($userId);
        }
        else
        {
            $userId = 0;
            $user = null;
        }
        $product = Product::find($id);
        $productImages = ProductImage::where("product_id", $id)->get();
        $thumnail = ProductImage::where("product_id", $id)->first();
        $reviews = Review::where("product_id",$id)->get();
        if($reviews->count() <= 0)
        {
            $totalRating = 0;
        }
        else
        {
            $totalRating = $reviews->sum("rating") / $reviews->count();
        }
        
        $totalReview = $reviews->count();

        return view("front.product", [
            "product" => $product,
            "productImages" => $productImages,
            "thumbnail" => $thumnail,
            "reviews" => $reviews,
            "totalRating" => $totalRating,
            "totalReview" => $totalReview,
            "userId" => $userId,
            "user" => $user
        ]);
    }

    public function saveReview($producId, Request $request)
    {
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required",
        ]);

        if ($validator->passes()) {
            $review = new Review();
            $review->product_id = $producId;
            $review->status = 1;
            $review->name = $request->name;
            $review->email = $request->email;
            $review->comment = $request->comment;
            $review->rating = $request->rating;
            $review->user_id = $userId;
            $review->save();

            return response()->json([
                "status" => true,
                "message" => "Thank you"
            ]);
        }
        else{
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }


    }
}
