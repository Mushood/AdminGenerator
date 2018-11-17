const layout = [
    {
        path: '/',
        component: resolve => require(['pages/index'], resolve),
        meta: {
            title: "Dashboard",
        }
    },
    {
        path: '/gallery',
        component: resolve => require(['pages/gallery/index'], resolve),
        meta: {
            title: "Media",
        }
    },
    {
        path: '/gallery-create',
        component: resolve => require(['pages/gallery/create'], resolve),
        meta: {
            title: "Create Media",
        }
    },
    {
        path: '/gallery-show/:url',
        component: resolve => require(['pages/gallery/show'], resolve),
        meta: {
            title: "Edit Media",
        }
    },
    {
        path: '/page',
        component: resolve => require(['pages/page/index'], resolve),
        meta: {
            title: "Page",
        }
    },
    {
        path: '/page/:ref',
        component: resolve => require(['pages/page/create'], resolve),
        meta: {
            title: "Edit Page",
        }
    },
    //INJECT_ROUTESJS_HERE
]

export default layout
