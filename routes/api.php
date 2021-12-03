<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TutoringClassController;
use App\Http\Controllers\EnrolmentController;
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


//Contact Request
Route::get('/contact-requests', [ContactRequestController::class, 'showAll']);
Route::post('/contact-requests', [ContactRequestController::class, 'add']);
Route::get('/contact-requests/{id}', [ContactRequestController::class, 'showById']);
Route::patch('/contact-requests/{id}', [ContactRequestController::class, 'updateContact']);
Route::delete('/contact-requests/{id}', [ContactRequestController::class, 'delete']);


Route::group(['prefix' => 'auth'], function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::get('/users', [AuthController::class, 'showUsers']);
Route::get('/users/{id}', [AuthController::class, 'showById']);
Route::patch('/users/{id}', [AuthController::class, 'updateUser']);
Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);

Route::get('/reviews', [ReviewController::class, 'showReviews']);
Route::get('/reviews/{id}', [ReviewController::class, 'showById']);
Route::post('/reviews', [ReviewController::class, 'addReview']);
Route::patch('/reviews/{id}', [ReviewController::class, 'updateReview']);
Route::delete('/reviews/{id}', [ReviewController::class, 'deleteReviews']);

Route::post('/tutoring-classes', [TutoringClassController::class, 'addTutoringClass']);
Route::get('/tutoring-classes', [TutoringClassController::class, 'getClasses']);
Route::get('/tutoring-classes/{id}', [TutoringClassController::class, 'getClassById']);
Route::patch('/tutoring-classes/{id}', [TutoringClassController::class, 'updateDescription']);
Route::delete('/tutoring-classes/{id}', [TutoringClassController::class, 'deleteClass']);

Route::post('/tutoring-class/{id}/enroll', [EnrolmentController::class, 'addEnrolment'])->name('enroll');

