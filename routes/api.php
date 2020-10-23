<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('guest')->group(function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('refresh-token', 'AuthController@refreshToken')->name('refreshToken');
});

Route::post('logout', 'AuthController@logout')->name('logout');

Route::middleware('auth:api')->group(function () {
    Route::get('validate-token', 'AuthController@validateToken')->name('validateToken');

    Route::middleware('admin')->group(function () {
        Route::post('employee/create', 'EmployeeController@store')->name('employee.create');
        Route::post('employee/{employee}/licenses', 'EmployeeLicenseController@store')->name('employee.licenses');
        Route::post(
            'employee/{employee}/license/{license}/complete',
            'EmployeeLicenseController@complete'
        )->name('employee.license.complete');

        Route::post('license', 'LicenseController@store')->name('license.create');
    });
});
