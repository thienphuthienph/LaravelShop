<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::query();
        //$pages = Page::all();

        if(!empty($request->keyword))
        {
            $pages =  $pages->where("name", "like", "%" . $request->get("keyword") . "%");
        }

        $pages = $pages->orderBy("created_at","desc")->paginate(10);
        return view("admin.pages.index",compact("pages"));
    }

    public function create()
    {
        return view("admin.pages.create");
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "name" => "required|unique:pages",
            "slug" => "required|unique:pages",
        ]);

        if($validator->passes())
        {
            $page = new Page();
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();

            session()->flash("success","Page created successfully");
            return response()->json([
                "status" => true,
                "message" => "Page saved successfully"
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
        $page = Page::find($id);

        return view("admin.pages.edit",[
            "page" => $page,
        ]);
    }

    public function update($id,Request $request)
    {
        $page = Page::find($id);

        $validator = Validator::make($request->all(),[
            "name" => "required",
            "slug" => "required",
        ]);

        if($validator->passes())
        {
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();

            $request->session()->flash("success","Page updated successfully");

            return response()->json([
                "status" => true,
                "message" => "Page updated successfully"
            ]);

        }else{

            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }

    }

    public function destroy($id)
    {
        $page = Page::find($id);

        if(!empty($id))
        {
            $page->delete();
            
            session()->flash("success","Page deleted successfully");
            return response()->json([
                "status" => true,
                "message" => "Page deleted successfully"
            ]);
        }
        else
        {
            session()->flash("fail","Page not found");
            return response()->json([
                "status" => false,
                "message" => "Page not found"
            ]); 
        }
    }

}
