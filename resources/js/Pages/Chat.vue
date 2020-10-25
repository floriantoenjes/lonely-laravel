<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat with Michalis
            </h2>
        </template>

        <div class="bg-white m-4 p-16">
            <div v-for="chatMessage in chatMessages" class="flex flex-row mb-4 overflow-scroll">
                <p class="mr-4">{{ chatMessage.sender_id }}: </p>
                <p>{{ chatMessage.chat_message }}</p>
            </div>
        </div>

        <form @submit.prevent="sendChatMessage" class="flex flex-row m-4 px-4">
            <input id="chatInput" type="text" class="border rounded w-full px-4" placeholder=" Your message goes here..."
                   v-model="form.chatMessageInput">
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
        },
        'currentUser': {},
        'receiver': {},
        'chatMessages': {}
    },
    data() {
        return {
            form: this.$inertia.form({
                chatMessageInput: ''
            }),
        }
    },
    methods: {
        sendChatMessage() {
            this.form.post(route('send-chat-message', { userId: this.userId }, this.form));
        }
    }
}
</script>

<style scoped>

</style>
