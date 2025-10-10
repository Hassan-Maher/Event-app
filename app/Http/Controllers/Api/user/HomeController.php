<?php

namespace App\Http\Controllers\Api\user;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Http\Resources\ProductMainResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServiceResource;
use App\Models\Event;
use App\Models\Package;
use App\Models\Product;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::latest()->get(['id' , 'name']);

        $services = Service::all();

        $products = Product::latest()->take(5)->get();

        return ApiResponse::sendResponse(200 , 'data retrieved successfully' , [
            'events' => $events,
            'services' => ServiceResource::collection($services),
            'products' => ProductMainResource::collection($products)
        ]);
    }


    public function search(Request $request)
    {
        
            $products = Product::query()
                ->when($request->filled('name'), function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                })
                ->when($request->filled('service_id'), function ($query) use ($request) {
                    $query->where('service_id', $request->service_id);
                })
                ->when($request->filled('city_id'), function ($query) use ($request) {
                    $query->where('city_id', $request->city_id);
                })
                ->when($request->filled('start_price') && $request->filled('final_price'), function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->whereBetween('price', [$request->start_price, $request->final_price])

                        ->orWhereHas('options', function ($optionQuery) use ($request) {
                            $optionQuery->whereBetween('price', [$request->start_price, $request->final_price]);
                        });
                    });
                })
                ->when($request->filled('date'), function ($query) use ($request) {
                    $date = Carbon::createFromFormat('Y-m-d g:i A' , $request->date);
                    $day = strtolower($date->format('l'));

                    $query->whereJsonContains('available_days' , $day)->where('available_from', '<=', $date->format('H:i:s'))
                        ->where('available_to', '>=', $date->format('H:i:s'));
                })
            ->with('options')->get();

            
            $packages = Package::query()
                ->when($request->filled('name'), function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                })               
                ->when($request->filled('city_id'), function ($query) use ($request) {
                    $query->whereHas('store', function ($storeQuery) use ($request) {
                    $storeQuery->where('city_id', $request->city_id);
                    });
                })
                ->when($request->filled('start_price') && $request->filled('final_price'), function ($query) use ($request) {
                    $query->whereBetween('final_price', [$request->start_price, $request->final_price]);
                })
                ->when($request->has('date') , function ($query) use ($request){
                        $date = Carbon::createFromFormat('Y-m-d g:i A' , $request->date);
                        $query->where('end_date' , '>', $date->format('H:i:s'));
                    })
                ->get();
            

            
            return ApiResponse::sendResponse(200, 'Search results retrieved successfully', [
                'products' => ProductResource::collection($products),
                'packages' => PackageResource::collection($packages)
            ]);
            
    


    }
}
