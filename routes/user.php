<?php

use App\Http\Controllers\Api\user\EventController;
use App\Http\Controllers\Api\user\HomeController ;
use App\Http\Controllers\Api\user\OrderController;
use App\Http\Controllers\Api\user\ProfileController;
use App\Http\Controllers\Api\user\ServiceController;
use App\Http\Controllers\Api\user\StoreController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum' , 'isUser'])->group(function(){

    Route::controller(ServiceController::class)->group(function(){
        Route::get('/all_categories' , 'all_categories');
        Route::get('/services_by_category_id' , 'services_by_cat_id');
        Route::get('/all_services' , 'all_services');
        Route::get('/products_by_service_id' , 'product_by_service_id');
    });


    Route::controller(OrderController::class)->prefix('order')->group(function(){
        Route::post('/store' , 'store');
        Route::get('/show/{order_id}' , 'show');
        Route::post('/continue/{order_id}' , 'continue_order');
        // Route::post('/cancele/{order_id}' , 'cancele_order');
    });

    Route::controller(StoreController::class)->prefix('store')->group(function(){
        Route::get('/{store_id}' , 'index_products');
        Route::get('/{store_id}/show' , 'show');
        Route::get('/{store_id}/evaluations' , 'index_evaluation');
        Route::post('/{store_id}/store_evaluation' , 'store_evaluation');
    });

    Route::controller(ProfileController::class)->prefix('user')->group(function(){
        Route::get('/' , 'index');
        Route::get('/orders' , 'index_orders');
        Route::get('/items/order/{order_id}' , 'index_items');
        Route::post('/profile/update' , 'update_profile');
        Route::post('/profile/change_password' , 'change_password');
        Route::post('/profile/change_phone' , 'change_phone');
        Route::post('/profile/verify_change_phone' , 'verify_change_phone');
        
    });


    Route::controller(EventController::class)->prefix('event')->group(function(){
        Route::post('/store' , 'store');
        Route::post('/{event_id}/update' , 'updateMainData');
        Route::post('/{event_id}/show' , 'show');
        Route::post('/{event_id}/edit_image' , 'edit_image');
        Route::delete('/image/{image_id}/delete' , 'delete_image');
        Route::get('/{event_id}/images' , 'index_images');
        Route::post('/{event_id}/edit_item' , 'edit_item');
        Route::delete('/item/{item_id}/delete' , 'delete_item');
        Route::post('/{event_id}/edit_tasks' , 'edit_tasks');
        Route::get('/{event_id}/tasks' , 'index_tasks');
        Route::post('/task/{task_id}/do' , 'do_task');
        Route::delete('/task/{task_id}/delete' , 'delete_task');

        Route::post('/{event_id}/send_invitation' , 'send_invitation');
        Route::get('/{event_id}/guests' , 'index_guests');
        Route::get('/my_invitations' , 'my_invitations');
        Route::get('/my_events' , 'my_events');
        Route::post('/invitation/event/{event_id}/confirm' , 'response');
    });

        Route::controller(HomeController::class)->group(function(){
            Route::get('/home' , 'index');
            Route::get('/products/search' , 'search');

        });
});