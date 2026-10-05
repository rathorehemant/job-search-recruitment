<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\CustomerController;

route::post('/auth/login', [AuthController::class, 'login'])
    ->name('api.login');

    Route::middleware('auth:api')->group(function () {
     Route::get('/leads', [LeadsController::class, 'index'])
        ->middleware('permission:leads.view')
        ->name('api.leads.index');

        Route::get('/customers', [CustomerController::class, 'index'])
        ->middleware('permission:customers.view')
        ->name('customers.index');

    });

