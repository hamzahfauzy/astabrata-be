import view from '../core/view.js'

export default class IndexController {

    async index(ctx) {
        return await view.render('index')
    }
    
    async monev(ctx) {
        return await view.render('pages/monev')
    }
    
    async graduasi(ctx) {
        return await view.render('pages/graduasi')
    }
    
    async pascaGraduasi(ctx) {
        return await view.render('pages/pasca-graduasi')
    }
    
    async penilaianUlang(ctx) {
        return await view.render('pages/penilaian-ulang')
    }
    
    async uep(ctx) {
        return await view.render('pages/uep')
    }

    async setting(ctx){
        return await view.render('pages/setting')
    }

    async profile(ctx){
        const fields = [
            {label: 'Nama', key: 'name'},
            {label: 'Email', key: 'email'},
            {label: 'Username', key: 'username'},
            {
                label: 'Status', key: 'is_active', 
                value: function(data, record){
                    const template = '<span class="badge rounded-pill px-3 py-2 border '+(data ? 'text-success-emphasis bg-success-subtle border-success-subtle' : 'text-danger-emphasis bg-danger-subtle border-danger-subtle')+'">value</span>'
                    return template.replace('value', data ? 'Active' : 'Not Active')
                }
            },
            {
                label: 'Peran', key: 'roles', 
                value: function(data, record){
                    var html = '';
                    for(const role of data)
                    {
                        html += '<span class="badge border text-success-emphasis bg-success-subtle border-success-subtle me-1">'+role.name+'</span>'
                    }
                    return html
                }
            }
        ]
        return await view.render('pages/profile', {
            fields
        })
    }

    async editProfile(ctx){
        const fields = [
            {name: 'name', label: 'Nama', type: 'text', required: true},
            {name: 'email', label: 'Email', type: 'text', required: true},
            {name: 'username', label: 'Username', type: 'text', required: true},
            {name: 'password', label: 'Kata Sandi', type: 'password', required: true},
        ]

        ctx.onMounted(() => {

            ctx.on('#update-profile-form', 'submit', async e => {
                e.preventDefault()

                document.querySelector('.btn-submit').innerHTML = 'Submiting...'

                try {
                    
                    const formData = new FormData(e.currentTarget)
    
                    const  response = await ctx.http.put('/me', formData)
    
                    Swal.fire("Profile updated!", "", "success");
                    
                    ctx.app.provide('userData', response.data)
                    ctx.app.refresh('vtp-account', {props: {}, ctx})
                    ctx.redirect('/profile')
                    
                } catch (error) {
                    
                }

                document.querySelector('.btn-submit').innerHTML = 'Submit'
                return false;
            })
        })

        return await view.render('pages/edit-profile', {
            fields
        })
    }
}