<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat with Michalis
            </h2>
        </template>

        <div class="flex flex-col">
            <div class="bg-white m-4 p-16 overflow-scroll" style="height: 75vh">
                <div v-for="chatMessage in chatMessages" class="messages-div mb-4 overflow-scroll">
                    <div class="flex flex-row">
                        <div style="min-width: 5em; max-width: 5em">
                            <img class="h-16 rounded block mb-4 block m-auto" v-if="chatMessage.sender_id === currentUser.id" :src="currentUser.profile_photo_url">
                            <img class="h-16 rounded block mb-4 block m-auto" v-else :src="receiver.profile_photo_url">
                        </div>
                        <div class="flex flex-col mb-4">
                            <div class="flex flex-row">
                                <p class="mr-4" v-if="chatMessage.sender_id === currentUser.id"><span
                                    class="font-bold text-lg">You</span> {{ formatDateFromMessage(chatMessage) }}: </p>
                                <p class="mr-4" v-else><span class="font-bold text-lg">{{ receiver.name }}</span>
                                    {{ formatDateFromMessage(chatMessage) }}: </p>
                            </div>
                            <p>{{ chatMessage.chat_message }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="sendChatMessage" class="flex flex-row m-4 px-4 mr-4">
                <input id="chatInput" type="text" class="border rounded w-full px-4"
                       placeholder=" Your message goes here..."
                       v-model="form.chatMessageInput"
                       autocomplete="off">
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
            this.chatMessages.push(e.message);
            this.scrollToLastMessage();
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
        },
        formatDateFromMessage(message) {
            let [date, time] = message.created_at.split('T');
            date = date.substr(0, 10);
            time = time.substr(0, 8);
            return time;
        }
    }
}
</script>

<style scoped>

</style>
