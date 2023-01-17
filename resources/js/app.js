import Vue from 'vue';

import store from './store'
import router from './Services/Router/Router';

import Axios from './Services/httpRequest/axios';
Vue.prototype.$axios = Axios

import VueApexCharts from 'vue-apexcharts'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)

import LoaderPanel from './pages/LoaderPanel';
Vue.component('loader-panel', LoaderPanel);

import HeaderPanel from './pages/HeaderPanel';
Vue.component('header-panel', HeaderPanel);

import SidebarPanel from './pages/SidebarPanel';
Vue.component('sidebar-panel', SidebarPanel);

import VueSimpleAlert from "vue-simple-alert";
Vue.use(VueSimpleAlert);

import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
Vue.component('date-picker', VuePersianDatetimePicker);

import money from 'v-money'

import vuetify from './vuetify'

Vue.use(money, {
    decimal: '.',
    thousands: ',',
    prefix: '',
    suffix: '',
    precision: 0,
    masked: false
})

// import io from 'socket.io-client';

import { mapState, mapActions } from 'vuex';
Vue.mixin({
    data: function() {
        return {
            ImageUrl: 'http://localhost:8000/uploads/',
            StaticUrl: 'http://localhost:8000/',
            // ImageUrl:'https://dl.polexofficial.com/uploads/',
            // StaticUrl:'https://panel.polexofficial.com/',
            // socket : io('http://localhost:3000/'),
            // socket : io('https://node.arzine.org/')
        }
    },
    computed: {

    },
    methods: {
        ...mapActions([
            'SPIN_LOADING'
        ]),
        formatPrice(value) {
            // let val = (value/1).replace('.', '')
            // return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            if (value) {
                const number = parseInt(value)
                const val = Number((number).toFixed(1)).toLocaleString()
                return val
            } else {
                return value
            }
        },
        just_float(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                evt.preventDefault();
            } else {
                return true;
            }
        },
    }
})

window.Vue = require('vue');

const app = new Vue({
    el: '#arzineh',
    router,
    vuetify,
    store
});
export default app;