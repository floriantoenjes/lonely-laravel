<template>
    <div class="h-full">
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
                            maxZoom: 18,
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
</template>

<script>
import {bus} from "../app";
import {gmapApi} from "gmap-vue";

export default {
    name: "LonelyMap",
    props: {
        'lonely': {},
        'userLonelySettings': {},
        'lonelyPersons': {},
        'activities': {},
    },
    data() {
        return {
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
        }
    },
    mounted() {
        this.zoomLevel = parseInt(Math.log2(156543.03392 * Math.cos(this.userLonelySettings.latitude * Math.PI / 180) / this.userLonelySettings.radius));

        this.generateMarkers();
        this.generateActivityMarkers();

        this.$gmapApiPromiseLazy().then(() => {
            if (this.lonely) {
                this.listenOnBoundsChanged();
            }
        });

        bus.$on('setLonely', this.setLonely);
    },
    computed: {
        google: gmapApi
    },
    methods: {
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
        showPersonDetails(event, user) {
            this.infoWindow.openedId = user.id;
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

        getPositionFromUser(user) {
            return new google.maps.LatLng({ lat: +user.user_lonely_setting.latitude, lng: +user.user_lonely_setting.longitude});
        },

        openChat(userId) {
            if (userId !== undefined) {
                this.$inertia.get(`/chat/${userId}`, { prevRoute: 'lonely-dashboard'});
            }
        },

        showPersonDetailsAndPan(event, user, position) {
            this.$refs.mainMap.panTo(position);
            this.showPersonDetails(event, user);
        },

        calculateAge(birthdate) {
            return moment().diff(birthdate, 'years');
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

        setLonely() {
            this.generateMarkers();
            this.generateActivityMarkers();
            setTimeout(() => {
                this.listenOnBoundsChanged();
            }, 1000);
        },

        refreshDashBoard() {
            this.generateMarkers();
            this.generateActivityMarkers();
            this.infoWindow.openedId = null;
        },
    }

}
</script>

<style scoped>

</style>
