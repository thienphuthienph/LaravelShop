<?php

namespace App\Http\Controllers;

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
                return redirect()->route("front.home");
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
        return view("front.account.profile");
    }

    public function logout()
    {
        Auth::logout();
        return redirect()
            ->route("account.login")
            ->with("success","Logout successfully");
    }
}
