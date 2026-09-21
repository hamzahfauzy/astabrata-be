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
 * example 2
 * Route::get('/', function(\Libraries\Request $request){ return 'Hello World';})
 * 
 * example 3
 * Route::get('/', [\App\Controllers\IndexController::class, 'index'])
 * 
 * example 4
 * Route::get('/', isAuthenticated(), 'modules/index')
 */

Route::get('/', function(){
    return ['message' => "It's Work"];
});

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

Route::get('/villages/find-by-region-name/{name}', isAuthenticated(), 'modules/master/villages/find-by-region-name');

Route::get('/regions/get', isAuthenticated(), 'modules/master/regions/get');
Route::get('/educations/get', isAuthenticated(), 'modules/master/educations/get');

Route::crud('/regions', 'modules/master/regions/config', 'regions.', isAuthenticated());
Route::crud('/villages', 'modules/master/villages/config', 'villages.', isAuthenticated());
Route::crud('/instances', 'modules/master/instances/config', 'instances.', isAuthenticated());
Route::crud('/educations', 'modules/master/educations/config', 'educations.', isAuthenticated());
Route::crud('/periods', 'modules/master/periods/config', 'periods.', isAuthenticated());

// Route::crud('/kpm/profiles', 'modules/kpm/profiles/config', 'profiles.', isAuthenticated());
Route::get('/kpm/dashboard', isAuthenticated(), periodActive(), 'modules/kpm/dashboard');

Route::get('/kpm/profile-documents', isAuthenticated(), permissionMiddleware('desa'), periodActive(), 'modules/kpm/profiles/documents');
Route::get('/kpm/profiles', isAuthenticated(), periodActive(), 'modules/kpm/profiles/index');
Route::get('/kpm/profiles/{id}', isAuthenticated(), periodActive(), 'modules/kpm/profiles/view');
Route::post('/kpm/profiles/{id}/stage', isAuthenticated(), periodActive(), 'modules/kpm/profiles/submit-stage');
Route::post('/kpm/profiles', isAuthenticated(), permissionMiddleware('desa'), periodActive(), 'modules/kpm/profiles/create');