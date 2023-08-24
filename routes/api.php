<?php

use App\Http\Controllers\AuthController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/index',[QuestionController::class,'index']);
Route::get('/detail/{id}',[QuestionController::class,'detail']);

Route::middleware(['auth:sanctum'])->group(function () {
 Route::get('/logout', [AuthController::class, 'logout']);
 Route::prefix('question')->group(function () {
  Route::controller(QuestionController::class)->group(function(){
   Route::post('/create','store');
   Route::put('/update','update');
   Route::delete('/delete/{id}','destroy');
  });
  
 });
});
