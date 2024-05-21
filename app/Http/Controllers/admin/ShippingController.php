<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function create()
    {
        $countries = Country::get();
        $data["countries"] = $countries; 

        $shippingCharge = ShippingCharge::select('shipping_charges.*','countries.name')
            ->leftJoin("countries","countries.id","shipping_charges.country_id")
            ->orderBy("country_id","desc")
            ->get();


        $data["shippingCharge"] = $shippingCharge;

        return view("admin.shipping.create",$data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            "country" => "required",
            "amount" => "required|numeric"
        ]);

        if($validator->passes())
        {
            $count = ShippingCharge::where("country_id",$request->country)->count();

            if($count >0)
            {
                session()->flash("error","Shipping already added");
                return response()->json([
                    "status" => true,
                ]);
            }

            $shipping = new ShippingCharge;
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            $request->session()->flash("success","create new shipping successfully");

            return response()->json([
                "status" => true,
                "message" => "success",
            ]);
        }
        else{
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }
    }

    public function edit($id)
    {
        $shippingCharge = ShippingCharge::find($id);
        $countries = Country::get();

        if(!empty($shipping))
        {
            
        }
        $data["shippingCharge"] = $shippingCharge;
        $data["countries"] = $countries;
        return view("admin.shipping.edit",$data);
    }

    public function update($id,Request $request)
    {
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount'   => 'required|numeric'
        ]);

        if($validator->passes())
        {
            $shipping = ShippingCharge::find($id);
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();
    
            $request->session()->flash("success","shipping charge updated successfully");
    
            return response()->json([
                "status" => true,
                "id"    => $id,
            ]);
        }

       
    }

    public function destroy($id)
    {
        $shippingCharge = ShippingCharge::find($id);
        $shippingCharge->delete();

        session()->flash("success","shipping charge delete successfully");
    
        return response()->json([
            "status" => true,
        ]);

    }
}
