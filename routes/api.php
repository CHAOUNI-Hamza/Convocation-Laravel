<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CycleController;

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

Route::apiResource('students', StudentController::class);
Route::get('/students/{apogee}/apogee', [StudentController::class, 'getByApogee']);

Route::get('/profs/{som}', [TeacherController::class, 'getBySom']);

Route::get('/timeslots', [TimeslotController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/reservations/{apogee}', [ReservationController::class, 'getReservationsByApogee']);


Route::apiResource('users', UserController::class);
Route::put('users/{user}/password', [UserController::class, 'updatePassword']);

Route::get('/professeurs/{id}/exams', [TeacherController::class, 'getExamDunProf']);
Route::get('/professeurs-disponibles', [TeacherController::class, 'getTeachersDisponibles']);

Route::get('/professeurs/som/{sum_number}/exams', [TeacherController::class, 'getExamDunProfParSumNumber']);

Route::get('/assign-prof-module', [ExamController::class, 'assignProfModulesRandomly']);
Route::get('/remove-prof-assignments', [ExamController::class, 'removeProfAssignments']);
Route::get('/exam-teacher/details', [ExamController::class, 'getExamTeachersDetails']);


Route::get('exams/all', [ExamController::class, 'all']);
Route::apiResource('exams', ExamController::class);

Route::get('/cycle', [CycleController::class, 'index']);
Route::put('/cycle', [CycleController::class, 'update']);



Route::get('teachers/all', [TeacherController::class, 'all']);

Route::apiResource('teachers', TeacherController::class);

Route::apiResource('reservation', ReservationController::class);
Route::get('res/reservation', [ReservationController::class, 'indexRes']);
