import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

require('./bootstrap');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';

import * as GmapVue from 'gmap-vue';
import {library} from "@fortawesome/fontawesome-svg-core";
import {faBackspace} from "@fortawesome/free-solid-svg-icons";

Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);

library.add(faBackspace);

Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.use(GmapVue, {
   load: {
       key: process.env.MIX_GEOCODE_API_KEY
   },
    installComponents: true
});

const app = document.getElementById('app');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
