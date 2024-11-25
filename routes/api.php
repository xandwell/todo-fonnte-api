<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('tasks', [TaskController::class, 'index']);  // Fetch all tasks
    Route::post('tasks', [TaskController::class, 'store']);  // Add a new task
    Route::put('tasks/{id}', [TaskController::class, 'update']);  // Edit task
    Route::patch('tasks/{id}/complete', [TaskController::class, 'markComplete']);  // Mark task as complete
    Route::delete('tasks/{id}', [TaskController::class, 'destroy']);  // Delete task
    Route::post('logout', [AuthController::class, 'logout']);  // Logout user

    Route::get('tasks/{id}/sendMessage', [TaskController::class, 'sendWhatsAppMessage']);
});

