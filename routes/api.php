<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Role\RoleUserController;
use App\Http\Controllers\User\UserRoleController;

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
Route::resource('roles.users', RoleUserController::class, ['only' => ['index']]);

/**
 * Users
 */

Route::resource('users', UserController::class, ['except' => ['create', 'edit']]);
Route::resource('users.roles', UserRoleController::class, ['only' => ['index']]);
