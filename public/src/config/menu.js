export default [
    {
        label: 'Dashboard',
        name:'',
        icon: 'bi bi-grid',
        permissions: ['dashboard.index','desa','pendamping','opd','dinsos','kecamatan','kabupaten','asesor','pelaksana','teknis'],
        route: '/',
        activeState: '/',
        children: []
    },

    {
        label: 'Master',
        name: 'master',
        icon: 'bi bi-folder2-open',
        permissions: [],
        activeState: ['/master/regions/*', '/master/villages/*', '/master/instances/*', '/master/educations/*', '/master/periods/*', '/master/assignments/*'],
        children: [
            {label: 'Periode', permissions: ['periods.index'], activeState: '/master/periods/*', route: '/master/periods'},
            // {label: 'Kecamatan', permissions: ['regions.index'], activeState: '/master/regions/*', route: '/master/regions'},
            // {label: 'Desa / Kelurahan', permissions: ['villages.index'], activeState: '/master/villages/*', route: '/master/villages'},
            {label: 'Instansi', permissions: ['instances.index'], activeState: '/master/instances/*', route: '/master/instances'},
            {label: 'Penugasan', permissions: ['assignments.index'], activeState: '/master/assignments/*', route: '/master/assignments'},
        ]
    },
    
]