<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your Lonely Dashboard
            </h2>
        </template>

        <jet-form-section @submitted="updateLonelySettings">
            <template #title>
                Your Lonely Settings
            </template>

            <template #description>
                Set up your settings.
            </template>

            <template #form>
                <jet-input id="name" type="text" v-model="form.name">

                </jet-input>
            </template>

            <template #actions>
                <jet-button>Save</jet-button>
            </template>
        </jet-form-section>

    </app-layout>
</template>

<script>
import AppLayout from "../Layouts/AppLayout";
import JetButton from '../Jetstream/Button';
import JetFormSection from '../Jetstream/FormSection';
import JetInput from '../Jetstream/Input';
export default {
    name: "LonelyDashboard",
    components: {
        AppLayout,
        JetButton,
        JetFormSection,
        JetInput
    },
    data() {
        return {
            form: this.$inertia.form({
                name: 'Florian'
            })
        }
    },
    methods: {
        updateLonelySettings() {
            this.form.post(route('lonely-dashboard', this.form));
        }
    }
}
</script>

<style scoped>

</style>
