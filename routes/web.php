<?php

use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\PackageController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ProviderController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\ProfileController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(HomeController::class)->prefix('dashboard')->group(function(){

    Route::get('/', 'index')->name('dashboard');
    Route::get('/account/create' , 'create')->name('account.create');
    Route::post('/account/store' , 'store')->name('account.store');
    Route::get('/account/{account_id}/edit' , 'edit')->name('account.edit');

    Route::post('/account/{account_id}/update' , 'update')->name('account.update');
    Route::post('/account/{account_id}/delete' , 'destroy')->name('account.destroy');
    Route::get('/account/{account_id}/show' , 'show')->name('account.show');
    Route::get('/company/account/{account_id}/create' , 'createcompany')->name('company.create');
    Route::post('/company/account/{account_id}/store' , 'storecompany')->name('company.store');

    Route::get('/account/{account_id}/products' , 'indexproducts')->name('indexaccountproducts');
    Route::get('/account/{account_id}/product/create' , 'createproduct')->name('createaccountproduct');
    Route::post('/account/{account_id}/product/store' , 'storeproduct')->name('storeaccountproduct');
    Route::post('/account/{account_id}/product/{product_id}/delete' , 'destroyproduct')->name('destroyaccountproduct');
});

Route::controller(UserController::class)->prefix('dashboard')->group(function(){
    Route::get('/users' , 'index')->name('dashboard.users.index');
    Route::get('/user/{user_id}/show' , 'show')->name('user.show');
    Route::post('/users/{user_id}/block' , 'block')->name('user.block');
    Route::post('/users/{user_id}/active' , 'active')->name('user.active');
});


Route::controller(ProviderController::class)->prefix('dashboard')->group(function(){
    Route::get('/providers' , 'index')->name('dashboard.providers.index');
    Route::post('/providers/{provider_id}/block' , 'block')->name('provider.block');
    Route::post('/providers/{provider_id}/active' , 'active')->name('provider.active');
    Route::get('/provider/{provider_id}/show' , 'show')->name('provider.show');
});
Route::controller(OrderController::class)->prefix('dashboard')->group(function(){
    Route::get('/orders' , 'index')->name('dashboard.orders.index');
    Route::get('/order/{order_id}/show' , 'show')->name('order.show');
    
});
Route::controller(EventController::class)->prefix('dashboard')->group(function(){
    Route::get('/events' , 'index')->name('dashboard.events.index');
    Route::get('/event/{event_id}/show' , 'show')->name('event.show');
    
});
Route::controller(ProductController::class)->prefix('dashboard')->group(function(){
    Route::get('/products' , 'index')->name('dashboard.products.index');
    Route::get('/product/{product_id}/show' , 'show')->name('product.show');
    
});
Route::controller(PackageController::class)->prefix('dashboard')->group(function(){
    Route::get('/packages' , 'index')->name('dashboard.packages.index');
    Route::get('/package/{package_id}/show' , 'show')->name('package.show');
    
});
Route::controller(ServiceController::class)->prefix('dashboard')->group(function(){
    Route::get('/services' , 'index')->name('dashboard.services.index');
    Route::get('/service/create' , 'create')->name('service.create');
    Route::post('/service/store' , 'store')->name('service.store');
    Route::get('/service/{servce_id}/edit' , 'edit')->name('service.edit');
    Route::post('/service/{servce_id}/update' , 'update')->name('service.update');
    Route::post('/service/{servce_id}/delete' , 'destroy')->name('service.destroy');
    
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
