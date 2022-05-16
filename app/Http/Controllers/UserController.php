<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;    
use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail; 

class UserController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }

            try {
                $code = $this->generateCode();
                $this->saveOtpCode($code);
                $sendOTP = $this->sendOtp($request->email, $code);
            } catch (JWTException $e) {
                return response()->json(['error sending opt', 500]);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(
            [
                "message" => "OTP sent to your email successfully. Please verify to continue.",
                "token" => $token
            ], 200
        );
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), 
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name'      => $request->get('name'),
            'email'     => $request->get('email'),
            'password'  => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'), 201);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    public function logout(Request $request) {

        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function generateCode(){
        return mt_rand();
    }
    
    public function sendOtp($email, $code){
        try {

            Mail::send('auth.mailotp', ['code' => $code], function ($message) use ($email) {
                $message->subject('Verify Account Restaurant Bot');
                $message->from('dewa.ariandy@gmail.com', 'Restaurant Bot');
                $message->to($email);
            });

        } catch (Exception $e) {
            return response()->json(
                [
                    "message" => "Failed Send OTP",
                ], 500
            );
        }
    }

    protected function saveOtpCode($code) {
        $user = Auth::user();
        $user->remember_token = $code;
        $user->save();
    }

    protected function verifyOtp(Request $request) {
        $user = User::where('email', $request->email)->first();
        if ($user->remember_token === $request->code) {
            $user->email_verified_at = now();
            $user->save();

            return response()->json(['message' => 'Account successfuly verified'], 200);
        }
        return response()->json(
            ['message'=> 'Invalid OTP Provided'], 
            500
        );
    }
}
