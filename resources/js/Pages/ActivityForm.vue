<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl">Create a New Activity</h2>
        </template>

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
    </app-layout>
</template>

<script>
import AppLayout from "../Layouts/AppLayout";
export default {
    name: "ActivityForm",
    components: {AppLayout},
    data() {
        return {
            form: this.$inertia.form({
                name: '',
                description: '',
                city: '',
                postcode: '',
                address: ''

            })
        }
    },
    methods: {
        createActivity() {
            this.form.post(route('create-activity'), this.form);
        }
    }
}
</script>

<style scoped>

</style>
