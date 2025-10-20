<?php

namespace App\Http\Controllers\Api\user;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductMainResource;
use App\Http\Resources\ServiceResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{

    public function all_categories(Request $request)
    {
        $categories = Category::get();

        if(count($categories) > 0)
            return ApiResponse::sendResponse(200 , 'categories retrieved successfully' , CategoryResource::collection($categories));

        return ApiResponse::sendResponse(200 , 'categories is empty' , []);
    }

    public function services_by_cat_id(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'exists:categories,id',
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        if($request->has('category_id'))
        {
            $services = Service::where('category_id' , $request->category_id)->get();
        }
        else
        {
            $services = Service::get();
        }

        if(count($services) > 0)

            return ApiResponse::sendResponse(200 , 'services retrieved successfully' , ServiceResource::collection($services));
            
            return ApiResponse::sendResponse(200 , 'services is empty' , []);

    }

    public function all_services()
    {
        $services = Service::get();

        if(count($services) > 0)
            {
                return ApiResponse::sendResponse(200 , 'services retrieved succesfully' , ServiceResource::collection($services));
        }
    }
    
    public function product_by_service_id(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'exists:services,id',
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        if($request->has('service_id'))
        {
            $products = Product::where('service_id' , $request->service_id)->latest()->get();
        }
        else
        {
            $products = Product::get();
        }

        if(count($products)> 0)
            return ApiResponse::sendResponse(200 , 'products retrieved succesfully' , ProductMainResource::collection($products));
        return ApiResponse::sendResponse(200 , 'products is empty' , []);
    }   
}
