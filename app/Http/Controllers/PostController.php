<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Post;
use Validator;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Posts = Post::orderBy('id','DESC')->get();
        $data = PostResource::collection($Posts);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $roules = [
            'title'   => 'required',
            'content' => 'required',
            'image'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'comment' => 'required'
        ];

        $validator = Validator::make($request->all(),$roules);
        if($validator->fails()){
            return response()->json(['error' => $validator->error()],422);
        }

        $photoUpload = request()->file('image');
        $photoName = time() . '.' . $photoUpload->getClientOriginalExtension();
        $photoPath = public_path('/images/Posts/');
        $photoUpload->move($photoPath,$photoName);

        $inpute= $validator->validated();

        $inpute['image'] = '/images/Posts/' . $photoName;

        $Posts = Post::create($inpute);
        $data = new PostResource($Posts);

        return response()->json($data,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Posts = Post::find($id);
        $data = new PostResource($Posts);
        if(is_null($Posts)){
            return response()->json('id not found',404);
        }
        return response()->json($data,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Posts = Post::find($id);
        $data = new PostResource($Posts);

        if(is_null($Posts)){
            return response()->json('id not found',404);
        }

        $Posts->update($request->all());
        return response()->json($data,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $Posts = Post::find($id);
        $data = new PostResource($Posts);
        if(is_null($Posts)){
            return response()->json('id not found',404);
        }

        $data->delete($request->all());
        return response()->json('record delete');
    }
}
