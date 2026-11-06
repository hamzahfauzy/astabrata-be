export async function isAuthenticated(ctx)
{
    const token = localStorage.getItem('token')
    if(!token)
    {
        ctx.redirect('/login')
        return false
    }

    ctx.http.defaults.headers = {
        Authorization: 'Bearer ' + token
    }

    if(ctx.app.getGlobal('userData'))
    {
        return true;
    }

    try {
        const response = await ctx.http.get('/me')

        if(response.data)
        {
            ctx.app.provide('userData', response.data)
            return true
        }
        
    } catch (error) {
        
    }

    ctx.redirect('/login')
    return false
}

export function isRouteActive(activeState){
    const path = window.location.pathname.replace(/\/$/, "");

    const match = (route) => {
        route = route.replace(/\/$/, "");

        // Wildcard
        if (route.endsWith("/*")) {
            const base = route.slice(0, -2);
            return path === base || path.startsWith(base + "/");
        }

        // Exact match
        return path === route;
    };

    if (Array.isArray(activeState)) {
        return activeState.some(match);
    }

    return match(activeState);
}

export function filterAction(actions, app){
    const userData = app.getGlobal('userData')
    const userPermissions = userData.permissions

    const _actions = []

    for(const action of actions)
    {
        const actionPermissions = action.permissions
    
        if(app.can(userPermissions, actionPermissions)){
            _actions.push(action)
        }
    }

    return _actions
    
}

export function isAllowed(permissions, app){
    const userData = app.getGlobal('userData')
    const userPermissions = userData.permissions

    return app.can(userPermissions, permissions)
}

export function doLogout(){
    localStorage.clear();
    window.location = '/login'
}

export function setSideMenuActive(e){
    if(e.target.classList.contains('active'))
    {
        return
    }

    document.querySelectorAll('.vtp-side-menu').forEach(side => {
        side.classList.remove('active')
    })
    e.target.classList.add('active')
}