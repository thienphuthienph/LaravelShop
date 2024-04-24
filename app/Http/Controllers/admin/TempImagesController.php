<?php

namespace App\Http\Controllers\admin;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        if($request->image)
        {
            $image = $request->image;
            $ext = $image ->getClientOriginalExtension();
            $newName = time().".".$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp', $newName);
            
            //Tao thumbnail
            $manager = new ImageManager(new Driver());

            $sourcePath = public_path().'/temp/'.$newName;
            $destPath = public_path().'/temp/thumb/'.$newName;
            $img = $manager->read($sourcePath);
            $img->resize(300,275);
            $img->save($destPath);

            return response()->json([
                'status' => true,
                'img_id' => $tempImage->id,
                'ImgPath' => asset('/temp/thumb/'.$newName),
                'message' => 'image uploade successfully'
                ]);
        }
    }
}
