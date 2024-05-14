<?php

namespace App\Http\Controllers\admin;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $discountCoupons = Coupon::query();
        
        if (!empty($request->get("keyword"))) {
            $discountCoupons = $discountCoupons->where("name", "like", "%" . $request->get("keyword") . "%");
        }
        $discountCoupons = $discountCoupons->orderBy('created_at','desc')->paginate(10);
        return view("admin.coupons.list",compact("discountCoupons"));
    }

    public function create()
    {
        return view("admin.coupons.create");
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "name" => "required",
                "code" => "required",
                "discount_amount" => "required|numeric",
                "status" => "required"
            ]
        );

        if ($validator->passes()) {
            if (!empty($request->start_at)) {
                $now = Carbon::now();

                $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->start_at);
                if ($startAt->lte($now) == true) {
                    return response()->json([
                        "status" => false,
                        "errors" => ["start_at" => "Start date can not be less than current time"],
                    ]);
                }
            }

            $coupon = new Coupon();
            $coupon->name = $request->name;
            $coupon->code = $request->code;
            $coupon->description = $request->description;
            $coupon->max_uses = $request->max_uses;
            $coupon->max_uses_user = $request->max_uses_user;
            $coupon->type = $request->type;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->min_amount = $request->min_amount;
            $coupon->status = $request->status;
            $coupon->starts_at = $request->start_at;
            $coupon->expires_at = $request->expire_at;
            $coupon->save();

            $request->session()->flash("success", "Coupon created successfully");
            return response()->json([
                "status" => true,
                "message" => "coupon create successfully",
            ]);

        } else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }
    }

    public function edit($id,Request $request)
    {
        $coupon = Coupon::find($id);

        if($coupon == null)
        {
            return response()->json([
                "status" => false,
                "message" => "Record not found"
            ]);
        }

       return view("admin.coupons.edit",compact("coupon"));
    }

    public function update($id,Request $request)
    {
        $coupon = Coupon::find($id);
        $validator = Validator::make(
            $request->all(),
            [
                "name" => "required",
                "code" => "required",
                "discount_amount" => "required|numeric",
                "status" => "required"
            ]
        );

        if ($validator->passes()) {
            if (!empty($request->start_at)) {
                $now = Carbon::now();

                $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->start_at);
                if ($startAt->lte($now) == true) {
                    return response()->json([
                        "status" => false,
                        "errors" => ["start_at" => "Start date can not be less than current time"],
                    ]);
                }
            }

            $coupon->name = $request->name;
            $coupon->code = $request->code;
            $coupon->description = $request->description;
            $coupon->max_uses = $request->max_uses;
            $coupon->max_uses_user = $request->max_uses_user;
            $coupon->type = $request->type;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->min_amount = $request->min_amount;
            $coupon->status = $request->status;
            $coupon->starts_at = $request->start_at;
            $coupon->expires_at = $request->expire_at;
            $coupon->save();

            $request->session()->flash("success", "Coupon created successfully");
            return response()->json([
                "status" => true,
                "message" => "coupon create successfully",
            ]);

        } else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }
    }

    public function destroy($id,Request $request)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();

        $request->session()->flash("success","Category delete successfully");
        return redirect()->route("coupons.index");
    }
}
