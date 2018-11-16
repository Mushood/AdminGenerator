import 'es6-promise/auto'
import Vue from 'vue'
import Vuex from 'vuex'
import mutations from './mutations'

Vue.use(Vuex)

function addDays(noOfDays) {
    return (noOfDays * 24 * 60 * 60 * 1000)
}

//=======vuex store start===========
const store = new Vuex.Store({
    state: {
        left_open: true,
        preloader: true,
        site_name: "Website Admin",
        page_title: null,
        user: {
            name: "",
            picture: require("img/authors/prf4.jpg"),
            job: "",
            email: "",
            token: "",
        },
        cal_events: [{
            id: 0,
            title: 'Office',
            start: Date.now(),
            end: Date.now() + addDays(1)
        }, {
            id: 1,
            title: 'Holidays',
            start: Date.now() + addDays(3),
            end: Date.now() + addDays(4)
        }],
        // Add your application keys
        gmap_key: '',
        openWeather_key: '',
        google_analytics_key: null,
        locale: '',
        url: '',
    },
    mutations
})
//=======vuex store end===========
export default store
