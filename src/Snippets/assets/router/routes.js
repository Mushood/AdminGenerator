import layout_routes from './layout'

const routes = [
    {
    path: '/',
    component: resolve => require(['src/layout'], resolve),
    children: layout_routes
    },
    {
        path: '/login',
        component: resolve => require(['pages/login'], resolve),
        meta: {
            title: "Login",
        }
    },
]
export default routes
