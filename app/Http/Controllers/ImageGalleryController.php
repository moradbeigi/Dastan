<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageGalleryResource;
use Illuminate\Http\Request;
use App\ImageGallery;
use Validator;

class ImageGalleryController extends Controller
{
      /**
     * Listing Of images gallery
     *
     * @return \Illuminate\Http\Response
     */
    public function index () {
        
        $images = ImageGallery::orderBy('id','DESC')->get();
        $data = ImageGalleryResource::collection($images);
        return response()->json($data);
    }


     /**
     * Upload image function
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $Roules = [
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        $validator = Validator::make($request->all(),$Roules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()],422);
        }

        $photoUpload = request()->file('image');
        $photoName = time() . '.' . $photoUpload->getClientOriginalExtension();
        $photoPath = public_path('/images/Gallery/');
        $photoUpload->move($photoPath,$photoName);

        $inpute= $validator->validated();
        
        $inpute['image'] = '/images/Gallery/' . $photoName;

        $ImageGallery = ImageGallery::create($inpute);
        //$success['image'] = $ImageGallery->image;

        $data = new ImageGalleryResource($ImageGallery);

        return response()->json($data,200);

    }

     /**
     * Remove Image function
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy (Request $request , $id) {
        
        $ImageGallery = ImageGallery::find($id);
        $data = new ImageGalleryResource($ImageGallery);
        if(is_null($ImageGallery)){
            return response()->json(['message' => 'record not found'],404);
        }

        $data->delete($request->all());
        return response()->json(['message' => 'record delete'],200);
    }
}
