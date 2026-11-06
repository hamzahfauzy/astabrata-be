import { CrudController } from "../CrudController.js";

export default class PeriodController extends CrudController {

    config = {
        baseUrl: '/master/periods',
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
            endpoint: '/master/periods',
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
            title: 'Periode',
            subtitle: 'Daftar Periode',
            createLabel: 'Input Data Periode',
            breadcrumbs: [],
            columns: [
                {label: 'Nama', key: 'name'},
                {
                    label: 'Status', key: 'status', 
                    value: function(data, record){
                        const template = '<span class="badge rounded-pill px-3 py-2 border '+(data == 'ACTIVE' ? 'text-success-emphasis bg-success-subtle border-success-subtle' : 'text-danger-emphasis bg-danger-subtle border-danger-subtle')+'">value</span>'
                        return template.replace('value', data == 'ACTIVE' ? 'Active' : 'In Active')
                    }
                },
            ],
            filters: [],
            actions: [
                // {
                //     label: 'View', type: 'link', 
                //     url: row => { return '/master/periods/' + row.id }, 
                //     class: '',
                //     permissions: ['periods.view'],
                // },
                {
                    label: 'Edit', type: 'link', 
                    url: row => { return '/master/periods/' + row.id + '/edit' }, 
                    class: '',
                    permissions: ['periods.edit'],
                },
                {
                    label: 'Hapus', type: 'button', 
                    class: 'btn-delete text-danger',
                    permissions: ['periods.delete'],
                },
            ],
            headerActions: [
                {
                    label: '<i class="bi bi-plus-lg me-2"></i> Input Data Periode', route: '/master/periods/create', 
                    class: 'btn btn-success rounded-0', permissions: ['periods.create'],
                    type: 'link'
                }
            ],
        },
        view: {
            breadcrumbs: [
                {label: 'Periode', route: '/master/periods'}
            ],
            title: 'Detail Periode',
            subtitle: 'Data Detail Periode',
            fields: [
                {label: 'Nama', key: 'name'},
            ],
        },
        create: {
            title: 'Input Data Periode',
            subtitle: 'Isi form dibawah ini',
            breadcrumbs: [
                {label: 'Periode', route: '/master/periods'}
            ],
            fields: [
                {name: 'name', label: 'Nama', type: 'text', required: true},
                {name: 'status', label: 'Status', type: 'select', options: [
                    {label: 'Aktif', value: 'ACTIVE'},
                    {label: 'Tidak Aktif', value: 'INACTIVE'},
                ], required: true},
            ],
        },
        edit: {
            title: 'Edit Periode',
            subtitle: 'Isi form untuk mengedit data Periode',
            breadcrumbs: [
                {label: 'Periode', route: '/master/periods'}
            ],
            fields: [
                {name: 'name', label: 'Nama', type: 'text', required: true},
                {name: 'status', label: 'Status', type: 'select', options: [
                    {label: 'Aktif', value: 'ACTIVE'},
                    {label: 'Tidak Aktif', value: 'INACTIVE'},
                ], required: true},
            ],
        },
    }

}