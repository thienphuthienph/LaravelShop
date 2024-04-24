<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;


class ProductImageController extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image ->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();

        $imageName                = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        $productImage->image      = $imageName;
        $productImage->save();

        //Large Image
        $manager = new ImageManager(new Driver());
        $desPath = public_path().'/uploads/product/large/'.$imageName;
        $image = $manager->read($sourcePath);
        $image->scale(1400, null);
        $image->save($desPath);

        //Small Image
        $desPath = public_path().'/uploads/product/small/'.$imageName;
        $image = $manager->read($sourcePath);
        $image->resize(300,300);
        $image->save($desPath);

        return response()->json([
            "status" =>  true,
            "image_id"=> $productImage->id,
            "ImgPath" => asset("uploads/product/small".$productImage->image),
            "message"=> "Save successfully"
            ]);
    }

    public function destroy(Request $request)
    {
        $productImage = ProductImage::find($request->id);

        if(empty($productImage))
        {
            return response()->json([
                "status" =>  false,
                "message"=> "Product not found"
                ]);
        }
        //Xoa hinh trong folder 
        File::delete(public_path("/uploads/product/large/").$productImage->image);
        File::delete(public_path("/uploads/product/small/").$productImage->image);
        $productImage->delete();

        return response()->json([
            "status" =>  true,
            "message"=> "Delete successfully"
            ]);
    }
}
