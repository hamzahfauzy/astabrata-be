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


export default Route.getRoutes();