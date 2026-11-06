import view from "../core/view.js";

export default async function (props, ctx) {
    const userData = ctx.app.getGlobal('userData')
    const response = await ctx.http.get('/dashboard')
    var compName = 'default'
    if(userData.roles.map(role => role.name).includes('Admin Kabupaten'))
    {
        compName = 'kabupaten'
    }
    else if(userData.roles.map(role => role.name).includes('Desa'))
    {
        compName = 'desa'
    }
    else if(userData.roles.map(role => role.name).includes('Pendamping'))
    {
        compName = 'pendamping'
    }
    else if(userData.roles.map(role => role.name).includes('Kecamatan'))
    {
        compName = 'kecamatan'

        ctx.onMounted(() => {
            ctx.on('.btn-ajukan', 'click', e => {
                const el = e.currentTarget
                const id = el.dataset.rowid
                
                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah anda yakin akan mengajukan data ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                }).then(async (result) => {
                /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed)
                    {
                        await ctx.http.get('/kpm/profiles/' + id + '/ajukan')
                        Swal.fire("Data berhasil diajukan!", "", "success");
                        ctx.refresh()
                    }
                });
            })
        })
    }
    else if(userData.roles.map(role => role.name).includes('Dinsos'))
    {
        compName = 'dinsos'
    }
    else if(userData.roles.map(role => role.name).includes('OPD'))
    {
        compName = 'opd'
    }
    else if(userData.roles.map(role => role.name).includes('Asesor'))
    {
        compName = 'asesor'
    }
    else if(userData.roles.map(role => role.name).includes('Tim Pelaksana'))
    {
        compName = 'pelaksana'
    }
    else if(userData.roles.map(role => role.name).includes('Tim Teknis'))
    {
        compName = 'teknis'
    }
    
    return view.render('components/dashboard/' + compName, {
        data: response.data
    })
}