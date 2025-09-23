<?php

use App\Http\Controllers\Api\provider\HomeController;
use App\Http\Controllers\Api\provider\PackageController;
use App\Http\Controllers\Api\provider\ProductController;
use App\Http\Controllers\Api\provider\SettingController;
use App\Http\Controllers\Api\provider\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum' , 'isProvider'])->prefix('provider')->group(function(){

    Route::post('/store' , [StoreController::class ,  'store']);

    Route::middleware('hasStore')->group(function(){
        //store routes
        Route::controller(StoreController::class)->prefix('company')->group(function(){
            Route::get('/' , 'index');
            Route::post('/update' , 'update');
        });
        
        
        Route::get('/services' , [ProductController::class , 'all_services']); // get sevices in product store screen
        
        // Home routes
            Route::get('/' , HomeController::class);
        
        // product routes
        Route::controller(ProductController::class)->prefix('product')->group(function(){
            Route::get('/' ,  'index'); 
            Route::post('/store' , 'store');
            Route::post('/update/{product_id}' , 'update');
            Route::get('/show/{product_id}' , 'show');
            Route::delete('/delete/{product_id}' , 'destroy');
        });
        
        
        // package routes 
    
        Route::controller(PackageController::class)->prefix('package')->group(function(){
            Route::get('/' , 'index');
            Route::post('/store' , 'store');
            Route::post('/update/{package_id}' , 'update');
            Route::get('/show/{package_id}' , 'show');
            Route::delete('/delete/{package_id}' , 'destroy');
        });
    
    
    
        // settings route
    
        Route::get('/social_links' ,[ SettingController::class,'social_links']);
    });

    
});