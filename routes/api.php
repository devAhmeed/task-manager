<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

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

    Route::group(['prefix' => 'tasks' , 'middleware' => 'auth:sanctum'] , function(){
        Route::get('/' , [TaskController::class , 'index']);
        Route::post('/' , [TaskController::class , 'store']);
        Route::put('/{task}' , [TaskController::class , 'update']);
        Route::delete('/{task}' , [TaskController::class , 'destroy']);
    });
