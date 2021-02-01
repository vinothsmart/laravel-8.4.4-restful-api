<?php

use App\Http\Controllers\Role\RoleController;
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

/**
 * Roles
 */

Route::resource('roles', RoleController::class, ['except' => ['create', 'edit']]);
Route::get('rolesList', [RoleController::class, 'list']);

/**
 * Users
 */

Route::resource('users', UserController::class, ['except' => ['create', 'edit']]);
