<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Whoops\Handler\PlainTextHandler;

class AuthController extends Controller
{
    use HttpResponses;
    public function login(LoginUserRequest $request){
        $request->validate([$request->all()]);  

        if(!Auth::attempt($request->only(["email","password"]))){
            return $this->error('','credential donot exists',401);
        }
         $user=User::where('email', $request->email)->first();
        return $this->success([
            'user'=> $user,
            'token'=>$user->createToken('Api token of'.$user->name)->plainTextToken
        ]);
        
    }



    public function register(StoreUserRequest $request){
        $request->validate([$request->all()]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password),
            ]);

        return $this->success([
            'user'=>$user,
            'token'=>$user->createToken('Api token of'. $user->name)->plainTextToken
        ]);
    }



    public function logout(Request $request){
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
           'message'=>'you have been successfully logged out',
        ]);
    }
}
