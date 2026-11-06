import { CrudController } from "../CrudController.js";
import view from "../../core/view.js";

export default class InstanceController extends CrudController {

    config = {
        baseUrl: '/master/instances',
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
            endpoint: '/master/instances',
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
            title: 'Instansi',
            subtitle: 'Daftar Instansi',
            createLabel: 'Input Data Instansi',
            breadcrumbs: [],
            columns: [
                {label: 'Nama', key: 'name'},
                {label: 'Wilayah', key: 'region_type', value: (data, row) => {
                    return data == 0 ? row.region : row.region_name + (row.village_name ? ' - ' + row.village_name : '')
                }},
            ],
            filters: [],
            actions: [
                {
                    label: 'Edit', type: 'link', 
                    url: row => { return '/master/instances/' + row.id + '/edit' }, 
                    class: '',
                    permissions: ['instances.edit'],
                },
                {
                    label: 'Hapus', type: 'button', 
                    class: 'btn-delete text-danger',
                    permissions: ['instances.delete'],
                },
            ],
            headerActions: [
                {
                    label: '<i class="bi bi-plus-lg me-2"></i> Input Data Instansi', route: '/master/instances/create', 
                    class: 'btn btn-success rounded-0', permissions: ['instances.create'],
                    type: 'link'
                }
            ],
        },
        view: {
            breadcrumbs: [
                {label: 'Instansi', route: '/master/instances'}
            ],
            title: 'Detail Instansi',
            subtitle: 'Data Detail Instansi',
            fields: [
                {label: 'Nama', key: 'name'},
            ],
        },
        create: {
            title: 'Input Data Instansi',
            subtitle: 'Isi form dibawah ini',
            breadcrumbs: [
                {label: 'Instansi', route: '/master/instances'}
            ],
            fields: [
                {name: 'name', label: 'Nama', type: 'text', required: true},
                {name: 'region_type', label: 'Jenis Wilayah', type: 'select', options: [
                    {label: 'Pemerintah Kabupaten', value: 0},
                    {label: 'Pemerintah Kecamatan', value: 1},
                    {label: 'Pemerintah Desa / Kelurahan', value: 2},
                ], required: true},
                {name: 'region_id', label: 'Kecamatan', type: 'select', options: [], attr: {wrapperClassName: 'd-none'}},
                {name: 'village_id', label: 'Desa / Kelurahan', type: 'select', options: [], attr: {wrapperClassName: 'd-none'}},
            ],
        },
        edit: {
            title: 'Edit Instansi',
            subtitle: 'Isi form untuk mengedit data Instansi',
            breadcrumbs: [
                {label: 'Instansi', route: '/master/instances'}
            ],
            fields: [
                {name: 'name', label: 'Nama', type: 'text', required: true},
            ],
        },
    }

    async create(ctx){
    
        if(!ctx.state)
        {
            ctx.state = this.config.state
        }

        ctx.onMounted(() => {

            ctx.on('[name="region_type"]', 'change', async e => {
                const val = e.target.value
                
                if(val > 0)
                {
                    const regions = await ctx.http.get('/master/regions?per_page=30')
                    const selectMenu = document.querySelector('select[name="region_id"]');
                    selectMenu.closest('.form-group').classList.remove('d-none')

                    // Clear the dropdown
                    selectMenu.innerHTML = '<option value="0">Choose</option>';

                    // Loop through the data and append each option
                    regions.data.forEach(item => {
                        selectMenu.add(new Option(item.name, item.id));
                    });

                    if(val == 2)
                    {
                        const selectMenuVillage = document.querySelector('select[name="village_id"]');
                        selectMenuVillage.innerHTML = '<option value="0">Choose</option>';
                        selectMenuVillage.closest('.form-group').classList.remove('d-none')
                    }
                }
                else
                {
                    document.querySelector('select[name="region_id"]').innerHTML = ''
                    document.querySelector('select[name="village_id"]').innerHTML = ''

                    document.querySelector('select[name="region_id"]').closest('.form-group').classList.add('d-none')
                    document.querySelector('select[name="village_id"]').closest('.form-group').classList.add('d-none')

                }
            })

            ctx.on('[name="region_id"]', 'change', async e => {
                const selectMenuVillage = document.querySelector('select[name="village_id"]');
                if(selectMenuVillage && !selectMenuVillage.closest('.form-group').classList.contains('d-none'))
                {
                    const region = document.querySelector('select[name="region_id"]');
                    const villages = await ctx.http.get('/master/villages/find-by-region-id/' + region.value)
    
                    // Clear the dropdown
                    selectMenuVillage.innerHTML = '';
    
                    // Loop through the data and append each option
                    villages.data.forEach(item => {
                        selectMenuVillage.add(new Option(item.name, item.id));
                    });
                }
            })

            ctx.on('#crud-form', 'submit', async e => {
                e.preventDefault()

                const formData = new FormData(e.currentTarget)

                if(formData.get('village_id') == 'Choose')
                {
                    formData.delete('village_id')
                }

                await ctx.http.post(ctx.state.endpoint, formData)

                ctx.flash("success", "Data berhasil disimpan.");

                ctx.redirect(this.config.baseUrl)

                return false;
            })
        })

        return await view.render('crud/form', {...ctx.state, pageAttr: this.config.create, baseUrl: this.config.baseUrl, data: {region_type: 0,village_id:0,region_id:0}})
    }

}