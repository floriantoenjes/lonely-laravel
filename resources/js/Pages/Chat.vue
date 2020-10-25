<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat with Michalis
            </h2>
        </template>

        <div class="flex flex-col">
            <div class="bg-white m-4 p-16 overflow-scroll" style="height: 75vh">
                <div v-for="chatMessage in chatMessages" class="messages-div flex flex-row mb-4 overflow-scroll">
                    <p class="mr-4" v-if="chatMessage.sender_id === currentUser.id">{{ currentUser.name }}: </p>
                    <p class="mr-4" v-else>{{ receiver.name }}: </p>
                    <p>{{ chatMessage.chat_message }}</p>
                </div>
            </div>

            <form @submit.prevent="sendChatMessage" class="flex flex-row m-4 px-4">
                <input id="chatInput" type="text" class="border rounded w-full px-4"
                       placeholder=" Your message goes here..."
                       v-model="form.chatMessageInput">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Send</button>
            </form>
        </div>
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
    mounted() {
        Echo.channel('messages').listen('MessageReceived', (e) => {
            console.log(e.message);
            this.chatMessages.push(e.message);
        });
        this.scrollToLastMessage();
    },
    methods: {
        sendChatMessage() {
            this.form.post(route('send-chat-message', { userId: this.userId }, this.form), {
                onSuccess: () => {
                    this.scrollToLastMessage();
                }
            });
        },
        scrollToLastMessage() {
            const messagesDivs = this.$el.getElementsByClassName('messages-div');
            const el = messagesDivs[messagesDivs.length - 1];
            el.scrollIntoView();
        }
    }
}
</script>

<style scoped>

</style>
