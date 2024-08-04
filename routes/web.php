<?php

use App\Mail\MyEmail;
use App\Mail\OrderShipped;
use App\Models\Room;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return response()->json(['hello' => 'world']);
//});
Route::get('/', function () {
    $room = Room::where('id', 16)->with('images')->first();
//    dd($room->toArray());
    $room->roomType->name;
    return view('welcome');
});
