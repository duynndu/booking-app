<?php

use App\Http\Controllers\AmenityController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MoMoController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refreshToken', 'refreshToken');
});

Route::controller(TodoController::class)->group(function () {
    Route::get('todos', [TodoController::class, 'index']);
    Route::post('todo', [TodoController::class, 'store']);
    Route::get('todo/{id}', [TodoController::class, 'show']);
    Route::put('todo/{id}', [TodoController::class, 'update']);
    Route::delete('todo/{id}', [TodoController::class, 'destroy']);
});

Route::apiResource('room-types', RoomTypeController::class);
Route::apiResource('amenities', AmenityController::class);
Route::apiResource('rooms', RoomController::class);
Route::post('create-room', [RoomController::class, 'createRoom']);
Route::post('rooms/{id}/upload-image', [RoomController::class, 'uploadImageRoom']);
Route::get('available-rooms', [RoomController::class, 'availableRooms']);
Route::apiResource('banners', BannerController::class);
Route::get('banner-active', [BannerController::class, 'getBannerActive']);
Route::post('banners/{id}/upload-image', [BannerController::class, 'uploadImageBanner']);
Route::apiResource('promotions', PromotionController::class);
Route::apiResource('bookings', BookingController::class);
Route::post('bookings/{id}/mark-as-paid', [BookingController::class, 'markAsPaid']);

Route::post('momo/create-payment', [MoMoController::class, 'createPayment']);
Route::post('momo/momo-ipn', [MoMoController::class, 'momoIpn']);

Route::post('cash/create-payment', [CashController::class, 'createPayment']);


Route::post('delete-image-by-url', [ImageController::class, 'deleteImageByUrl']);
Route::post('images', [ImageController::class, 'store']);
