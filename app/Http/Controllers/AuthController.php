<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Whishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return view("front.account.login");
    }

    public function register(Request $request)
    {
        return view("front.account.register");
    }

    public function processRegister(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "name" => "required|min:3",
            "email"=> "required|email|unique:users",
            "password"=> "required|min:5|confirmed",
            ]);
        
        if ($validation->passes())
        {  
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash("success","account created");
            
            return response()->json([
            "status"=> true,
            "message" => "account created"
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "error" => $validation->errors()
                ]);
        }
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email"=> "required",
            "password"=> "required|min:5"
            ]);
        if ($validator->passes())
        {
            if(Auth::attempt(["email" => $request->email,"password" => $request->password],$request->get('remember')))
            {
                $userStatus = Auth::user()->status;
                if($userStatus == 1)
                {
                    return redirect()->route("front.home");
                }
                else
                {
                    Auth::logout();
                    return redirect()->route("account.login")->with("error","Tài khoản của bạn đã bị cấm");
                }
                
            }
            else
            {
                $request->session()->flash('error','Email or password incorrect');
                return redirect()
                    ->route('account.login')
                    ->withInput($request->only('email'))
                    ->with('error','Email or password incorrect');
            }
        }
        else
        {
            $request->session()->flash("error","email or password cannot be null");
            return response()->json([
                "status"=> false,
                "error"=> $validator->errors()
                ]);
        }
    }

    public function profile()
    {
        $countries = Country::orderBy("name","asc")->get();

        $user = Auth::user();
        $address = CustomerAddress::where("user_id",$user->id)->first();

        return view("front.account.profile",[
            "user" => $user,
            "countries" => $countries,
            "address" => $address,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(),[
            "name" => "required",
            "email" => "required",
            "phone" => "required",
        ]);

        if($validator->passes())
        {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            session()->flash("success","account updated");
            return response()->json([
                "status" => true,
                "message" => "account updated successfully",
            ]);
        }
        else
        {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }
    }

    public function updateAddress(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            "first_name" => "required|min:5",
            "last_name" => "required",
            "email" => "required",
            "country_id" => "required",
            "address" => "required|min:20",
            "city" => "required",
            "zip" => "required",
            "mobile" => "required",
        ]);
        

        if($validator->passes())
        {
            CustomerAddress::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country_id,
    
                    'address' => $request->address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip,
                ]
            );

            session()->flash("success","account updated");
            return response()->json([
                "status" => true,
                "message" => "account updated successfully",
            ]);
        }
        else
        {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }




    }

    public function logout()
    {
        Auth::logout();
        return redirect()
            ->route("account.login")
            ->with("success","Logout successfully");
    }

    public function orders()
    {
        $accountOrders = Order::where("user_id",Auth::user()->id)->get();
        $data["accountOrders"] = $accountOrders;
        return view("front.account.order",$data);
    }

    public function orderDetail($id)
    {
        $order = Order::where("id",$id)->get();
        $orderItems = OrderItem::where("order_id",$id)->get();
        $ItemQuantity = OrderItem::where("order_id",$id)->get()->count();
        $data["orderItem"] = $orderItems;
        $data["order"] = $order;
        $data["itemQuantity"] = $ItemQuantity;
        return view("front.account.orderDetail",$data);
    }

    public function wishlist()
    {
        $whishlists = Whishlist::where("user_id",Auth::user()->id)->with("product")->get();

        $data = [];
        $data["wishlists"] = $whishlists;
        return view("front.account.wishlist",$data);

    }

    public function removeProductFromWishlist(Request $request)
    {
        $wishlist = Whishlist::where("user_id",Auth::user()->id)->where("product_id",$request->id)->first();

        if($wishlist == null)
        {
            session()->flash("error","Product already removed");

            return response()->json([
                "status" => true,
            ]);
        }
        else{
            $wishlist = Whishlist::where("user_id",Auth::user()->id)->where("product_id",$request->id)->delete();
            session()->flash("error","Product remove succcess");

            return response()->json([
                "status" => true,
            ]);
        }
    }
}
