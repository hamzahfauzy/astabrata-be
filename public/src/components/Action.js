/**
 * 
 * @param {action { url(data), route, class, label, id}, data} props 
 * @param {*} ctx 
 * @returns 
 */
export default async function (props, ctx) {
    const userData = ctx.app.getGlobal('userData')
    const userPermissions = userData.permissions
    
    const action = props.action
    const actionPermissions = action.permissions

    if(!ctx.app.can(userPermissions, actionPermissions)) return '';

    const data = props.data ?? {}
    var html = ''
    if(action.type == 'link')
    {
        const route = action.url ? action.url(data) : action.route
        html = '<a href="'+route+'" class="'+action.class+'" router-link>'+action.label+'</a>'
    }

    if(action.type == 'button')
    {
        html = '<button class="'+action.class+'" data-rowid="'+(data.id ?? '')+'">'+action.label+'</button>'
    }
    
    return html
}