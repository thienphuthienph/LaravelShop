<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ]);
        }

        if (Cart::count() > 0) {
            //Already in cart
            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }
            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status = true;
                $message = $product->title . 'added in cart';
            } else {
                $status = false;
                $message = $product->title . 'already in cart';
            }
        } else {
            echo 'Cart empty adding';
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title . 'added in cart';
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
    public function delete(Request $request)
    {
        $itemInfo = Cart::get($request->rowId);

        if ($itemInfo == null) {
            session()->flash("error", "product not found");
            return response()->json([
                "status" => false,
                "message" => "product not found"
            ]);
        }

        Cart::remove($request->rowId);
        $request->session()->flash("success", "product remove from cart");
        return response()->json([
            "status" => true,
            "message" => "product remove from cart"
        ]);
    }

    public function update(Request $request)
    {
        $qty = $request->qty;
        $rowId = $request->rowId;
        Cart::update($rowId, $qty);

        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);

        if ($product->track_qty == "Yes") {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $status = true;
                $message = "update successfully";
                $request->session()->flash("success", $message);
            } else {
                $status = false;
                $message = "out of stock";
                $request->session()->flash("error", $message);

            }
        } else {
            Cart::update($rowId, $qty);
            $status = true;
            $message = "update successfully";
            $request->session()->flash("success", $message);
        }
        return response()->json([
            'status' => true,
            'message' => 'update successfully'
        ]);
    }
    public function cart()
    {
        $cartContent = Cart::Content();
        $data['cartContent'] = $cartContent;

        return view("front.cart", $data);
    }

    public function checkout(Request $request)
    {
        if(Cart::count() == 0) {
            return redirect()->route("front.cart");
        }
        if(Auth::check() == false)
        {
            if(!session()->has('url.intended')){
                session(["url.intended"=> url()->current()]);
            }
            
            return redirect()->route("account.login");
        }

        session()->forget('url.intended');

        $cartContent = Cart::Content();
        $countries = Country::orderBy("name")->get();
        $data['cartContent'] = $cartContent;
        $data['countries'] = $countries;

        return view("front.checkout",$data);
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "first_name" => "required|min:5",
            "last_name" => "required",
            "email" => "required",
            "country" => "required",
            "address" => "required|min:30",
            "city" => "required",
            "zip" => "required",
            "mobile" => "required",
        ]);

        if($validator->fails())
        {
            return response()->json([
                "message" => "something went wrong",
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }

        $user = Auth::user();
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,

                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]

        );


        if($request->payment_method=='cod')
        {
            $shipping = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2,'.','');
            $grandTotal = $subTotal + $shipping;

            $order = new Order;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;

            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->notes = $request->notes;
            $order->country_id = $request->country;
            $order->city = $request->city;
            $order->save();

            //Luu OrderItems
            foreach(Cart::content() as $item)
            {
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;
                
                $orderItem->save();               
            }
        }
        $request->session()->flash("success","Đặt hàng thành công");

        return response()->json([
            "message" => "Save successfully",
            "orderId" => $orderItem->id,
            "status" => true,
        ]);
        
    }

    public function thankyou($id)
    {
        return view("front.Thankyous",
    [
        "id" => $id,
    ]);
    }
}
