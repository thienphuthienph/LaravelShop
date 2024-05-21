<?php 

use App\Mail\OrderMail;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\Page;

function getCategories()
{
    return Category::orderBy("name","asc")
        ->with('sub_category')
        ->orderBy('id','desc')
        ->where('status','1')
        ->where('showHome','Yes')
        ->get();
}

function getProductImage($id)
{
    return ProductImage::where("product_id",$id)->first();
}

function getOrderInfor($id)
{
    return Order::find($id);
}
 
function getOrder($id)
{

    //Get order infor to send mail
    $order = Order::where('id',$id)->first();
    $mailData = [
        "subject" => "Thank you for your order",
        "orders"  => $order,
    ];

    Mail::to($order->email)->send(new OrderMail($mailData));
}

function getPage()
{
    return Page::where("slug","get-in-touch")->first();
}