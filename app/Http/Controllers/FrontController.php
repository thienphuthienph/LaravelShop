<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
}
