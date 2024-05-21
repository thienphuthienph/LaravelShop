<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy("id","asc")->get();
        
        if (!empty($request->get("keyword"))) {
            $users = $users->where("name", "like", "%" . $request->get("keyword") . "%");
        }

        $data["users"] = $users;
        return view("admin.users.index",$data);
    }  

    public function edit($id)
    {   
        $user = User::find($id);

        return view("admin.users.edit",compact("user"));
    }

    public function update($id, Request $request)
    {
        $user = User::find($id);

        if(empty($user))
        {
            session()->flash("fail","User not found");
            return response()->json([
                "status" => false,
                "message" => "User not found"
            ]);
        }

        $validator = Validator::make($request->all(),
        [
            "name" => "required",
            "email" => "required",
            "phone" => "required",
            "status" => "required",
            "role"  => "required"
        ]);

        if($validator->passes())
        {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role = $request->role;
            $user->status = $request->status;   
            $user->save(); 
            session()->flash("success","User updated successfully");
            return response()->json([
                "status" => true,
                "message" => $validator->errors(),
            ]);
        }
        else{
            session()->flash("error","User updated fail");
            return response()->json([
                "status" => false,
                "message" => $validator->errors(),
            ]);
        }
    }

    public function delete($id)
    {
        $user = User::find($id);

        if(empty($user))
        {
            return response()->json([
                "status" => true,
                "message" => "User not found",
            ]);
        }
        else
        {
            $user->delete();
        }
    }
}
