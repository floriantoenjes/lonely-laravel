<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your Lonely Dashboard
            </h2>
        </template>

        <div class="flex flex-row">
            <div class="m-4 p-16 w-1/4 h-full bg-white">
                <form class="flex flex-col" @submit.prevent="updateLonelySettings">
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

                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8"
                            :disabled="!success" :class="{ 'opacity-50 cursor-not-allowed': !success }">Show me lonely
                        people!
                    </button>
                </form>
            </div>

            <div class="m-4 p-16 w-3/4 h-auto bg-white">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci delectus est ex iure molestiae obcaecati optio quibusdam repellendus velit. Aliquam cumque esse eveniet, ipsa odit repudiandae ut! Architecto, voluptatum!</p>
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
export default {
    name: "LonelyDashboard",
    components: {
        Input,
        AppLayout,
        JetButton,
        JetFormSection,
        JetInput
    },
    props: {
        'userLonelySettings': {

        },
        'success': {
            default: true
        }
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
            })
        }
    },
    methods: {
        updateLonelySettings() {
            this.success = false;
            this.form.post(route('lonely-dashboard', this.form));
        }
    }
}
</script>

<style scoped>

</style>
