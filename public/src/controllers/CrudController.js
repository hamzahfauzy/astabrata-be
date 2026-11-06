import view from '../core/view.js'

export class CrudController {
    constructor(config = {}) {
        this.config = {
            baseUrl: '',
            state: {
                data: [],
                meta: {
                    from: 0,
                    last_page: 0,
                    page: 0,
                    per_page: 0,
                    to: 0,
                    total: 0
                },
                endpoint: '/crud',
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
                title: '',
                subtitle: '',
                breadcrumbs: [],
                columns: [],
                filters: [],
                actions: [],
                headerActions: [
                    {label: 'Create Data', route: '', class: '', icon:''}
                ],
            },
            view: {
                breadcrumbs: [],
                title: 'View',
                subtitle: 'View Data',
                fields: [],
            },
            create: {
                breadcrumbs: [],
                title: 'Create',
                subtitle: 'Create Data',
                fields: [],
            },
            edit: {
                breadcrumbs: [],
                title: 'Edit Data',
                subtitle: 'Edit data',
                fields: [],
            },
            ...config
        };
    }

    async index(ctx){

        if(!ctx.state)
        {
            ctx.state = this.config.state
            ctx.state.success = ctx.flash("success");
        }

        ctx.onMounted(() => {

            if(!ctx.state.loaded) {
                ctx.state.loaded = true;
                this.loadData(ctx);
            }

            ctx.on('#searchForm', 'submit', e => {
                e.preventDefault()

                const search = document.querySelector('input[name=search]').value

                ctx.redirect(ctx.state.endpoint + '/?search=' + search)

                return false;
            })

            ctx.on('.btn-delete', 'click', e => {
                const el = e.currentTarget
                const id = el.dataset.rowid
                
                Swal.fire({
                    title: "Are you sure to delete this data?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                }).then(async (result) => {
                /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed)
                    {
                        await ctx.http.delete(ctx.state.endpoint + '/' + id)
                        Swal.fire("Data deleted!", "", "success");
                        this.loadData(ctx)
                    }
                });
            })
        })

        return await view.render('crud/index', {
            ...ctx.state, 
            baseUrl: this.config.baseUrl, 
            list: this.config.list
        })

    }

    async loadData(ctx){
        ctx.state.query.search = ctx.query.search ?? ''
        ctx.state.query.page = ctx.query.page ?? 1
        
        const response = await ctx.http.get(ctx.state.endpoint, {
            query: ctx.state.query
        })

        ctx.state.data = response.data
        ctx.state.isLoading = false
        ctx.state.meta = response.meta
        ctx.refresh()
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

        return await view.render('crud/form', {...ctx.state, pageAttr: this.config.create, baseUrl: this.config.baseUrl, data: {}})
    }

    async edit(ctx){

        if(!ctx.state)
        {
            ctx.state = this.config.state
        }

        ctx.onMounted(() => {
            ctx.on('#crud-form', 'submit', async e => {
                e.preventDefault()

                const formData = new FormData(e.target)

                await ctx.http.put(ctx.state.endpoint + '/' + ctx.params.id, formData)

                ctx.flash("success", "Data updated.");

                ctx.redirect(this.config.baseUrl)

                return false;
            })
        })

        const response = await ctx.http.get(ctx.state.endpoint + '/' + ctx.params.id)
        return await view.render('crud/form', {...ctx.state, pageAttr: this.config.edit, baseUrl: this.config.baseUrl, data: response.data})

    }
    
    async show(ctx){

        if(!ctx.state)
        {
            ctx.state = this.config.state
        }

        const response = await ctx.http.get(ctx.state.endpoint + '/' + ctx.params.id)
        return await view.render('crud/detail', {...ctx.state, pageAttr: this.config.view, baseUrl: this.config.baseUrl, data: response.data})

    }
}