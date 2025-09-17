<?php

namespace App\Http\Controllers\Api\provider;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $store = $request->user()->store;

        $store->load('product');
        return ApiResponse::sendResponse(200 , 'home data retrieved successfully' , [ 
            'store' => new StoreResource($store) , 
            'provider_name' =>  $request->user()->name
        ]);
    }



}
