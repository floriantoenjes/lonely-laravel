<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl">Create a New Activity</h2>
        </template>

        <div class="flex flex-row">
            <form class="m-4 p-16 flex flex-col w-1/4 bg-white" @submit.prevent="createActivity">
                <div class="mb-4">
                    <label for="name" class="block mr-4 mb-4">Activity Name:</label>
                    <input id="name" class="border rounded w-full" type="text" placeholder=" Name" v-model="form.name">
                </div>

                <div class="mb-4">
                    <label for="description" class="block mr-4 mb-4">Description:</label>
                    <input id="description" class="border rounded w-full" type="text" placeholder=" Description" v-model="form.description">
                </div>

                <div class="mb-4">
                    <label for="city" class="block mr-4 mb-4">City:</label>
                    <input id="city" class="border rounded w-full" type="text" placeholder=" City" v-model="form.city">
                </div>

                <div class="mb-4">
                    <label for="postcode" class="block mr-4 mb-4">Postcode:</label>
                    <input id="postcode" class="border rounded w-full" type="text" placeholder=" Postcode" v-model="form.postcode">
                </div>

                <div class="mb-4">
                    <label for="address" class="block mr-4 mb-4">Address:</label>
                    <input id="address" class="border rounded w-full" type="text" placeholder=" Address" v-model="form.address">
                </div>

                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8">
                    Create Activity
                </button>
            </form>

            <div class="m-4 p-16 w-1/3 bg-white">
                <ul class="list-disc list-inside">
                    <li v-for="activity in notJoinedActivities" :key="activity.id">{{ activity.name }} on <span class="mr-4">{{ formatDateTime(activity.created_at) }}</span>
                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded mt-8" @click="joinActivity(activity)">Join</button></li>
                </ul>
            </div>

            <div class="m-4 p-16 w-1/3 bg-white">
                <ul class="list-disc list-inside">
                    <li v-for="activity in joinedActivities" :key="activity.id">{{ activity.name }} on <span class="mr-4">{{ formatDateTime(activity.created_at) }}</span>
                        <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded mt-8" @click="leaveActivity(activity)">Leave</button></li>
                </ul>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "../Layouts/AppLayout";
import Button from "../Jetstream/Button";
export default {
    name: "ActivityForm",
    components: {Button, AppLayout},
    props: {
        activities: {},
        joinedActivities: {}
    },
    data() {
        return {
            form: this.$inertia.form({
                name: '',
                description: '',
                city: '',
                postcode: '',
                address: ''

            }),
        }
    },
    computed: {
        notJoinedActivities: function () {
            // console.log(this.activities, this.joinedActivities);
            return this.activities.filter(a => this.joinedActivities.indexOf(a.id) === -1);
        }
    },
    methods: {
        createActivity() {
            this.form.post(route('create-activity'), this.form);
        },
        joinActivity(activity) {
            axios.post(`/activity/${activity.id}/join`).then(
                response => {
                    if (response.data.joined) {
                        this.activities.splice(this.activities.indexOf(activity), 1);
                        this.joinedActivities.push(activity);
                    }
                });
        },
        leaveActivity(activity) {
            axios.post(`/activity/${activity.id}/leave`).then(
                response => {
                    if (response.data.left) {
                        this.joinedActivities.splice(this.joinedActivities.indexOf(activity), 1);
                        this.activities.push(activity);
                    }
                });
        },
        formatDateTime(dateTime) {
            return moment(dateTime).format('LLL');
        }
    }
}
</script>

<style scoped>

</style>
