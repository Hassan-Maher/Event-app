<?php

namespace App\Http\Controllers\Api\provider;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemsResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\StoreResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'in:pending,accepted,rejected',
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $store = $request->user()->store;

        
        $items = OrderItem::with('order:id,customer_name')->where('store_id' , $store->id)->where('status' ,$request->status)->get();


        return ApiResponse::sendResponse(200 , 'orders retrieved successfully' , ItemsResource::collection($items));
        
    }

    public function show(Request $request , $item_id)
    {
        $item = OrderItem::with(['product' , 'package'])->findOrFail($item_id);
        $order_user = $item->order->user;

        if($request->user()->store->id != $item->store_id)
        {
            return ApiResponse::sendResponse(403 , 'this item is isnt for you' , []);
        }

        return ApiResponse::sendResponse(200 , 'item retrieved successfully' ,[ 
        'item' =>     new ItemsResource($item) ,
        'user' => new UserResource($order_user)]);

    }

    public function confirm_items(Request $request , $item_id)
    {
        $validator = Validator::make($request->all(), [
            'confirm' => 'required|boolean',
            'rejected_reason' => 'nullable|string'
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $item = OrderItem::findOrFail($item_id);
        if($request->user()->store->id != $item->store_id)
        {
            return ApiResponse::sendResponse(403 , 'you are not allowed to do this action' , ['is_allowed' => false]);
        }
        if($item->status != 'pending')
        {
            return ApiResponse::sendResponse(403 , 'item already confirmed' , []);
        }
        if($request->confirm)
        {
            $item->update([
            'status' => 'accepted'
        ]);
        }
        else
        {
            $item->update([
            'status' => 'rejected',
            'rejected_reason' => $request->has('rejected_reason')? $request->rejected_reason: null
            ]);
        }
        $order = $item->order;

        $pendingCount  = $order->items()->whereNotIn('status', ['rejected', 'accepted'])->count();
        $acceptedCount = $order->items()->where('status', 'accepted')->count();
        $rejectedCount = $order->items()->where('status', 'rejected')->count();
        $totalItems    = $order->items()->count();

        if ($pendingCount > 0) {
            $orderStatus = 'waiting';
        } 
        elseif ($acceptedCount === $totalItems) {
            $orderStatus = 'accepted';
        } 
        elseif ($rejectedCount === $totalItems) {
            $orderStatus = 'rejected';
        } 
        else {
            $orderStatus = 'semi_accepted';
        }

        $order->update([
            'status' => $orderStatus
        ]);
        
       


        $store = $request->user()->store;
        
        return ApiResponse::sendResponse(200 , 'item is confirmed successfully' , ['provider' => new StoreResource($store) , 'item' => new ItemsResource($item)]);
    }


}
