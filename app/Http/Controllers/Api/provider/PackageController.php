<?php

namespace App\Http\Controllers\Api\provider;

use App\Helpers\ApiResponse;
use App\Helpers\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Http\Resources\PackageResource;
use App\Http\Resources\ProductResource;
use App\Models\Package;
use App\Models\PackageProduct;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use League\Uri\Idna\Option;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */




    public function index(Request $request)
    {
        $packages = $request->user()->store->package;
        if(count($packages) < 1)
            return ApiResponse::sendResponse(404 , 'packages is empty' , []);

        return ApiResponse::sendResponse(200 , 'packages retrieved successfully' , PackageResource::collection($packages));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageRequest $request)
    {

        $validated_data = $request->validated();

        $validated_data['store_id'] = $request->user()->store->id;
        $validated_data['end_date'] = now()->addDays(intval($request->duration));
        $validated_data['price'] = 0 ; //default

        $validated_data['final_price'] = 0; // default

        if ($request->hasFile('image')) {
            $validated_data['image'] = StoreImage::upload($request->file('image') ,'packages');
        }

        $package = Package::create(Arr::except($validated_data, ['products' , 'duration']));

        if (! $package) {
            return ApiResponse::sendResponse(500, 'package failed to store', []);
        }

        if (! empty($validated_data['products']) ) 
        {
            $data = [];
            foreach($validated_data['products'] as $product)
            {
                $data[$product['id']] = ['option_id' => $product['option_id']??null];
            }
            $package->product()->attach($data);
        }

        $price = $package->calculate_price();
        $final_price =  $validated_data['offer'] ? $price - ($price * $validated_data['offer'] / 100): $price;
        $package->update(['price' => $price ,
        'final_price' => $final_price
        ]);
        
        return ApiResponse::sendResponse(201, 'package stored successfully', new PackageResource($package));
    }


    /**
     * Display the specified resource.
     */
    public function show($package_id)
    {
        $package = Package::with(['store' , 'product' , 'orders.user' , 'events.user'])->find($package_id);

        $orders_users_name  = $package->orders->pluck('user.name')->unique()->values()->toArray();
        $events_users_name  = $package->events->pluck('user.name')->unique()->values()->toArray();

        if(!$package)
            return ApiResponse::sendResponse(404 , 'Package Not Found' , []);

        return ApiResponse::sendResponse(200 , 'package retrieved successfully' , 
        ['package'=>new PackageResource($package) ,
        'orders_users_name' => $orders_users_name , 
        'event_orders_name' => $events_users_name]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PackageRequest $request, $package_id)
    {

        $package = Package::find($package_id);
        if(!$package)
            return ApiResponse::sendResponse(404 , 'Package Not Found' , []);

        if($request->user()->store->id != $package->store_id)
            return ApiResponse::sendResponse(403,'you are not allowed to do this action' , []);

        $validated_data = $request->validated();
        $validated_data['store_id'] = $request->user()->store->id;
        $validated_data['end_date'] = now()->addDays($request->duration);
        $validated_data['price'] = 0 ; //default

        $validated_data['final_price'] = 0;


        if ($request->hasFile('image')) {
            $validated_data['image'] = StoreImage::upload($request->file('image') ,'packages');
        }

        $record = $package->update(Arr::except($validated_data, ['products' , 'duration']));

        if (! $record) {
            return ApiResponse::sendResponse(401, 'package failed to store', []);
        }

        if (! empty($validated_data['products']) ) 
            {
                $data = [];
                foreach($validated_data['products'] as $product)
                {
                    $data[$product['id']] = ['option_id' => $product['option_id']??null];
                }
                $package->product()->sync($data);
            }
        
        $price = $package->calculate_price();
        $final_price =  $validated_data['offer'] ? $price - ($price * $validated_data['offer'] / 100): $price;
        $package->update(['price' => $price ,
        'final_price' => $final_price
        ]);

       return ApiResponse::sendResponse(201, 'package updated successfully',[]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request , $package_id)
    {
        $package = Package::find($package_id);
        if(!$package)
            return ApiResponse::sendResponse(404 , 'Package Not Found' , []);

        if($request->user()->store->id != $package->store_id)
            return ApiResponse::sendResponse(403,'you are not allowed to do this action' , []);

        $package->delete();
        return ApiResponse::sendResponse(200 , 'package deleted successfully' , []);
    }
}
