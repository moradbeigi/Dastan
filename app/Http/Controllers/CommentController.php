<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Comment;
use App\User;
use Validator;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comment = Comment::orderby('id','DESC')->get();
        $data = CommentResource::collection($comment);
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
        $Roules = [
            'comment' => 'required'
        ];
        $validator = Validator::make($request->all(),$Roules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()],422);
        }
 
        $comment = new Comment ([
            'comment' => $request->get('comment'),
            'user_id' => auth()->id()
        ]);

        $comment->save();

        $data = new CommentResource($comment);

        return response()->json($data,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(auth()->user()->isAdmin == 1){
            $comment = Comment::find($id);
            $data = new CommentResource($comment);
        
        if (is_null($comment)) {
            return response('id not found');
        }
        
        return response()->json($data);
    }
    else {
        return response()->json('you atrnt admin');
    }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user =\Auth::User();
        $comment = Comment::orderBy('id','DESC')->where('user_id', $user->id)->find($id);

        if(is_null($comment)){
            return response()->json('id not found!!!!', 404);
        }

        $comment->update($request->all());

        $data = new CommentResource($comment);

        return response()->json($data,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(auth()->user()->isAdmin == 1){
            $comment = Comment::orderBy('id','DESC')->find($id);
            
        $comment->delete($request->all());

        return response()->json('record delete'); 
        }
        else {
        $user =\Auth::User();
        $comment = Comment::orderBy('id','DESC')->where('user_id', $user->id)->find($id);

        if(is_null($comment)){
            return response()->json('id not found!!!!', 404);
        }

        $comment->delete($request->all());

        return response()->json('record delete'); 
    }}
}
