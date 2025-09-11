<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function(){
    Route::post('/Register' , 'Register');
    Route::post('/verify-register-otp' , 'verifyRegisterOtp');
    Route::post('/resend-otp' , 'resendOtp');
    Route::post('/login' , 'login');
    Route::post('/reset-password-otp' , 'resetPasswordOtp');
    Route::post('/verify-reset-otp' , 'verifyResetOtp');
    Route::post('/reset-Password' , 'resetPassword');

    Route::middleware('auth:sanctum')->group(function (){
        Route::post('/store' , 'store');
    });
    

    
});
