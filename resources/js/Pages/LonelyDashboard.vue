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

            <div class="m-4 p-16 w-3/4 h-auto bg-white">
                <p v-if="!lonely">Are you lonely  today? Mark yourself as lonely!</p>
                <div v-else>
                    <h2 class="text-2xl mb-8">Lonely People:</h2>
                    <ul class="list-disc list-inside">
                        <li v-for="lonelyPerson in lonelyPersons">{{ lonelyPerson.name }}</li>
                    </ul>
                </div>
            </div>

            <chat></chat>
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
import Chat from './Chat';

export default {
    name: "LonelyDashboard",
    components: {
        Input,
        AppLayout,
        Chat,
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
            loading: false
        }
    },
    mounted() {
        Echo.channel('messages').listen('MessageReceived', (e) => {
            alert(e.messages);
        });

        Inertia.on('start', event => {
            this.loading = true;
        });

        Inertia.on('finish', event => {
            this.loading = false;
        });
    },
    methods: {
        updateLonelySettings() {
            if (!this.lonely) {
                this.form.post(route('lonely-dashboard', this.form));
            } else if (this.lonely) {
                this.form.post(route('lonely-no-more'));
            }
        }
    }
}
</script>

<style scoped>

</style>
