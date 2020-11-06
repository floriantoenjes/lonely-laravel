<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl">Current Activities</h2>
        </template>

        <div class="flex flex-row">
            <form class="m-4 p-16 flex flex-col w-1/4 bg-white" @submit.prevent="createActivity">
                <div class="mb-4">
                    <label for="name" class="block mr-4 mb-4">Activity Name:</label>
                    <input id="name" class="border rounded w-full" type="text" placeholder=" Name" v-model="form.name">
                </div>

                <div class="mb-4">
                    <label for="description" class="block mr-4 mb-4">Description:</label>
                    <textarea id="description" class="border rounded w-full" type="text" placeholder=" Description" v-model="form.description"></textarea>
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
                <h2 class="text-2xl mb-8">Available Activities</h2>
                <ul class="list-disc list-inside text-lg">
                    <p class="mt-8 text-lg" v-if="notJoinedActivities.length === 0">There are no other activities set for today!</p>
                    <li v-for="activity in notJoinedActivities" :key="activity.id" class="mb-4">
                        <inertia-link :href="route('activity-detail', activity.id)" class="text-blue-500 hover:text-black">{{ activity.name }}</inertia-link> at <span class="mr-4">{{ formatDateTime(activity.created_at) }} by {{ activity.creator.name }}</span>
                    </li>
                </ul>
            </div>

            <div class="m-4 p-16 w-1/3 bg-white">
                <h2 class="text-2xl mb-8">Joined Activities</h2>
                <ul class="list-disc list-inside text-lg">
                    <li v-for="activity in joinedActivities" :key="activity.id" class="mb-4">
                        <inertia-link :href="route('activity-detail', activity.id)" class="text-blue-500 hover:text-black">{{ activity.name }}</inertia-link> at <span class="mr-4">{{ formatDateTime(activity.created_at) }} by {{ activity.creator.name }}</span>
                    </li>
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
            const joinedActivityIds = this.joinedActivities.map(jA => jA.id);
            return this.activities.filter(a => joinedActivityIds.indexOf(a.id) === -1);
        }
    },
    methods: {
        createActivity() {
            this.form.post(route('create-activity'), this.form);
        },
        formatDateTime(dateTime) {
            return moment(dateTime).format('LT');
        }
    }
}
</script>

<style scoped>

</style>
