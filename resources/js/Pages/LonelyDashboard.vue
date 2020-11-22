<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your Lonely Dashboard
            </h2>
        </template>

        <div class="flex flex-row h-full">
            <div class="m-4 p-16 w-1/4 bg-white">
                <form class="flex flex-col" @submit.prevent="updateLonelySettings" id="lonelySettings">
                    <label for="placesAutocomplete">Places API</label>
                    <input id="placesAutocomplete" type="text" ref="placesAutocomplete" class="border rounded w-full"></input>

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
                        <label for="radius" class="block mr-4 mb-4">Radius in km:</label>
                        <input id="radius" class="border rounded w-full" type="text" placeholder=" Radius"
                               v-model="form.radius">
                    </div>

                    <div class="mb-4">
                        <label for="age-from" class="block mr-4 mb-4">Age from:</label>
                        <input id="age-from" class="border rounded w-full" type="text" placeholder=" Age from"
                               v-model="form.ageFrom">
                    </div>

                    <div class="mb-4">
                        <label for="age-to" class="block mr-4 mb-4">Age to:</label>
                        <input id="age-to" class="border rounded w-full" type="text" placeholder=" Age to"
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

                    <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8"
                            @click="refreshDashBoard" v-if="lonely">
                        Refresh
                        <font-awesome-icon icon="sync" class="ml-4" size="lg" :spin="refreshing"></font-awesome-icon>
                    </button>
                </form>
            </div>

            <div class="m-4 w-3/4 h-auto bg-white relative">

                <div v-if="!lonely" class="h-full flex justify-center items-center">
                    <p class="text-2xl" style="width: max-content;">Are you lonely  today? Mark yourself as lonely!</p>
                </div>

                <GmapMap
                    :center="mapCenter"
                    :zoom="zoomLevel"
                    map-type-id="roadmap"
                    style="width: 100%; height: 100%"
                    v-if="lonely"
                    :options="{
                        mapTypeControl: false,
                        streetViewControl: false,
                        styles: [{
                            featureType: 'poi',
                            stylers: [{
                                visibility: 'off'
                            }]
                        }],
                        maxZoom: 18
                    }"
                    ref="mainMap"
                >
                    <GmapCluster imagePath="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m"
                        :zoomOnClick="true" :maxZoom="17" @click="resolveCluster">

                        <GmapMarker
                            :key="index"
                            v-for="(m, index) in markers"
                            :position="m.position"
                            :clickable="true"
                            :draggable="false"
                            @mouseover="showPersonDetails($event, m.user)"
                        />

                        <GmapMarker
                            :key="'a' + index"
                            v-for="(m, index) in activityMarkers"
                            :position="m.position"
                            :clickable="true"
                            :draggable="false"
                            :icon="{ url: 'http://maps.gstatic.com/mapfiles/markers2/icon_green.png' }"
                            @mouseover="showActivityDetails($event, m.activity, m.position)"
                        />

                    </GmapCluster>


                    <GmapInfoWindow
                        :key="'i' + index"
                        v-for="(m, index) in markers"
                        :opened="infoWindow.openedId === m.user.id"
                        :position="m.position"
                        :options="infoWindow.options"
                        v-if="m.user">
                        <div v-if="m.user.id !== $page.user.id" style="max-width: 192px">
                            <p class="text-lg mb-2">{{ m.user.name }}<span v-if="m.user.birthdate">, {{ calculateAge(m.user.birthdate) }}</span></p>
                            <p class="text-lg text-blue-500 hover:text-black cursor-pointer text-center" @click="openChat(m.user.id)" v-if="m.user.id">
                                <font-awesome-icon :icon="['far', 'comments']" size="lg"></font-awesome-icon>
                                Chat
                            </p>
                        </div>
                        <div v-else>
                            <p class="text-lg mb-2">You</p>
                        </div>
                    </GmapInfoWindow>

                    <GmapInfoWindow
                        :key="'ai' + index"
                        v-for="(m, index) in activityMarkers"
                        :opened="infoWindow.openedId === 'a' + m.activity.id"
                        :position="m.position"
                        :options="infoWindow.options"
                        v-if="m.activity">
                        <div style="max-width: 192px">
                            <p class="text-lg mb-2">{{ m.activity.name }}</p>
                            <p class="text-lg text-blue-500 hover:text-black cursor-pointer text-center" @click="openActivity(m.activity.id)">
                                <font-awesome-icon icon="info-circle" size="lg"></font-awesome-icon>
                                Details
                            </p>
                        </div>
                    </GmapInfoWindow>

                    <GmapCircle
                        :center="{
                            lat: +userLonelySettings.latitude,
                            lng: +userLonelySettings.longitude
                        }"
                        :radius="userLonelySettings.radius * 1000"
                        :options="{
                            fillOpacity: 0.0,
                            strokeOpacity: 0.2,
                            strokeColor: '#0000FF'
                        }">
                    </GmapCircle>
                </GmapMap>

                <div v-if="lonely" class="bg-white p-4 rounded" style="width: max-content; position: absolute; left: 0.5rem; top: 2rem">

                    <h2 class="text-2xl mb-2" v-if="lonelyPersons.length > 0">Lonely People:</h2>
                    <h2 class="text-2xl mb-8" v-else>No one seems to be lonely right now, sorry.</h2>

                    <ul class="list-disc list-inside">
                        <li v-for="lonelyPerson in lonelyPersons" class="mb-2" @click="showPersonDetailsAndPan($event, lonelyPerson, getPositionFromUser(lonelyPerson))">
                            <span class="text-blue-500 hover:text-black cursor-pointer">{{ lonelyPerson.name }}</span>
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
        'lonelyPersons': {},
        'activities': {}
    },
    data() {
        return {
            loading: false,

            form: this.$inertia.form({
                city: this.userLonelySettings?.city,
                postcode: this.userLonelySettings?.postcode,
                address: this.userLonelySettings?.address,
                radius: this.userLonelySettings?.radius,
                ageFrom: this.userLonelySettings?.meet_up_age_from,
                ageTo: this.userLonelySettings?.meet_up_age_to,

                street_number: '',
                route: '',
                locality: '',
                postal_code: '',

            }, {
                resetOnSuccess: false
            }),

            mapCenter: { lat: +this.userLonelySettings.latitude, lng: +this.userLonelySettings.longitude },
            zoomLevel: 0,
            markers: [],
            activityMarkers: [],

            markersMoved: false,
            movedMarkers: [],

            infoWindow: {
                openedId: -1,
                options: {
                    pixelOffset: {
                        width: 0,
                        height: -40
                    }
                }
            },

            refreshing: false,

            autocomplete: null,
        }
    },
    computed: {
        google: gmapApi,
    },
    mounted() {
        this.zoomLevel = parseInt(Math.log2(156543.03392 * Math.cos(this.userLonelySettings.latitude * Math.PI / 180) / this.userLonelySettings.radius));

        Inertia.on('start', event => {
            this.loading = true;
        });
        Inertia.on('finish', event => {
            this.loading = false;
        });

        if (this.lonely) {
            this.disableSettingsForm();
        } else {
            this.enableSettingsForm();
        }

        this.generateMarkers();
        this.generateActivityMarkers();

        this.$gmapApiPromiseLazy().then(() => {
            if (this.lonely) {
                this.listenOnBoundsChanged();
            }

            console.log(this.$refs.placesAutocomplete);

            this.autocomplete = new google.maps.places.Autocomplete(this.$refs.placesAutocomplete);
            this.autocomplete.setFields(["address_component"]);
            this.autocomplete.addListener('place_changed', () => {
                const place = this.autocomplete.getPlace();
                console.log(place);

                for (const component of place.address_components) {
                    const addressType = component.types[0];

                    console.log(this.form, addressType);

                    if (this.form[addressType] !== undefined) {
                        this.form[addressType] = component.long_name;
                    }
                }
            })
        });
    },
    methods: {
        updateLonelySettings() {
            if (!this.lonely) {
                this.form.post(route('lonely-dashboard', this.form)).then(() => {
                    this.generateMarkers();
                    this.generateActivityMarkers();
                });
                this.disableSettingsForm();
                setTimeout(() => {
                    this.listenOnBoundsChanged();
                }, 1000);
            } else if (this.lonely) {
                this.form.post(route('lonely-no-more'));
                this.enableSettingsForm();
            }
        },
        enableSettingsForm() {
            const form = document.getElementById('lonelySettings');
            const elements = form.elements;
            for (const element of elements) {
                element.readOnly = false;
            }
        },
        disableSettingsForm() {
            const form = document.getElementById('lonelySettings');
            const elements = form.elements;
            for (const element of elements) {
                element.readOnly = true;
            }
        },
        showPersonDetails(event, user) {
            this.infoWindow.openedId = user.id;
        },
        showPersonDetailsAndPan(event, user, position) {
            this.$refs.mainMap.panTo(position);
            this.showPersonDetails(event, user);
        },
        openChat(userId) {
            if (userId !== undefined) {
                this.$inertia.get(`/chat/${userId}`, { prevRoute: 'lonely-dashboard'});
            }
        },
        showActivityDetails($event, activity, position) {

            let newLat = position.lat();
            let newLng = position.lng();

            if (!this.markersMoved) {
                for (let i = 0; i < this.activityMarkers.length; i++) {
                    if (
                        this.activityMarkers[i].activity.id !== activity.id &&
                        this.activityMarkers[i].position.lat() === position.lat() &&
                        this.activityMarkers[i].position.lng() === position.lng()) {

                        var theta = 2.39998131 * i;
                        var radius = 2.5 * Math.sqrt(theta);
                        var x = Math.cos(theta) * radius;
                        var y = Math.sin(theta) * radius;

                        console.log(x, y);

                        newLat += 0.00005 * x;
                        newLng += 0.00005 * y;
                        this.activityMarkers[i].position = new google.maps.LatLng({ lat: newLat, lng: newLng})

                        const line = new google.maps.Polyline({
                            path: [
                                new google.maps.LatLng(position.lat(), position.lng()),
                                new google.maps.LatLng(newLat, newLng)
                            ],
                            strokeColor: "#000000",
                            strokeOpacity: 1.0,
                            strokeWeight: 2,
                            map: this.$refs.mainMap.$mapObject
                        });

                        this.activityMarkers[i].oldPosition = position;
                        this.activityMarkers[i].line = line;
                        this.movedMarkers.push(this.activityMarkers[i]);

                        this.markersMoved = true;
                    }
                }
            }

            this.infoWindow.openedId = 'a' + activity.id;

        },
        openActivity(id) {
            this.$inertia.get(`activity/${id}`, { prevRoute: 'lonely-dashboard'});
        },
        calculateAge(birthdate) {
            return moment().diff(birthdate, 'years');
        },
        getPositionFromUser(user) {
            return new google.maps.LatLng({ lat: +user.user_lonely_setting.latitude, lng: +user.user_lonely_setting.longitude});
        },
        generateMarkers() {
            this.markers = [];
            this.$gmapApiPromiseLazy().then(() => {
                for (const lonelyPerson of this.lonelyPersons) {
                    this.markers.push({
                        position: new google.maps.LatLng({ lat: +lonelyPerson.user_lonely_setting.latitude, lng: +lonelyPerson.user_lonely_setting.longitude}),
                        user: lonelyPerson
                    });
                }
                this.markers.push({
                    position: new google.maps.LatLng({ lat: +this.userLonelySettings.latitude, lng: +this.userLonelySettings.longitude}),
                    user: this.$page.user
                })
            });
        },
        generateActivityMarkers() {
            this.activityMarkers = [];
            this.$gmapApiPromiseLazy().then(() => {
                for (const activity of this.activities) {
                    this.activityMarkers.push({
                        position: new google.maps.LatLng({ lat: +activity.latitude, lng: +activity.longitude}),
                        activity: activity
                    });
                }
            });
        },
        refreshDashBoard() {
            this.refreshing = true;
            axios.get('/lonely-dashboard/refresh').then(response => {
                this.lonelyPersons = response.data.lonelyPersons;
                this.activities = response.data.activities;

                this.generateMarkers();
                this.generateActivityMarkers();

                this.infoWindow.openedId = null;

                setTimeout(() => {
                    this.refreshing = false;
                }, 3000);
            });
        },
        resolveCluster(event) {
            // this.$refs.mainMap.panTo(event.getMarkers()[0].position);
            // this.$refs.mainMap.fitBounds(event.getBounds());

            this.$gmapApiPromiseLazy().then(() => {
                const map = this.$refs.mainMap.$mapObject;
                if (map.getZoom() > 21) {
                    map.setZoom(map.getZoom() - 4);
                }
            });
        },
        listenOnBoundsChanged() {
            this.$refs.mainMap.$mapObject.addListener("bounds_changed", () => {
                this.infoWindow.openedId = null;
                if (this.movedMarkers.length > 0) {
                    for (const movedMarker of this.movedMarkers) {
                        movedMarker.line.setMap(null);
                        movedMarker.position = movedMarker.oldPosition;
                        this.markersMoved = false;
                    }
                }
            });
        }
    }
}
</script>

<style scoped>
</style>
