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


Route::apiResource('users', UserController::class)->except(['edit', 'create', 'store', 'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin']);
Route::post('users', [UserController::class, 'store']);
Route::put('users/{user}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);
Route::post('users/{user}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);
Route::patch('users/{user}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);
Route::get('me', [UserController::class, 'me'])->middleware('auth:sanctum');
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('roles', RoleController::class)->except(['create', 'edit'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);
Route::apiResource('users.roles', UserRoleController::class)->except(['create', 'edit', 'show', 'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin']);

Route::apiResource('quiz', \App\Http\Controllers\Api\QuizController::class)->middleware(['auth:sanctum', 'ability:admin,super-admin']);

Route::apiResource('question', \App\Http\Controllers\Api\QuestionController::class);

Route::get('user/questions', [\App\Http\Controllers\Api\QuestionApiController::class, 'getTodaysQuestions'])->middleware('auth:sanctum');
//Route::post('user/questions', [\App\Http\Controllers\Api\QuestionApiController::class,'submitAnswers'])->middleware(['auth:sanctum', 'ability:user']);
Route::post('user/questions', [\App\Http\Controllers\Api\QuestionApiController::class, 'submitAnswers'])->middleware('auth:sanctum');

Route::get('user/submitted/questions', [\App\Http\Controllers\Api\QuestionApiController::class, 'getSubmittedQuestions'])->middleware('auth:sanctum');

Route::get('user/today', [\App\Http\Controllers\Api\QuestionApiController::class, 'availableQuiz'])->middleware('auth:sanctum');

Route::post('user/contribute/questions', [\App\Http\Controllers\Api\QuestionApiController::class, 'contributeQuestion'])->middleware('auth:sanctum');

Route::get('contributed-questions', [\App\Http\Controllers\Api\QuestionController::class, 'contributedQuestions']);

Route::post('remove-contributed-question/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'removeContributedQuestion']);

Route::get('user/weekly-history', [\App\Http\Controllers\Api\QuizHistoryApiController::class, 'myWeeklyHistory'])->middleware('auth:sanctum');

Route::get('user/daily-history/{week}', [\App\Http\Controllers\Api\QuizHistoryApiController::class, 'myDailyHistory'])->middleware('auth:sanctum');

Route::get('leaderboard/daily', [\App\Http\Controllers\ProgressController::class,'daily']);
Route::get('leaderboard/weekly', [\App\Http\Controllers\ProgressController::class,'weekly']);
Route::get('leaderboard/overall', [\App\Http\Controllers\ProgressController::class,'overall']);
