import { CrudController } from "./CrudController.js";
import view from "../core/view.js";

export default class UserController extends CrudController {

    config = {
        baseUrl: '/users',
        state: {
            data: [],
            selectedRoles: [],
            meta: {
                from: 0,
                last_page: 0,
                page: 0,
                per_page: 0,
                to: 0,
                total: 0
            },
            endpoint: '/users',
            query: {
                per_page: 20,
                page: 1,
                search: ''
            },
            loaded: false,
            isLoading: true
        },
        searchFields: [],
        list: {
            title: 'Pengguna',
            subtitle: 'Daftar Pengguna',
            createLabel: 'Buat Pengguna',
            breadcrumbs: [],
            columns: [
                {
                    label: 'Nama', key: 'name',
                    value: function(data, record) {
                        return '<a href="/users/'+record.id+'" class="text-decoration-none link-brand fw-bold" router-link>' + data + '</a>'
                    }
                },
                {
                    label: 'Email', key: 'email',
                    value: function(data, record) {
                        return '<a href="mailto:'+data+'" class="text-decoration-none link-brand font-monospace small" router-link>' + data + '</a>'
                    }
                },
                {label: 'Username', key: 'username'},
                {
                    label: 'Status', key: 'is_active', 
                    value: function(data, record){
                        const template = '<span class="badge rounded-pill px-3 py-2 border '+(data ? 'text-success-emphasis bg-success-subtle border-success-subtle' : 'text-danger-emphasis bg-danger-subtle border-danger-subtle')+'">value</span>'
                        return template.replace('value', data ? 'Active' : 'Not Active')
                    }
                },
            ],
            filters: [],
            actions: [
                {
                    label: 'Lihat', type: 'link', 
                    url: row => { return '/users/' + row.id }, 
                    class: '',
                    permissions: ['users.view'],
                },
                {
                    label: 'Edit', type: 'link', 
                    url: row => { return '/users/' + row.id + '/edit' }, 
                    class: '',
                    permissions: ['users.edit'],
                },
                {
                    label: 'Hapus', type: 'button', 
                    class: 'btn-delete text-danger',
                    permissions: ['users.delete'],
                },
            ],
            headerActions: [
                {
                    label: '<i class="bi bi-plus-lg me-2"></i> Buat Pengguna', route: '/users/create', type: 'link',
                    class: 'btn btn-success rounded-0',
                    permissions: ['users.create'],
                }
            ],
        },
        view: {
            breadcrumbs: [
                {label: 'Pengguna', route: '/users'}
            ],
            title: 'Detail Pengguna',
            subtitle: 'Detail data pengguna',
            fields: [
                {label: 'Nama', key: 'name'},
                {
                    label: 'Email', key: 'email',
                    value: function(data, record) {
                        return '<a href="mailto:'+data+'" class="text-decoration-none link-brand font-monospace small" router-link>' + data + '</a>'
                    }
                },
                {label: 'Username', key: 'username'},
                {
                    label: 'Status', key: 'is_active', 
                    value: function(data, record){
                        const template = '<span class="badge rounded-pill px-3 py-2 border '+(data ? 'text-success-emphasis bg-success-subtle border-success-subtle' : 'text-danger-emphasis bg-danger-subtle border-danger-subtle')+'">value</span>'
                        return template.replace('value', data ? 'Active' : 'Not Active')
                    }
                },
                {
                    label: 'Peran', key: 'role_data', 
                    value: function(data, record){
                        var html = '';
                        for(const role of data)
                        {
                            html += '<span class="badge border text-success-emphasis bg-success-subtle border-success-subtle me-1">'+role.name+'</span>'
                        }
                        return html
                    }
                }
            ],
        },
        create: {
            title: 'Buat Pengguna',
            subtitle: 'Isi form untuk membuat pengguna',
            breadcrumbs: [
                {label: 'Pengguna', route: '/users'}
            ],
            fields: [
                {name: 'name', label: 'Nama', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'text', required: true},
                {name: 'username', label: 'Username', type: 'text', required: true},
                {name: 'password', label: 'Kata Sandi', type: 'password', required: true},
                {name: 'is_active', label: 'Status', type: 'select', options: [
                    {label: 'Aktif', value: 1},
                    {label: 'Tidak Aktif', value: 0},
                ], required: true},
                {
                    name: 'roles', label: 'Peran', 
                    type: 'checkbox',
                    options: [],
                    ajax: {
                        response: {value: 'id', label: 'name'},
                        url: '/roles',
                    },
                    required: true
                }
            ],
        },
        edit: {
            title: 'Edit Pengguna',
            subtitle: 'Ubah data pada form untuk mengedit pengguna',
            breadcrumbs: [
                {label: 'Pengguna', route: '/users'}
            ],
            fields: [
                {name: 'name', label: 'Nama', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'text', required: true},
                {name: 'username', label: 'Username', type: 'text', required: true},
                {name: 'password', label: 'Kata Sandi', type: 'password', required: true},
                {name: 'is_active', label: 'Status', type: 'select', options: [
                    {label: 'Aktif', value: 1},
                    {label: 'Tidak Aktif', value: 0},
                ], required: true},
                {
                    name: 'roles', label: 'Peran', 
                    type: 'checkbox',
                    options: [],
                    ajax: {
                        response: {value: 'id', label: 'name'},
                        url: '/roles',
                    },
                    required: true
                },
            ],
        },
    }

    async create(ctx){
    
        if(!ctx.state)
        {
            ctx.state = this.config.state
        }

        ctx.onMounted(() => {

            ctx.on('#crud-form', 'submit', async e => {
                e.preventDefault()

                const formData = new FormData(e.currentTarget)

                await ctx.http.post(ctx.state.endpoint, formData)

                ctx.flash("success", "Data created.");

                ctx.redirect(this.config.baseUrl)

                return false;
            })
        })

        return await view.render('crud/form', {...ctx.state, pageAttr: this.config.create, baseUrl: this.config.baseUrl, data: {is_active: 1}})
    }
}