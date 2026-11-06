<?php

use Libraries\Route;

/**
 * How to use
 * 
 * Route::{method}({path}, ...{handlers})
 * 
 * example 1
 * Route::get('/', 'modules/index')
 * 
 * example 2 : not cacheable
 * Route::get('/', function(\Libraries\Request $request){ return 'Hello World';})
 * 
 * example 3
 * Route::get('/', [\App\Controllers\IndexController::class, 'index'])
 * 
 * example 4
 * Route::get('/', isAuthenticated(), 'modules/index')
 */

Route::get('/', 'modules/index');

Route::post('/login', validate('modules/auth/validation/login'), 'modules/auth/actions/login');
Route::post('/register', validate('modules/auth/validation/register'), 'modules/auth/actions/register');
Route::get('/me', isAuthenticated(), 'modules/auth/actions/me');
Route::put('/me', isAuthenticated(), validate('modules/auth/validation/update-profile'), 'modules/auth/actions/update-profile');
Route::get('/dashboard', isAuthenticated(), 'modules/dashboard');

/**
 * Alternate Code
 * Route::crud('/roles', [\App\RoleController::class, 'config']); // config was static
 * Route::crud('/roles', function(){
 *    return [
 *      'data' => [
 *        'table' => 'roles'
 *      ]
 *    ];
 * });
 */
Route::crud('/roles', 'modules/roles/config', 'roles.', isAuthenticated());
Route::crud('/permissions', 'modules/permissions/config', 'permissions.', isAuthenticated());
Route::crud('/users', 'modules/users/config', 'users.', isAuthenticated());