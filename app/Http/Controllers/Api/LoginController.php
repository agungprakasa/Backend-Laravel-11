<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Session;
// use JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('email', 'password');

        //if auth failed
        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }
        //if auth success
            $email = $request->email;
            $cekrole = DB::table('users')->select('role','name','email')->where('remember_token',session('token'))->get();
            // dd($cekrole);
            $payload = JWTFactory::sub($cekrole)
                ->Data($cekrole)
                // ->myCustomArray(['Apples', 'Oranges'])
                // ->myCustomObject($cekrole)
                ->make();
            $token1 = JWTAuth::encode($payload)->get();
                try {
                    // attempt to verify the credentials and create a token for the user
                     //decode token
                    $token2 = JWTAuth::encode($payload);
                    $apy = JWTAuth::decode($token2)->toArray();
                } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            
                    return response()->json(['token_expired'], 500);
            
                } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            
                    return response()->json(['token_invalid'], 500);
            
                } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            
                    return response()->json(['token_absent' => $e->getMessage()], 500);
            
                }

            // $user = DB::select("UPDATE users set remember_token = '$token' where email = '$email' ");
            $user = DB::table('users')->where('email',$email)->update(['remember_token' => $token1]);
            return response()->json([
                'success' => true,
                'user'    => auth()->guard('api')->user(),    
                'token'   => $token1
            ], 200);
    }
}