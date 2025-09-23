<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\StoreResource;
use App\Http\Resources\UserResource;
use App\Models\City;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function Register(RegisterRequest $request)
    {
        $validated_date = $request->validated();
        $validated_date['password'] = Hash::make($validated_date['password']);
        $validated_date['is_verified'] = false;
        $validated_date['phone'] = ($request->country_code) . ($validated_date['phone']);

        $user = User::create($validated_date);

        $otp = UserOtp::create([
        'user_id'   => $user->id,
        'code'  => rand(100000, 999999),
        'expires_at'=> now()->addMinutes(5),
        'is_verified' => false,

        ]);
        

        $code = $otp->code;
        if($user)
            return ApiResponse::sendResponse(201 , 'please verify your code',[ 'user' => new UserResource($user) , 'code' => $code]);
    }

    public function verifyRegisterOtp(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'code' => ['required' , 'digits:6'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }


        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return ApiResponse::sendResponse(404, 'User not found', []);
        }

        $otp = UserOtp::where('user_id', $user->id)->where('code', $request->code)->first();


        if (!$otp) {
            return ApiResponse::sendResponse(400, 'Code is not valid', []);
        }

        if ($otp->expires_at < now()) {
            return ApiResponse::sendResponse(400, 'The verification code has expired', []);
        }

        $user->update(['is_verified' => true]);
        $token = $user->createToken('registerToken')->plainTextToken;

        $otp->delete();

        return ApiResponse::sendResponse(200, 'Code verified successfully', [
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function resendOtp(Request $request)
    {
         $request->validate([
            'phone' => 'required'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if(!$user)
            return ApiResponse::sendResponse(404  , 'User Not Found' , []);

        $new_otp = rand(100000, 999999);

       $record = UserOtp::updateOrCreate(
        ['user_id' => $user->id],
        [
            'code'       => $new_otp,
            'expires_at' => now()->addMinutes(5),
            'is_verified' => false,
        ]
        );
        if($record)
            return ApiResponse::sendResponse(200 , 'code resend successfully' , [
            'user'  => new UserResource($user),
            'new_otp' => $new_otp,
            ]);
    }

    public function login(LoginRequest $request)
    {

        $validated_data = $request->validated();

        if(! Auth::attempt($validated_data))
        {
           return ApiResponse::sendResponse(404 , 'data not valid' , []);
        }

        $user = Auth::user();
         

        if(! $user->is_verified)
        {
            $new_otp = rand(100000, 999999);
            
            $record = UserOtp::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'code'       => $new_otp,
                    'expires_at' => now()->addMinutes(5),
                    'is_verified' => false,
                    ]
                );
                return ApiResponse::sendResponse(401,'user not verified' , ['is_verified' => $user->is_verified , 'otp' => $new_otp]);

        }

        if(! $user->store)
        {

            $token = $user->createToken('loginToken')->plainTextToken;

            return ApiResponse::sendResponse(403  , 'you dont have a store please complete your store data' , ['user' => new UserResource($user) , 'has_store' => false , 'token' => $token]); 
        }
        
        $token = $user->createToken('loginToken')->plainTextToken;

        return ApiResponse::sendResponse(200 , 'login successfully' , [
            'user' => new UserResource($user),
            'token'=> $token
        ]);

        

    }


    public function resetPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^05[0-9]{8}$/']
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        $user = User::where('phone' , $request->phone)->first();
        if(! $user)
            return ApiResponse::sendResponse(404 , 'phone is  not valid' , []);

        $otp = rand(100000, 999999);

        $record = UserOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
            'code'       => $otp,
            'expires_at' => now()->addMinutes(5),
            'is_verified' => false,
            ]
        );

        if($record)
            return ApiResponse::sendResponse(200 , 'code resend successfully' , [
            'user'  => new UserResource($user),
            'otp' => $otp,
            ]);
    }

    public function verifyResetOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^05[0-9]{8}$/'],
            'code' => ['required' , 'digits:6'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return ApiResponse::sendResponse(404, 'User not found', []);
        }

        $otp = UserOtp::where('user_id', $user->id)->where('code', $request->code)->first();


        if (!$otp) {
            return ApiResponse::sendResponse(400, 'Code is not valid', []);
        }

        if ($otp->expires_at < now()) {
            return ApiResponse::sendResponse(400, 'The verification code has expired', []);
        }
        $otp->update(['is_verified' => true]);

        
        return ApiResponse::sendResponse(200 , 'verify code successfully' , ['canReset' => true]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => ['required' , 'regex:/^05[0-9]{8}$/'],
            'password' => ['required', 'confirmed' , 'min:8'],
            
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        $user = User::where('phone' , $request->phone)->first();
        if(!$user)
            return ApiResponse::sendResponse(404 , 'user not found' , []);

        if(empty($user->otp) || ! $user->otp->is_verified)
        {
            return ApiResponse::sendResponse(200 , 'please verify your code' , []);    
        }

        $record = $user->update([
                'password' => Hash::make($request->password)
            ]);
        
        if($record)
        {
            return ApiResponse::sendResponse(200, 'password has been  reset successfylly' , [
                'user' => new UserResource($user),
            ]);
        }
    }



    // get all cities 
    public function index()
    {
        $cities = City::get();
        return ApiResponse::sendResponse(200,'cities retrievd successfully' , CityResource::collection($cities));
    }







}
