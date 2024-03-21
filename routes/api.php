<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LeaveTypesController;

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


               //EMPLOYEES API
Route::get('employees', [EmployeeController::class, 'index']);

Route::post('employees', [EmployeeController::class, 'store']);

Route::get('employees/{id}', [EmployeeController::class, 'show']);

Route::get('employees/{id}/edit', [EmployeeController::class, 'edit']);

Route::put('employees/{id}/edit', [EmployeeController::class, 'update']);

Route::delete('employees/{id}/delete', [EmployeeController::class, 'destroy']);


            //LEAVE API 
Route::get('leaves', [LeaveController::class, 'index']);

Route::post('leaves', [LeaveController::class, 'store']);

Route::get('leaves/{id}', [LeaveController::class, 'show']);

Route::delete('leaves/{id}/delete', [LeaveController::class, 'destroy']);


             //LEAVETYPES API
Route::get('leavetypes', [LeaveTypesController::class, 'index']);

Route::post('leavetypes', [LeaveTypesController::class, 'store']);

Route::get('leavetypes/{id}', [LeaveTypesController::class, 'show']);

Route::get('leavetypes/{id}/edit', [LeaveTypesController::class, 'edit']);

Route::put('leavetypes/{id}/edit', [LeaveTypesController::class, 'update']);

Route::delete('leavetypes/{id}/delete', [LeaveTypesController::class, 'destroy']);
             
             