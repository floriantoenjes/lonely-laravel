<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your Lonely Dashboard
            </h2>
        </template>

        <div class="flex flex-row h-full">
            <div class="m-4 p-16 w-1/4 bg-white">

                <lonely-settings-form
                    :loading="loading"
                    :lonely="lonely"
                    :user-lonely-settings="userLonelySettings"
                    @set-lonely="setLonely"
                ></lonely-settings-form>

                <button type="button"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8 w-full"
                        @click="refreshDashBoard" v-if="lonely">
                    Refresh
                    <font-awesome-icon icon="sync" class="ml-4" size="lg" :spin="refreshing"></font-awesome-icon>
                </button>
            </div>

            <div class="m-4 w-3/4 h-auto bg-white relative">
                <div v-if="!lonely" class="h-full flex justify-center items-center">
                    <p class="text-2xl" style="width: max-content;">Are you lonely  today? Mark yourself as lonely!</p>
                </div>

                <lonely-map
                    :lonely="lonely"
                    :user-lonely-settings="userLonelySettings"
                    :lonely-persons="lonelyPersons"
                    :activities="activities">
                </lonely-map>
            </div>

        </div>

    </app-layout>
</template>

<script>
import AppLayout from "../Layouts/AppLayout";
import JetButton from '../Jetstream/Button';
import JetFormSection from '../Jetstream/FormSection';
import JetInput from '../Jetstream/Input';
import Input from "../Jetstream/Input";
import { Inertia } from '@inertiajs/inertia';
import Button from "../Jetstream/Button";

import { gmapApi } from 'gmap-vue';
import {bus} from "../app";

export default {
    name: "LonelyDashboard",
    components: {
        Button,
        Input,
        AppLayout,
        JetButton,
        JetFormSection,
        JetInput
    },
    props: {
        'lonely': {},
        'userLonelySettings': {},
        'lonelyPersonsProp': {},
        'activitiesProp': {}
    },
    data() {
        return {
            loading: false,
            refreshing: false,

            lonelyPersons: this.lonelyPersonsProp,
            activities: this.activitiesProp,
        }
    },
    computed: {
        google: gmapApi,
    },
    mounted() {
        Inertia.on('start', event => {
            this.loading = true;
        });
        Inertia.on('finish', event => {
            this.loading = false;
        });
    },
    methods: {
        setLonely() {
            bus.$emit('setLonely');
        },
        refreshDashBoard() {
            this.refreshing = true;
            axios.get('/lonely-dashboard/refresh').then(response => {

                this.lonelyPersons = response.data.lonelyPersons;
                this.activities = response.data.activities;

                setTimeout(() => {
                    this.refreshing = false;
                }, 3000);
            });
        },

    }
}
</script>

<style scoped>
</style>
