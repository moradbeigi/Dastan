<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Events\Verified;
use App\Notifications\VerifyApiEmail;


class RegisterController extends Controller
{
    use VerifiesEmails;
    public  $successStatus = 200;

    public function register (Request $request) {
        $Roules = [
            'name' => 'required', 
            'email' => 'required|email', 
            'photo_name' => ['sometimes','image', 'mimes:jpg,jpeg, png', 'max:5000'],
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ];
        $validator = Validator::make($request->all(),$Roules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()],422);
        }

        if(request()->has('photo_name')){
            $photouload = request()->file('photo_name');
            $photoname = time() . '.' . $photouload->getClientOriginalExtension();
            $photopath = public_path('/images/');
            $photouload->move($photopath,$photoname);
        }

        $input = $validator->validated();
        $input['password'] = bcrypt($input['password']);
        $input['photo_name'] = '/images/' . $photoname;
        $user = User::create($input);

        $user->sendApiEmailVerificationNotification();

        $success['message'] = 'Please confirm yourself by clicking on verify user button sent to you on your email';
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['name'] =  $user->name;

        return response()->json(['success'=>$success], $this-> successStatus); 
    }

    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 

            if($user->email_verified_at !== NULL){
             $success['message'] = 'Login successfully';
             $success['token'] =  $user->createToken('MyApp')-> accessToken; 
             $success['email_verified_at'] = $user->email_verified_at;

            return response()->json(['success' => $success], $this-> successStatus); 
        }   else {
            return response()->json('please veryfi your account');
        }

        }
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus);
    }
}
