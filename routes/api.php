<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

});





Route::get('exams/all', [ExamController::class, 'all']);
Route::apiResource('exams', ExamController::class);



Route::get('teachers/all', [TeacherController::class, 'all']);
Route::get('/professeurs-disponibles', [TeacherController::class, 'getTeachersDisponibles']);
Route::post('/ajouter-professeur-surveillance', [TeacherController::class, 'ajouterProfesseur']);
Route::apiResource('teachers', TeacherController::class);
