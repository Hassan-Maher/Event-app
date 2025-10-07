<?php

namespace App\Http\Controllers\Api\user;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductMainResource;
use App\Http\Resources\StoreResource;
use App\Models\Evaluation;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    public function index_products(Request $request , $store_id)
    {
        $store = Store::findOrFail($store_id);

        $products = $store->product;
        if($request->has('service_id'))
        {
            $products = $store->product()->where('service_id' , $request->service_id)->get();
        }
        if(count($products) > 0)
        {
            return ApiResponse::sendResponse(200 , 'products retrieved successfully', 
            ['products' =>ProductMainResource::collection($products) ,
            'store_name' => $store->name ]);
        }
        return ApiResponse::sendResponse(200 , 'products is empty' , ['products' => [] ,  'store_name' => $store->name ]);
    }

    public function show(Request $request , $store_id)
    {
        $store = Store::findOrFail($store_id);
        return ApiResponse::sendResponse(200 , "stored retrieved successfully" , new StoreResource($store)); 
    }

    public function index_evaluation(Request $request , $store_id)
    {
        $store = Store::findOrFail($store_id);

        $evaluations = $store->evaluations;

        if(count($evaluations) > 0)
            return ApiResponse::sendResponse(200 , ' evaluations retrieved succesfully' , $evaluations);
        return ApiResponse::sendResponse(200 , ' evaluations is empty' , []);

    }

    public function store_evaluation(Request $request , $store_id)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'string',
            'rating' => 'required|integer|min:1|max:5',
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        $store = Store::findOrFail($store_id);
        $user = $request->user();

        $evaluate = Evaluation::create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'description' => $request->description??null,
            'rating' => $request->rating
        ]);

        if($evaluate)
            return ApiResponse::sendResponse(201 , 'evaluate stored successfully' , $evaluate);
    }

}
