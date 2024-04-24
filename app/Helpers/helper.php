<?php 

use App\Models\Category;

function getCategories()
{
    return Category::orderBy("name","asc")
        ->with('sub_category')
        ->orderBy('id','desc')
        ->where('status','1')
        ->where('showHome','Yes')
        ->get();
}
 