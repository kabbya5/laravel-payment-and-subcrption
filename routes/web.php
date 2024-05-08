<?php

use Illuminate\Support\Facades\Route;
use App\Models\Currency;
use App\Models\PaymentPlatform;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/payment',function(){
    $currencies = Currency::all();
    $paymentPlatForms = PaymentPlatform::all();
    return view('payment.payment',compact('currencies','paymentPlatForms'));
})->name('payment.index');

Route::controller(PaymentController::class)->group(function(){
    Route::post('/payment/store','store')->name('payment.store');
    Route::get('/payment/approval','approval')->name('payment.approval');
    Route::get('/payment/cancelled','cancelled')->name('payment.cancel');
});
Route::get('config',[PaymentController::class,'config']);


