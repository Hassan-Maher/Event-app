<?php

namespace App\Http\Controllers\Api\user;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ItemsResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\UserOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Pest\Plugins\Profile;

class ProfileController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        return ApiResponse::sendResponse(200 , 'data retrieved successfully' , new UserResource($user));
    }
    public function index_orders(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'in:accepted,waiting,rejected,semi_accepted',
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $user = $request->user();
        $orders = $user->orders;
        if($request->has('status'))
        {
            $orders = $user->orders()->where('status' , $request->status)->get();
        }

        return ApiResponse::sendResponse(200 , 'orders retrieved successfully' , OrderResource::collection($orders));
    }

    public function index_items(Request $request , $order_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'in:accepted,pending,rejected',
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $user = $request->user();
        $order = Order::findOrFail($order_id);
        if($user->id != $order->user_id)
            return ApiResponse::sendResponse(403 , 'for bidden' , []);

        $items = $order->items()->with(['product' , 'package'])->get();

        if($request->has('status'))
        {
            $items = $order->items()->where('status' , $request->status)->get();
        }

        return ApiResponse::sendResponse(200, 'items retrieved successfully' , ItemsResource::collection($items));
    }

    public function update_profile(ProfileRequest $request)
    {
        $user = $request->user();

        $validated_data = $request->validated();

        $record  = $user->update($validated_data);
        if($record)
            return ApiResponse::sendResponse(200 , 'profile updated successfully' , new UserResource($user));
    }

    public function change_password(Request $request)
    {
        $user   = $request->user();
        $validator = Validator::make($request->all(), [
        'current_password' => ['required', 'string', 'min:8'],
        'new_password' =>     ['required', 'string', 'min:8', 'confirmed']
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        if(!Hash::check($request->current_password , $user->password))
        {
            return ApiResponse::sendResponse(403 , 'current_password is incorrect' , []);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return ApiResponse::sendResponse(200 , 'password changed successfully' , []);
    }

    public function change_phone(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
        'new_phone' => ['required', 'string', 'regex:/^\+\d{1,4}[0-9]{7,12}$/', Rule::unique('users' , 'phone')->ignore($user->id)],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        if($user->phone == $request->new_phone)
        {
            return ApiResponse::sendResponse(403 , 'enter new phone this phone is already for you' , []);
        } 

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
            return ApiResponse::sendResponse(200 , 'otp is send successfully' , ['phone' => $request->new_phone , 'otp' => $new_otp]);

    }

    public function verify_change_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required' , 'digits:6'],
            'new_phone' => ['required', 'string', 'regex:/^\+\d{1,4}[0-9]{7,12}$/']
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        $user = $request->user();


        $otp = UserOtp::where(['user_id'=> $user->id , 'code'=> $request->code])->first();

        if (!$otp) {
            return ApiResponse::sendResponse(400, 'Code is not valid', []);
        }

        if ($otp->expires_at < now()) {
            return ApiResponse::sendResponse(400, 'The verification code has expired', []);
        }

        $user->update(['phone' => $request->new_phone]);
        $otp->delete();

        return ApiResponse::sendResponse(200 , 'phone updated successfully' , []);
    }
}
