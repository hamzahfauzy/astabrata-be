import './core/directive.js'
import Router from "./core/router.js";
import routes from "./routes.js";
import Http from "./core/http.js";
import App from './core/app.js';
import menu from './config/menu.js';
import Input from './components/Input.js';
import Action from './components/Action.js';
import Breadcrumb from './components/Breadcrumb.js';
import view from './core/view.js';
import { doLogout, isRouteActive, setSideMenuActive, filterAction, isAllowed } from './libs/functions.js';
import { apiUrl } from './config/env.js';
import Dashboard from './components/Dashboard.js';

Http.defaults.baseURL = apiUrl;

Http.defaults.timeout = 10000;

const app = new App;

app.on('.btn-logout', 'click', doLogout);

app.function('isRouteActive', isRouteActive)
app.function('filterAction', actions => {
    return filterAction(actions, app)
})

app.function('isAllowed', permissions => {
    return isAllowed(permissions, app)
})

app.function('isStageAllowed', (userPermissions, stagePermission) => {
    const permissions = Array.isArray(stagePermission)
        ? stagePermission
        : [stagePermission];

    return permissions.some(permission =>
        userPermissions.includes(permission)
    );
})

app.provide('menus', menu)
app.component('vtp-input', Input)
app.component('vtp-action', Action)
app.component('breadcrumb', Breadcrumb)
app.component('vtp-dashboard', Dashboard)
app.component('vtp-notification', e => {
    return view.render('components/notification')
})

app.component('vtp-account', e => {
    return view.render('components/account')
})

app.component('vtp-sidebar', e => {
    return view.render('components/sidebar')
})

app.component('vtp-dashboard-default', e => {
    return view.render('components/dashboard/default')
})

app.component('vtp-dashboard-desa', e => {
    return view.render('components/dashboard/desa')
})

app.component('vtp-dashboard-pendamping', e => {
    return view.render('components/dashboard/pendamping')
})

app.component('vtp-dashboard-kecamatan', e => {
    return view.render('components/dashboard/kecamatan')
})

app.component('vtp-dashboard-dinsos', e => {
    return view.render('components/dashboard/dinsos')
})

app.component('vtp-dashboard-asesor', e => {
    return view.render('components/dashboard/asesor')
})

app.component('vtp-dashboard-opd', e => {
    return view.render('components/dashboard/opd')
})

app.component('vtp-dashboard-kabupaten', e => {
    return view.render('components/dashboard/kabupaten')
})

const router = new Router(routes, app);

router.start();