<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $totalOrder = Order::all()->count();
        $totalCustomer = CustomerAddress::all()->count();
        $totalSale = Order::where("status","delivered")->sum("grand_total");
        return view(
            "admin.dashboard",
            [
                "totalOrder" => $totalOrder,
                "totalCustomer" => $totalCustomer,
                "totalSale" => $totalSale,
            ]
        );
    }

    public function logout()
    {
        Auth::guard("admin")->logout();
        return redirect()->route("admin.login");
    }
}
