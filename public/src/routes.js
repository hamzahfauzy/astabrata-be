import Route from "../src/core/routes.js";
import Http from "./core/http.js";
import { isAuthenticated } from "./libs/functions.js";

const userData = {}

// AUTH ROUTE

Route.add("/login", 'views/layouts/auth', function(){
    document.title = 'Login'
}, ['AuthController', 'index'])

Route.add("/forget-password", 'views/layouts/auth', function(){
    document.title = 'Forget Password'
}, ['AuthController', 'forgetPassword'])

// APP ROUTE

Route.add("/", 'views/layouts/app', isAuthenticated, function(){
    document.title = 'Dashboard'
}, ['IndexController', 'index'])

Route.add('/profile/edit', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Edit Profile'
}, ['IndexController','editProfile'])

Route.add('/profile', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Profile'
}, ['IndexController','profile'])

Route.add('/users', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Users'
}, ['UserController','index'])

Route.add('/users/create', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Create User'
}, ['UserController','create'])

Route.add('/users/:id/edit', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Edit User'
}, ['UserController','edit'])

Route.add('/users/:id', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Detail User'
}, ['UserController','show'])

Route.add('/roles', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Roles'
}, ['RoleController','index'])

Route.add('/roles/create', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Create Role'
}, ['RoleController','create'])

Route.add('/roles/:id/edit', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Edit Role'
}, ['RoleController','edit'])

Route.add('/roles/:id', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Detail Role'
}, ['RoleController','show'])


// Master Periods
Route.add('/master/periods', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Periode'
}, ['Master/PeriodController','index'])

Route.add('/master/periods/create', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Input Data Periode'
}, ['Master/PeriodController','create'])

Route.add('/master/periods/:id/edit', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Edit Periode'
}, ['Master/PeriodController','edit'])

// Master Instances
Route.add('/master/instances', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Instansi'
}, ['Master/InstanceController','index'])

Route.add('/master/instances/create', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Input Data Instansi'
}, ['Master/InstanceController','create'])

Route.add('/master/instances/:id/edit', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Edit Instansi'
}, ['Master/InstanceController','edit'])

// Master Assignment
Route.add('/master/assignments', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Penugasan'
}, ['Master/AssignmentController','index'])

Route.add('/master/assignments/create', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Input Data Penugasan'
}, ['Master/AssignmentController','create'])

Route.add('/master/assignments/:id/edit', 'views/layouts/app', isAuthenticated, () => {
    document.title = 'Edit Penugasan'
}, ['Master/AssignmentController','edit'])


export default Route.getRoutes();