<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    public function register(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|max:190|unique:users',
            'password'=>'required|min:5',
            ]);
        if($validator->fails()){

            return response()->json([
                'validation_errors'=>$validator->messages()
            ]);

        }else{
            $user=User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> Hash::make($request->password),
            ]);
            $token=$user->createToken($request->email.'_token', ['server:update'])->plainTextToken;
        }
        return response()->json([
                'status'=>'200',
                'username'=>$request->name,
                'token'=>$token,
                'message'=>'Registered Successfully.',
            ]);

    }

    public function login(Request $request){
        
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required',

        ]);

        if($validator->fails()){

            return response()->json([
                'validation_errors'=>$validator->messages()
            ]);

        }else{
            $user=User::where('email',$request->email)->first();

            if(! $user || ! Hash::check($request->password,$user->password)) {

                return response()->json([
                'status'=>'401',
                'message'=>'Invalid Credentials',
              ]);

            }else{

                $token=$user->createToken($request->email.'_token', ['server:update'])->plainTextToken;

                return response()->json([
                'status'=>'200',
                'userDetails'=>$user,
                'token'=>$token,                
                'messgae'=>'Logged In Successfully',
            }            
        }
    }


}
