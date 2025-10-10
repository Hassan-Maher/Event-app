<?php

namespace App\Http\Controllers\Api\user;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\ItemsResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        $validated_data = $request->validated();
        
        $validated_data['user_id'] = $request->user()->id;
        $validated_data['price'] = 1;
        $validated_data['final_price'] = 1;
        $validated_data['customer_name'] = $validated_data['customer_name']?? $request->user()->name;
        $validated_data['customer_phone'] = $validated_data['customer_phone']?? $request->user()->phone;
        
        $order = Order::create(Arr::except($validated_data, ['items']));

        foreach($request->items as $item)
        {
            if($item['type'] == 'product')
            {
                $product = Product::find($item['item_id']);
                if($product)
                {
                    $item['store_id'] = $product->store_id;
                    if(!empty($item['option_id']))
                    {
                        $option = ProductOption::find($item['option_id']);
                        $item['price'] = $option->price;
                    }
                    else{
                        $item['price'] = $product->price;
                    }
                    
                }
            }
            elseif($item['type'] == 'package')
            {
                $package = Package::find($item['item_id']);                
                $item['store_id'] = $package->store_id;
                $item['price'] = $package->final_price;
            }


            $order->items()->create($item);
        }

        $order->load('items');
        $new_price = $order->calculatePrice();
        $order->update([
            'price'       => $new_price,
            'final_price' => $request->offer? $new_price - ($new_price *$validated_data['offer']/100): $new_price
        ]);
        

        return ApiResponse::sendResponse(200, 'order stored successfully please wait to provider confirmed' , new OrderResource($order->refresh()));
    }


    public function show($order_id)
    {
        
        $order = Order::with(['items.product' , 'items.package'])->findOrFail($order_id);

        return ApiResponse::sendResponse(200 , 'order retrieved successfully' , new OrderResource($order));
    }

    public function continue_order(Request $request , $order_id)
    {
        
        $order = Order::with('items')->findOrFail($order_id);
        if($order->status != 'semi_accepted')
            return ApiResponse::sendResponse(403 , 'there is items pending' , []);


        $new_price = $order->calculateNewPrice();
        $order->update([
            'price' => $new_price,
            'final_price' => $order->offer? ($new_price - $new_price*$order->offer/100):$new_price,
            'status' => 'accepted'
        ]);


        return ApiResponse::sendResponse(200 , 'order continued successfully' , new OrderResource($order));

    }

    // public function cancele_order(Request $request , $order_id)
    // {
    //     $order = Order::findOrFail($order_id);

    //     if($request->user()->id != $order->user_id)
    //         return ApiResponse::sendResponse(403 , 'you are not allowed' , ['is_allowed' => false]);

    //     if($order->status == 'semi_accepted')
    //     {
    //         $order->update([
    //             'status' => 'cancelled'
    //         ]);
    //     }
    //     return ApiResponse::sendResponse(200, 'order cancelled successfully', new OrderResource($order));


    // }
}
