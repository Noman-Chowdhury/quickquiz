<?php

use App\Http\Controllers\HydraController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::apiResource('users',UserController::class)->except(['edit','create','store','update'])->middleware(['auth:sanctum', 'ability:admin,super-admin']);
Route::post('users',[UserController::class,'store']);
Route::put('users/{user}',[UserController::class,'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);
Route::post('users/{user}',[UserController::class,'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);
Route::patch('users/{user}',[UserController::class,'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);
Route::get('me',[UserController::class,'me'])->middleware('auth:sanctum');
Route::post('login',[UserController::class,'login']);

Route::apiResource('roles',RoleController::class)->except(['create','edit'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);
Route::apiResource('users.roles',UserRoleController::class)->except(['create','edit','show','update'])->middleware(['auth:sanctum', 'ability:admin,super-admin']);

Route::apiResource('quiz', \App\Http\Controllers\Api\QuizController::class)->middleware(['auth:sanctum', 'ability:admin,super-admin']);
Route::apiResource('question', \App\Http\Controllers\Api\QuestionController::class)->middleware(['auth:sanctum', 'ability:admin,super-admin']);

Route::get('user/questions', [\App\Http\Controllers\Api\QuestionApiController::class,'getTodaysQuestions']);
//Route::post('user/questions', [\App\Http\Controllers\Api\QuestionApiController::class,'submitAnswers'])->middleware(['auth:sanctum', 'ability:user']);
Route::post('user/questions', [\App\Http\Controllers\Api\QuestionApiController::class,'submitAnswers']);

Route::get('user/submitted/questions', [\App\Http\Controllers\Api\QuestionApiController::class,'getSubmittedQuestions']);


