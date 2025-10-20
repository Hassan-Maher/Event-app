<?php

namespace App\Http\Controllers\Api\provider;

use App\Helpers\ApiResponse;
use App\Helpers\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ProductImageResource;
use App\Http\Resources\ProductMainResource;
use App\Http\Resources\ProductOptionResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // get all services for create new product

    public function all_services()
    {
        $services = Service::get();

        if(count($services) > 0)
        {
            return ApiResponse::sendResponse(200 , 'services retrieved succesfully' , ServiceResource::collection($services));
        }
    }
    
    public function index(Request $request)
    {
        $products = $request->user()->store->product;

        if(count($products) < 1)
            return ApiResponse::sendResponse(200 , 'you dont have any products', []);

        return ApiResponse::sendResponse(200 , 'products retrieved successfully' , ProductMainResource::collection($products));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        
        $validated_data = $request->validated();

        if($request->hasFile('main_image'))
        {
            $validated_data['main_image'] = StoreImage::upload($request->file('main_image') , 'products');
        }
            

        $validated_data['available_from'] = Carbon::createFromFormat('g:i A', $request->available_from)->format('H:i:s');
        $validated_data['available_to'] =   Carbon::createFromFormat('g:i A', $request->available_to)->format('H:i:s');
        $validated_data['store_id'] = $request->user()->store->id;

        $product = Product::create(Arr::except($validated_data, ['extra_images' , 'options']));

        if(!$product)
            return ApiResponse::sendResponse(404 , 'product failed to store' , ['is_stored' => false]);

      

        if ($request->hasFile('extra_images') && !empty($validated_data['main_image'])) 
        {
            $product->image()->create([
                'image'   => $validated_data['main_image'],
                'is_main' => true,
            ]);

                foreach($request->file('extra_images') as $image)
                {
                    
                    $image_path = StoreImage::upload($image , 'products');
                    $product->image()->create([
                        'image'   => $image_path,
                        'is_main' => false,
                    ]);
                }
        }

        if ($request->has('options') && is_array($validated_data['options']))
       {
            foreach ($validated_data['options'] as $index => $option) 
            {
                if (isset($option['name']) && !isset($option['price'])) {
                    return ApiResponse::sendResponse(422, "Please enter the price of option at index $index", []);
                }

                if (!isset($option['name']) && isset($option['price'])) {
                    return ApiResponse::sendResponse(422, "Please enter the name of option at index $index", []);
                }

                if (isset($option['name']) && isset($option['price'])) {
                    $product->options()->create([
                        'name'  => $option['name'],
                        'price' => $option['price'],
                    ]);
                }
            }
       }

        return ApiResponse::sendResponse(201 , 'product has been stored successfully' , 
             new ProductMainResource($product),
        );

    
    }

    /**
     * Display the specified resource.
     */
    public function show($product_id)
    {
        $product = Product::with('store.provider' , 'service' , 'city' , 'image' , 'orders.user' , 'events.user')->find($product_id);

        $orders_users_name  = $product->orders->pluck('user.name')->unique()->values()->toArray();
        $events_users_name  = $product->events->pluck('user.name')->unique()->values()->toArray();
        if(!$product)
            return ApiResponse::sendResponse(404 , 'product_not_found' , ['is_found' => false]);

        return ApiResponse::sendResponse(200 , 'product retrieved successfully' , 
        ['product' => new ProductResource($product) ,
        'orders_users_name' => $orders_users_name , 
        'event_orders_name' => $events_users_name]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, $product_id)
    {
        $product = Product::find($product_id);
        
        if(!$product)
            return ApiResponse::sendResponse(404 , 'product_not_found' , ['is_found' => false]);

        if($request->user()->store->id != $product->store_id)
        {
            return ApiResponse::sendResponse(403 , 'you are not allowed to do this action' ,['is_allowed' => false]);
        }

        $validated_data = $request->validated();

        if($request->hasFile('main_image'))
        {
            $validated_data['main_image'] = StoreImage::upload($request->file('main_image') , 'products');
        }
            

        $validated_data['available_from'] = Carbon::createFromFormat('g:i A', $request->available_from)->format('H:i:s');
        $validated_data['available_to'] = Carbon::createFromFormat('g:i A', $request->available_to)->format('H:i:s');
        $validated_data['store_id'] = $request->user()->store->id;

        $record = $product->update(Arr::except($validated_data, ['extra_images']));

        if(!$record)
            return ApiResponse::sendResponse(401 , 'product failed to update' , ['is_store' => false]);

        if ($request->hasFile('extra_images') && !empty($validated_data['main_image'])) 
        {
            $product->image()->delete();
            
            $product->image()->create([
                'image'   => $validated_data['main_image'],
                'is_main' => true,
            ]);

            foreach($request->file('extra_images') as $image)
            {
                $image_path = StoreImage::upload($image , 'products');
                $product->image()->create([
                    'image'   => $image_path,
                    'is_main' => false,
                ]);
            }
        }
        else{
            $product->images()->delete();
        }

        return ApiResponse::sendResponse(201 , 'product updated successfully' , []);
    }


    public function show_option(Request $request , $option_id)
    {
        $option = ProductOption::with(['order_items.order.user' , 'event_items.event.user'])->findOrFail($option_id);

        $orders_users_name = $option->order_items->pluck('order.user.name')->unique()->values()->toArray();
        $event_users_name = $option->event_items->pluck('event.user.name')->unique()->values()->toArray();

        return ApiResponse::sendResponse(200 , 'option retrieved successfully'  , 
        ['option' => new ProductOptionResource($option) , 
        'order_users_name' => $orders_users_name , 
        'event_users_name' => $event_users_name]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request , $product_id)
    {
        $product = Product::find($product_id);

        if(!$product)
            return ApiResponse::sendResponse(404 , 'product_not_found' , ['is_found' => false]);

        if($request->user()->store->id != $product->store_id)
        {
            return ApiResponse::sendResponse(403 , 'you are not allowed to do this action' ,['is_allowed' => false]);
        }
        $deleted_product = $product;
        $product->delete();

        return ApiResponse::sendResponse(200 , 'product is deleted successfully' ,[]);
    }





}
