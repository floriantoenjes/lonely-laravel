<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your Lonely Dashboard
            </h2>
        </template>

        <div class="flex flex-row">
            <div class="m-4 p-16 w-1/4 h-full bg-white">
                <form class="flex flex-col" @submit.prevent="updateLonelySettings" id="lonelySettings">
                    <div class="mb-4">
                        <label for="city" class="block mr-4 mb-4">City:</label>
                        <input id="city" class="border rounded w-full" type="text" placeholder=" City" v-model="form.city">
                    </div>

                    <div class="mb-4">
                        <label for="postcode" class="block mr-4 mb-4">Postcode:</label>
                        <input id="postcode" class="border rounded w-full" type="text" placeholder=" Postcode"
                               v-model="form.postcode">
                    </div>

                    <div class="mb-4">
                        <label for="address" class="block mr-4 mb-4">Address:</label>
                        <input id="address" class="border rounded w-full" type="text" placeholder=" Address"
                               v-model="form.address">
                    </div>

                    <div class="mb-4">
                        <label for="radius" class="block mr-4 mb-4">Radius:</label>
                        <input id="radius" class="border rounded w-full" type="text" placeholder=" Radius"
                               v-model="form.radius">
                    </div>

                    <div class="mb-4">
                        <label for="age-from" class="block mr-4 mb-4">Age from:</label>
                        <input id="age-from" class="border rounded w-full" type="text" placeholder=" Radius"
                               v-model="form.ageFrom">
                    </div>

                    <div class="mb-4">
                        <label for="age-to" class="block mr-4 mb-4">Age to:</label>
                        <input id="age-to" class="border rounded w-full" type="text" placeholder=" Radius"
                               v-model="form.ageTo">
                    </div>

                    <button v-if="!lonely" type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8"
                            :disabled="loading" :class="{ 'opacity-50 cursor-not-allowed': loading }">I am lonely today!
                    </button>

                    <button v-else-if="lonely" type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8"
                            :disabled="loading" :class="{ 'opacity-50 cursor-not-allowed': loading }">I am not lonely anymore!
                    </button>
                </form>
            </div>

            <div class="m-4 w-3/4 h-auto bg-white relative">

                <p v-if="!lonely" class="text-2xl m-auto" style="width: max-content; margin-top: calc(15% - 1.5rem)">Are you lonely  today? Mark yourself as lonely!</p>

                <GmapMap
                    :center="{lat: +userLonelySettings.latitude, lng: +userLonelySettings.longitude}"
                    :zoom="zoomLevel"
                    map-type-id="roadmap"
                    style="width: 100%; height: 100%"
                    v-if="lonely"
                >
                    <GmapMarker
                        :key="index"
                        v-for="(m, index) in markers"
                        :position="m.position"
                        :clickable="true"
                        :draggable="false"
                        @click="openChat(m.user.id)"
                        @mouseover="showPersonDetails($event, m.user, m.position)"
                    />

                    <GmapInfoWindow
                        :opened="infoWindowOpen"
                        :position="infoWindowPosition"
                        :options="infoOptions"
                        v-if="modalUser">
                        <p class="text-lg mb-2">{{ modalUser.name }}<span v-if="modalUser.birthdate">, {{ calculateAge(modalUser.birthdate) }}</span></p>
                        <p class="text-lg text-blue-500 hover:text-black cursor-pointer text-center" @click="openChat(modalUser.id)" v-if="modalUser.id">Chat</p>
                    </GmapInfoWindow>
                </GmapMap>

                <div v-if="lonely" class="bg-white p-4 rounded" style="width: max-content; position: absolute; left: 0.5rem; top: 4rem">

                    <h2 class="text-2xl mb-2" v-if="lonelyPersons.length > 0">Lonely People:</h2>
                    <h2 class="text-2xl mb-8" v-else>No one seems to be lonely right now, sorry.</h2>

                    <ul class="list-disc list-inside">
                        <li v-for="lonelyPerson in lonelyPersons" @mouseover="showPersonDetails($event, lonelyPerson, getPositionFromUser(lonelyPerson))">
                            <inertia-link class="text-blue-500 hover:text-black" :href="route('chat', lonelyPerson.id)">{{ lonelyPerson.name }}</inertia-link>
                        </li>
                    </ul>
                </div>
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
        'lonelyPersons': {}
    },
    data() {
        return {
            form: this.$inertia.form({
                city: this.userLonelySettings?.city,
                postcode: this.userLonelySettings?.postcode,
                address: this.userLonelySettings?.address,
                radius: this.userLonelySettings?.radius,
                ageFrom: this.userLonelySettings?.meet_up_age_from,
                ageTo: this.userLonelySettings?.meet_up_age_to
            }, {
                resetOnSuccess: false
            }),
            loading: false,
            markers: [],

            modalUser: null,
            infoWindowOpen: false,
            infoWindowPosition: null,
            infoOptions: {
                pixelOffset: {
                    width: 0,
                    height: -40
                }
            }
        }
    },
    computed: {
        google: gmapApi,
        zoomLevel: function () {
            return parseInt(Math.log2(156543.03392 * Math.cos(this.userLonelySettings.latitude * Math.PI / 180) / this.userLonelySettings.radius));
        }
    },
    mounted() {
        Inertia.on('start', event => {
            this.loading = true;
        });

        Inertia.on('finish', event => {
            this.loading = false;
        });

        this.$gmapApiPromiseLazy().then(() => {
            for (const lonelyPerson of this.lonelyPersons) {
                this.markers.push({
                    position: new google.maps.LatLng({ lat: +lonelyPerson.user_lonely_setting.latitude, lng: +lonelyPerson.user_lonely_setting.longitude}),
                    weight: 50,
                    // userId: lonelyPerson.id,
                    // username: lonelyPerson.name,
                    user: lonelyPerson
                });
            }
            this.markers.push({
                position: new google.maps.LatLng({ lat: +this.userLonelySettings.latitude, lng: +this.userLonelySettings.longitude}),
                weight: 100
            })
        });

        if (this.lonely) {
            this.disableSettingsForm();
        } else {
            this.enableSettingsForm();
        }
    },
    methods: {
        updateLonelySettings() {
            if (!this.lonely) {
                this.form.post(route('lonely-dashboard', this.form));
                this.disableSettingsForm();
            } else if (this.lonely) {
                this.form.post(route('lonely-no-more'));
                this.enableSettingsForm();
            }
        },
        openChat(userId) {
            if (userId !== undefined) {
                this.$inertia.get(`/chat/${userId}`);
            }
        },
        enableSettingsForm() {
            const form = document.getElementById('lonelySettings');
            const elements = form.elements;
            for (var i = 0, len = elements.length; i < len; ++i) {
                elements[i].readOnly = false;
            }
        },
        disableSettingsForm() {
            const form = document.getElementById('lonelySettings');
            const elements = form.elements;
            for (var i = 0, len = elements.length; i < len; ++i) {
                elements[i].readOnly = true;
            }
        },
        showPersonDetails(event, user, position) {
            if (!user) {
                this.modalUser = {
                    name: 'You',
                };
            } else {
                this.modalUser = user;
            }
            this.infoWindowOpen = true;
            this.infoWindowPosition = position
        },
        calculateAge(birthdate) {
            return moment().diff(birthdate, 'years');
        },
        getPositionFromUser(user) {
            return new google.maps.LatLng({ lat: +user.user_lonely_setting.latitude, lng: +user.user_lonely_setting.longitude});
        }
    }
}
</script>

<style scoped>

</style>
