<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\CustomerController;

Route::get('auth/login', function () {
    return view('Auth.login');
})->name('login');

Route::post('/auth/login', [AuthController::class, 'login'])
    ->name('login');


Route::middleware('auth.custom')->group(function () {

    Route::get('/', function () {
        return view('dashboard');
    })
    ->middleware('permission:dashboard.view')
    ->name('dashboard');


    // Users
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users.index');


    // Roles
    Route::get('/user/role', [UserController::class, 'getUserRoles'])
        ->middleware('permission:roles.view')
        ->name('users.role');

    Route::post('/user/role', [UserController::class, 'storeUserRoles'])
        ->middleware('permission:roles.create')
        ->name('roles.store');

    Route::put('/roles/{role}', [UserController::class, 'updateUserRoles'])
        ->middleware('permission:roles.update')
        ->name('roles.update');

    Route::delete('/roles/{role}', [UserController::class, 'deleteUserRoles'])
        ->middleware('permission:roles.delete')
        ->name('roles.destroy');


    // Leads
    Route::get('/leads', [LeadsController::class, 'index'])
        ->middleware('permission:leads.view')
        ->name('leads.index');

    Route::post('/leads', [LeadsController::class, 'store'])
        ->middleware('permission:leads.create')
        ->name('leads.store');

    Route::put('/leads/{lead}', [LeadsController::class, 'update'])
        ->middleware('permission:leads.update')
        ->name('leads.update');

    Route::delete('/leads/{lead}', [LeadsController::class, 'destroy'])
        ->middleware('permission:leads.delete')
        ->name('leads.destroy');


    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])
        ->middleware('permission:customers.view')
        ->name('customers.index');

});