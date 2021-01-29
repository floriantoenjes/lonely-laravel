import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

require('./bootstrap');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';

import * as GmapVue from 'gmap-vue';
import {library} from "@fortawesome/fontawesome-svg-core";
import {faAngleDown, faArrowLeft, faInfoCircle, faSync} from "@fortawesome/free-solid-svg-icons";
import {faBell, faComments, faFrownOpen} from "@fortawesome/free-regular-svg-icons";

import GoogleMapCluster from 'gmap-vue/dist/components/cluster';
import LonelySettingsForm from "./Components/LonelySettingsForm";
import LonelyMap from "./Components/LonelyMap";

Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);

library.add(
    faAngleDown,
    faArrowLeft,
    faBell,
    faComments,
    faInfoCircle,
    faFrownOpen,
    faSync
);

Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.component('lonely-settings-form', LonelySettingsForm);
Vue.component('lonely-map', LonelyMap);

Vue.use(GmapVue, {
   load: {
       key: process.env.MIX_GEOCODE_API_KEY,
       libraries: 'places'
   },
    installComponents: true
});

Vue.component('GmapCluster', GoogleMapCluster);

export const bus = new Vue();

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
