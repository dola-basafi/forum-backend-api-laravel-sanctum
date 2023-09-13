<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QuestionController;
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
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/question/index', [QuestionController::class, 'index']);
Route::get('/question/detail/{id}', [QuestionController::class, 'detail']);
Route::get('/category/index',[CategoryController::class,'index']);
Route::get('/answer/index/{id}',[AnswerController::class,'index']);


Route::middleware(['auth:sanctum'])->group(function () {
  Route::get('/logout', [AuthController::class, 'logout']);
  Route::prefix('question')->group(function () {
    Route::controller(QuestionController::class)->group(function () {
      Route::get('/own','own');
      Route::post('/create', 'store');
      Route::post('/update/{id}', 'update');
      Route::delete('/delete/{id}', 'destroy');
    });
  });
  Route::prefix('answer')->group(function () {
    Route::controller(AnswerController::class)->group(function () {      
      Route::get('/detail/{id}',  'detail');
      Route::post('/create', 'store');
      Route::post('/update/{id}', 'update');
      Route::delete('/delete/{id}', 'destroy');
    });
  });
});
