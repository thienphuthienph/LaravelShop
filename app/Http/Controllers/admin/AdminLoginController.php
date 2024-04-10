<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AdminLoginController extends Controller
{
    public function index()
    {
        return view("admin.login");
    }

    public function authenticate(Request $request)
    {
        $validator = validator($request->all(), [
            "email"=> "required|email",
            "password"=> "required"
            ]);

        if ($validator->passes())
        {
            if(Auth::guard("admin")->attempt(["email" => $request->email,"password" => $request->password],$request->get('remember')))
            {
                $admin = Auth::guard('admin')->user();
                if($admin->role == 2)
                {
                    return redirect()->route("admin.dashboard");
                }
                else
                {
                    Auth::guard("admin")->logout();
                    return redirect()->route("admin.login")->with("error","Bạn không có quyền truy cập vào trang này");
                }
                    
            }
            else
            {
                return redirect()->route("admin.login")->with("error","Sai mật khẩu hoặc email");
            }
        }
        else
        {
            return redirect()->route("admin.login")
                ->withErrors($validator)
                ->withInput($request->only("email"));
        }

    }

    public function logout()
    {
        Auth::guard("admin")->logout();
        return redirect()->route("admin.login");
    }
}
