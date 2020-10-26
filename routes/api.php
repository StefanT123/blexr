<?php

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

Route::middleware('guest')->group(function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('refresh-token', 'AuthController@refreshToken')->name('refreshToken');
});

Route::post('logout', 'AuthController@logout')->name('logout');

Route::middleware('auth:api')->group(function () {
    Route::get('validate-token', 'AuthController@validateToken')->name('validateToken');

    Route::middleware('admin')->group(function () {
        Route::get('employees', 'EmployeeController@index')->name('employee.index');
        Route::post('employee', 'EmployeeController@store')->name('employee.create');
        Route::post(
            'employee/{employee}/licenses',
            'EmployeeLicenseController@store'
        )->name('employee.licenses');
        Route::post(
            'employee/{employee}/license/{license}/complete',
            'EmployeeLicenseController@complete'
        )->name('employee.license.complete');

        Route::get('licenses', 'LicenseController@index')->name('license.index');
        Route::post('license', 'LicenseController@store')->name('license.create');

        Route::post(
            'work-from-home/{workFromHomeRequest}/approve',
            'WorkFromHomeController@approve'
        )->name('workFromHome.approve');

        Route::post(
            'work-from-home/{workFromHomeRequest}/deny',
            'WorkFromHomeController@deny'
        )->name('workFromHome.deny');

        Route::get(
            'work-from-home',
            'WorkFromHomeController@index'
        )->name('workFromHome.index');

        Route::get(
            'employee/{employee}/work-from-home',
            'WorkFromHomeController@show'
        )->name('workFromHome.show');
    });

    Route::get(
        'employee/licenses',
        'LicenseController@show'
    )->name('license.show');

    Route::get(
        'employee/{employee}',
        'EmployeeController@show'
    )->name('employee.show');


    Route::post(
        'employee/work-from-home',
        'WorkFromHomeController@store'
    )->name('employee.workFromHome');
});
