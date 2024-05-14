<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Whishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class FrontController extends Controller
{
    public function index()
    {
        //Feature Product
        $products = Product::where("is_featured","Yes")->orderBy('id','desc')->get();
        $data['featuredProducts'] = $products;
        $latestProducts = Product::orderBy('id','desc')->where('status','1')->take(8)->get();
        $data['latestProducts'] = $latestProducts;
        return view("front.home",$data);
    }

    public function addToWishlist(Request $request)
    {
        if(!Auth::check())
        {
            session(['url.intended' => url()->previous()]);

            return response()->json([
                "status" => false,
            ]);
        }
        else
        {
            $product = Product::where("id",$request->id)->first();
            //Product not found
            if($product == null)
            {
                return response()->json([
                    "status" => true,
                    "message" => '<div class="alert alert-danger">Product not found</div>'
                ]); 
            }

            Whishlist::updateOrCreate([
                "user_id" => Auth::user()->id,
                "product_id" => $request->id,
            ],
            [
                "user_id" => Auth::user()->id,
                "product_id" => $request->id,
            ]);

            // $whishlist = new Whishlist();
            // $whishlist->product_id = $request->id;
            // $whishlist->user_id = Auth::user()->id;
            // $whishlist->save();

            return response()->json([
                "status" => true,
                "message" => '<div class="alert alert-success"><strong>'. $product->title .'</strong> added in your wishlist</div>'
            ]); 
        }
    }
}
