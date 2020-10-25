<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat with Michalis
            </h2>
        </template>

        <div class="bg-white m-4 p-16">
            <div v-for="chatMessage in chatMessages" class="flex flex-row mb-4 overflow-scroll">
                <p class="mr-4">Florian: </p>
                <p>{{ chatMessage }}</p>
            </div>
        </div>

        <form @submit.prevent="sendChatMessage" class="flex flex-row m-4 px-4">
            <input id="chatInput" type="text" class="border rounded w-full px-4" placeholder=" Your message goes here..."
                   v-model="chatMessageInput">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Send</button>
        </form>
    </app-layout>
</template>

<script>
import Label from "../Jetstream/Label";
import AppLayout from "../Layouts/AppLayout";

export default {
    name: "Chat",
    components: {AppLayout, Label},
    props: {
        'userId': {
            type: String
        }
    },
    data() {
        return {
            chatMessageInput: '',
            chatMessages: [
                'I\'m baby hexagon flexitarian enamel pin chartreuse semiotics pork belly mustache kickstarter meh cred direct trade adaptogen trust fund tattooed. Ugh yuccie roof party, bushwick microdosing wolf lyft green juice master cleanse farm-to-table asymmetrical salvia lumbersexual franzen direct trade. Swag DIY master cleanse cray yr intelligentsia edison bulb. ',
                'Austin kale chips jean shorts tilde tote bag direct trade chambray jianbing sustainable 3 wolf moon gastropub mixtape wolf ',
                'Prism small batch microdosing wolf fanny pack. Squid subway tile jean shorts try-hard humblebrag fixie meggings pinterest hoodie leggings, paleo godard. Ethical cray migas chicharrones, celiac freegan lyft pinterest chia. Lyft shabby chic lumbersexual, schlitz biodiesel leggings roof party meggings subway tile selvage ramps butcher chicharrones.'
            ]
        }
    },
    methods: {
        sendChatMessage() {
            this.chatMessages.push(this.chatMessageInput);
        }
    }
}
</script>

<style scoped>

</style>
