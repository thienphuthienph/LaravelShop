<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\Order;
class OrderController extends Controller
{
    public function list(Request $request)
    {
        $orders = Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders = $orders->leftJoin('users','users.id','orders.user_id');

        if($request->get('keyword') != '')
        {
            $orders = $orders->where('users.name','like','%'.$request->keyword.'%');
            $orders = $orders->orWhere('users.email','like','%'.$request->keyword.'%');
            $orders = $orders->orWhere('users.id','like','%'.$request->keyword.'%');
        }
        
        $orders = $orders->paginate(10);


        return view("admin.order.index",[
            'orders' => $orders
        ]);
    }

    public function detail($id)
    {
        $order = Order::select('orders.*','countries.name as countryName')
            ->where('orders.id',$id)
            ->leftJoin('countries','countries.id','orders.country_id')
            ->first();
        $orderItem = OrderItem::where('order_id',$id)->get();

        return view('admin.order.detail',[
            'order' => $order,
            'orderItem' => $orderItem
        ]);
    }

    public function changeOrderStatus(Request $request,$id)
    {
        $order = Order::find($id);
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();

        session()->flash('status','Order updated successfully');

        return response()->json([
            "status" => true,
            "message" => "Update successfully"
        ]);
    }
    
    public function sendInvoice()
    {
        echo 12;
    }
}
