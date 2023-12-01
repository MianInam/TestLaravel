<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout',[AuthController::class,'logout']);

    Route::prefix('todo')->group(function () {
        Route::post('/store',[TodoController::class,'store']);
        Route::post('/update/{todo_id}',[TodoController::class,'update']);
        Route::delete('/delete/{todo_id}',[TodoController::class,'delete']);
        Route::get('/show/{todo_id}',[TodoController::class,'show']);
        Route::get('/list',[TodoController::class,'index']);
    });
});
