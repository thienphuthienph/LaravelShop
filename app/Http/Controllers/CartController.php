<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingCharge;
use Carbon\Carbon;
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
                Cart::update($rowId, $qty - 1);
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
        $discount = 0;
        if (Cart::count() == 0) {
            return redirect()->route("front.cart");
        }
        if (Auth::check() == false) {
            if (!session()->has('url.intended')) {
                session(["url.intended" => url()->current()]);
            }

            return redirect()->route("account.login");
        }
        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();


        session()->forget('url.intended');
        if($customerAddress != "")
        {
            $userCountry = $customerAddress->country_id;
            $shippingCharge = ShippingCharge::where("country_id", $userCountry)->first();
        }
        else
        {
            $shippingCharge = ShippingCharge::where("country_id", 233)->first();
        }
        

        $cartContent = Cart::Content();
        $countries = Country::orderBy("name")->get();

        $total = Cart::subtotal(2,".","");
        if(session()->has("code"))
        {
            $code = session()->get("code");

            if($code->type=="percent")
            {
                $discount = ($code->discount_amount/100)*$total;
            }
            else
            {
                $discount = $code->discount_amount;
            }
        }

        $totalQty = 0;
        $totalShippingCharge = 0;
        $grandTotal = 0;
        foreach (Cart::content() as $item) {
            $totalQty += $item->qty;
        }

        $totalShippingCharge = $shippingCharge->amount * $totalQty;
        $grandTotal = ($total - $discount) + $totalShippingCharge;

        $data['cartContent'] = $cartContent;
        $data['countries'] = $countries;
        $data["totalShippingCharge"] = $totalShippingCharge;
        $data["grandTotal"] = $grandTotal;
        $data["discount"]  = $discount;
        $data["customerAddress"] = $customerAddress;
        return view("front.checkout", $data);

    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => "required|min:5",
            "last_name" => "required",
            "email" => "required",
            "country" => "required",
            "address" => "required|min:20",
            "city" => "required",
            "zip" => "required",
            "mobile" => "required",
        ]);

        if ($validator->fails()) {
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




        if ($request->payment_method == 'cod') {

            $shipping = 0;
            $discount = 0;
            $discountCode = "";
            $subTotal = Cart::subtotal(2, '.', '');
            $grandTotal = $subTotal + $shipping;
            $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();

            if(session()->has("code"))
            {
                $code = session()->get("code");
    
                if($code->type=="percent")
                {
                    $discount = ($code->discount_amount/100)*$subTotal;
                }
                else
                {
                    $discount = $code->discount_amount;
                }

                $discountCode = $code->code;
            }

            //Tinh tien ship
            if($customerAddress != "")
            {
                $shippingInfor = ShippingCharge::where("country_id", $request->country)->first();
            
                $totalQty = 0;
                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }
    
                if ($shippingInfor != null) {
                    $shipping = $shippingInfor->amount * $totalQty;
                    $grandTotal = ($subTotal - $discount) + $shipping;
    
                } else {
                    $shippingInfor = ShippingCharge::where("country_id", "rest_of_the_world")->first();
    
                    $shipping = $shippingInfor->amount * $totalQty;
                    $grandTotal = ($subTotal - $discount) + $shipping;
                }
            }
            else{
                $grandTotal = Cart::subtotal(2,'.','');
                $shipping = 0;
            }   
          
            

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
            $order->status = "pending";
            $order->payment_status = "Not Paid";
            //// Discount
            $order->discount = $discount;
            $order->coupon_code = $discountCode;
            ////
            $order->apartment = $request->apartment;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->notes = $request->notes;
            $order->country_id = $request->country;
            $order->city = $request->city;
            $order->save();

            
            

            //Luu OrderItems
            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;

                //Tru vao stock
                $product = Product::find($item->id);
                $product->qty = $product->qty - $item->qty;
                $product->save();

                $orderItem->save();     
            }

            session()->forget("code");
            Cart::destroy();
        }
        $request->session()->flash("success", "Đặt hàng thành công");

        return response()->json([
            "message" => "Save successfully",
            "orderId" => $order->id,
            "status" => true,

        ]);

    }

    public function thankyou($id)
    {
        return view(
            "front.Thankyous",
            [
                "id" => $id,
            ]
        );
    }

    public function getOrderSummary(Request $request)
    {
        $total = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountString = "";
        
        $total = Cart::subtotal(2,".","");

        if(session()->has("code"))
        {
            $code = session()->get("code");

            if($code->type=="percent")
            {
                $discount = ($code->discount_amount/100)*$total;
            }
            else
            {
                $discount = $code->discount_amount;
            }
            
            $discountString = '<div class="mt-4" id="discount-response">
            <strong id="coupon_code" name="coupon_code">'. session()->get('code')->code.'</strong>
            <a class="btn btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
            </div>';
        }
        

        if (ShippingCharge::where("country_id",$request->country_id)->first() != null) {

            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            $shippingInfor = ShippingCharge::where("country_id", $request->country_id)->first();

            if ($shippingInfor != null) {
                $shippingCharge = $shippingInfor->amount * $totalQty;
                $grandTotal = ($total - $discount) + $shippingCharge;

                return response()->json([
                    "status" => true,
                    "shippingCharge" => number_format($shippingCharge),
                    "discount" => number_format($discount),
                    "discountString" => $discountString,
                    "grandTotal" => number_format($grandTotal),
                ]);
            } else {
                $shippingInfor = ShippingCharge::where("country_id", "rest_of_the_world")->first();

                $shippingCharge = $shippingInfor->amount * $totalQty;
                $grandTotal = ($total - $discount) + $shippingCharge;

                return response()->json([
                    "status" => true,
                    "shippingCharge" => number_format($shippingCharge),
                    "discount" => number_format($discount),
                    "discountString" => $discountString,
                    "grandTotal" => number_format($grandTotal),
                ]);
            }
        } else {
            return response()->json([
                "status" => true,
                "shippingCharge" => 0,
                "discount" => number_format($discount),
                "discountString" => $discountString,
                "grandTotal" => number_format(($total - $discount)),
            ]);
        }
    }

    public function applyDiscount(Request $request)
    {
        $code = Coupon::where("code",$request->code)->first();

        if($code==null)
        {
            return response()->json([
                "status" => false,
                "message" => "Invalid coupon",
            ]);
        }

        $now = Carbon::now();
        if($code->starts_at!= "")
        {
            $startDate = Carbon::createFromFormat("Y-m-d H:i:s",$code->starts_at);

            if($now->lt($startDate))
            {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid coupon",
                ]);
            }
        }

        if($code->expires_at!= "")
        {
            $endDate = Carbon::createFromFormat("Y-m-d H:i:s",$code->expires_at);

            if($now->gt($endDate))
            {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid coupon",
                ]);
            }
        }

        //Check max used 
        $usedCoupon = Order::where("coupon_code",$code->name)->count();
        if($usedCoupon >= $code->max_uses)
        {
            return response()->json([
                "status" => false,
                "message" => "Invalid coupon",
            ]);
        }
        //Check max used user 
        $usedUserCoupon = Order::where(["coupon_code" => $code->name,"user_id" => Auth::user()->id])->count();
        if($usedUserCoupon >= $code->max_uses_user)
        {
            return response()->json([
                "status" => false,
                "message" => "You already used this coupon",
            ]);
        }
        
        
        if(Cart::subtotal(2,".","") < $code->min_amount)
        {
            return response()->json([
                "status" => false,
                "message" => "Your total must be aleast " . number_format($code->min_amount) . " VND to use this coupon",
            ]);
        }

        session()->put("code",$code);

        return $this->getOrderSummary($request);
    }

    public function removeCoupon(Request $request)
    {
        session()->forget("code");
        return $this->getOrderSummary($request);
    }
}
