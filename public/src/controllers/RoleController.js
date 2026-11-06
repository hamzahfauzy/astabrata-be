import { CrudController } from "./CrudController.js";

export default class RoleController extends CrudController {

    config = {
        baseUrl: '/roles',
        state: {
            data: [],
            meta: {
                from: 0,
                last_page: 0,
                page: 0,
                per_page: 20,
                to: 0,
                total: 0
            },
            endpoint: '/roles',
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
            title: 'Peran',
            subtitle: 'Daftar peran dalam sistem',
            createLabel: 'Buat Peran',
            breadcrumbs: [],
            columns: [
                {label: 'Nama', key: 'name'},
                {
                    label: 'Slug', key: 'slug',
                    value: function(data, record) {
                        return '<span class="font-monospace small">' + data + '</span>'
                    }
                },
                {label: 'Deskripsi', key: 'description'},
            ],
            filters: [],
            actions: [
                {
                    label: 'Lihat', type: 'link', 
                    url: row => { return '/roles/' + row.id }, 
                    class: '',
                    permissions: ['roles.view'],
                },
                {
                    label: 'Edit', type: 'link', 
                    url: row => { return '/roles/' + row.id + '/edit' }, 
                    class: '',
                    permissions: ['roles.edit'],
                },
                {
                    label: 'Hapus', type: 'button', 
                    class: 'btn-delete text-danger',
                    permissions: ['roles.delete'],
                },
            ],
            headerActions: [
                {
                    label: '<i class="bi bi-plus-lg me-2"></i> Buat Peran', route: '/roles/create', 
                    class: 'btn btn-success rounded-0', permissions: ['roles.create'],
                    type: 'link'
                }
            ],
        },
        view: {
            breadcrumbs: [
                {label: 'Peran', route: '/roles'}
            ],
            title: 'Detail Peran',
            subtitle: 'Detail data peran',
            fields: [
                {label: 'Nama', key: 'name'},
                {label: 'Slug', key: 'slug'},
                {label: 'Deskripsi', key: 'description'},
                {
                    label: 'Hak Akses', key: 'permission_data', 
                    value: function(data, record){
                        var html = '';
                        for(const permission of data)
                        {
                            html += '<span class="badge border text-success-emphasis bg-success-subtle border-success-subtle me-1">'+permission.name+'</span>'
                        }
                        return html
                    }
                },
            ],
        },
        create: {
            title: 'Buat Peran',
            subtitle: 'Isi form untuk membuat peran',
            breadcrumbs: [
                {label: 'Peran', route: '/roles'}
            ],
            fields: [
                {name: 'name', label: 'Nama', type: 'text', required: true},
                {name: 'slug', label: 'Slug', type: 'text', required: true},
                {name: 'description', label: 'Deskripsi', type: 'textarea', required: true},
                {
                    name: 'permissions', label: 'Hak Akses', 
                    type: 'checkbox',
                    options: [],
                    ajax: {
                        response: {value: 'id', label: 'name'},
                        url: '/permissions',
                    },
                    required: true
                },
            ],
        },
        edit: {
            title: 'Edit Peran',
            subtitle: 'Ubah data pada form untuk mengedit peran',
            breadcrumbs: [
                {label: 'Peran', route: '/roles'}
            ],
            fields: [
                {name: 'name', label: 'Nama', type: 'text', required: true},
                {name: 'slug', label: 'Slug', type: 'text', required: true},
                {name: 'description', label: 'Deskripsi', type: 'textarea', required: true},
                {
                    name: 'permissions', label: 'Hak Akses', 
                    type: 'checkbox',
                    options: [],
                    ajax: {
                        response: {value: 'id', label: 'name'},
                        url: '/permissions',
                    },
                    required: true
                },
            ],
        },
    }

}