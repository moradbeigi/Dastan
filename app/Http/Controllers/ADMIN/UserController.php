<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Validator;
use Illuminate\Http\Request;
use Laminas\Diactoros\Response; 

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->isAdmin == 1){
            $getUser = User::get();
        return response()->json($getUser);
        }
        else {
            $getUser = \Auth::user();
            $data = new UserResource($getUser);
            return response()->json($data);
        }

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
        $roule = [
            'name'     => 'required',
            'email'    => 'required', 
            'photo_name' => ['sometimes','image', 'mimes:jpg,jpeg, png', 'max:5000'],
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(),$roule);

        if ($validator->fails()){
            return response()->json($validator->error(),422);
        }

        if(request()->has('photo_name')){
            $photouload = request()->file('photo_name');
            $photoname = time() . '.' . $photouload->getClientOriginalExtension();
            $photopath = public_path('/images/NormalUser');
            $photouload->move($photopath,$photoname);
        }

        $input = $validator->validated();

        $input['password'] = bcrypt($input['password']);
        $input['photo_name'] = '/images/NormalUser/' . $photoname;
        
        $user = User::create($input);
        return response()->json($user,200);
        }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $data = new UserResource($user);

        if (is_null($user)) {
           return response()->json('id not found',404);
        }
           return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $data = new UserResource($user);

        if (is_null($user)) {
           return response()->json('id not found',404);
        }

        $user->update($request->all());

        return response()->json($data,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $user = User::find($id);
        $data = new UserResource($user);
        if (is_null($user)) {
           return response()->json('id not found',404);
        }

        $data->delete($request->all());

        return response()->json(null,200);
    }
}
