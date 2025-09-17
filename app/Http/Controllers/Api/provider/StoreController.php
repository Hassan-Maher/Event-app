<?php

namespace App\Http\Controllers\Api\provider;

use App\Helpers\ApiResponse;
use App\Helpers\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $store = $request->user()->store;

        $store->load('provider');
        return ApiResponse::sendResponse(200 , 'store retrieved successfully' , new StoreResource($store));
    }

    public function store(StoreRequest $request)
    {
        
        if($request->user()->store)
            return ApiResponse::sendResponse(403 , 'user has already store' , []);


        $validated_data = $request->validated();
        
        if ($request->hasFile('logo')) 
            {
                
                $validated_data['logo'] = StoreImage::upload($request->file('logo') , 'stores');
            }
            
        $validated_data['user_id'] = $request->user()->id;
        $store = Store::create($validated_data);

        if($store)
            $store->load('provider');
            return ApiResponse::sendResponse(201, 'store has been stored successfully' , new StoreResource($store));
    }

    public function update(StoreRequest $request)
    {
        $store = $request->user()->store;

        $validated_data = $request->validated();
        
        if ($request->hasFile('logo')) 
            {
                
                $validated_data['logo'] = StoreImage::upload($request->file('logo') , 'stores');
            }
            
        $validated_data['user_id'] = $request->user()->id;

        $record = $store->update($validated_data);

        if($record)
            return ApiResponse::sendResponse(201 , 'store updated successfully' , []);
    }

    



    
}
