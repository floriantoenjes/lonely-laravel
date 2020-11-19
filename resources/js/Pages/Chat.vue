<template>
    <app-layout>
        <template #header>
            <div class="flex flex-row items-center" v-if="receiver">
                <inertia-link class="text-xl mr-8 text-blue-500 hover:text-black" :href="route(prevRouteIfExists('chat'))">
                    <font-awesome-icon icon="arrow-left" size="lg"></font-awesome-icon>
                </inertia-link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    You are chatting with {{ receiver.name }}
                </h2>
            </div>
        </template>

        <div>
            <div class="flex flex-row">
                <div class="bg-white m-4 p-16 overflow-scroll w-2/3" style="height: 75vh">
                    <div v-for="chatMessage in chatMessages" class="messages-div mb-4 overflow-scroll">
                        <div class="flex flex-row">
                            <div style="min-width: 5em; max-width: 5em">
                                <img class="h-16 rounded block mb-4 block m-auto"
                                     v-if="chatMessage.sender_id === currentUser.id"
                                     :src="currentUser.profile_photo_url">
                                <img class="h-16 rounded block mb-4 block m-auto" v-else
                                     :src="receiver.profile_photo_url">
                            </div>
                            <div class="flex flex-col mb-4">
                                <div class="flex flex-row">
                                    <p class="mr-4" v-if="chatMessage.sender_id === currentUser.id"><span
                                        class="font-bold text-lg">You</span> {{ formatTime(chatMessage.created_at) }}:
                                    </p>
                                    <p class="mr-4" v-else><span class="font-bold text-lg">{{ receiver.name }}</span>
                                        {{ formatTime(chatMessage.created_at) }}: </p>
                                </div>
                                <p>{{ chatMessage.chat_message }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-full flex justify-center" v-if="!receiver">
                        <h2 class="text-2xl text-center self-center">Select a Contact!</h2>
                    </div>
                </div>

                <div class="bg-white m-4 p-16 w-1/3 flex flex-col">
                    <div class="flex flex-row items-center mb-4">
                        <img class="h-16 rounded block mb-4 mr-4 w-16"
                             :src="receiver.profile_photo_url">
                        <h3 class="text-2xl mb-4 mr-8">{{ receiver.name }}</h3>
                    </div>
                    <div class="mb-16">
                        <button type="button" class="bg-yellow-300 hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded mr-4">Ignore User</button>
                        <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Report User</button>
                    </div>

                    <h3 class="text-xl mb-4">Recent Contacts:</h3>
                    <ul class="text-lg">
                        <li v-for="contact in contacts" class="mb-4">
                            <inertia-link :href="route('chat', contact.id)" class="text-blue-500 hover:text-black">

                                <img :src="contact.profile_photo_url" class="inline mr-2" style="max-width: 4rem">

                                {{ contact.name }}
                            </inertia-link>
                        </li>
                    </ul>
                </div>
            </div>

            <form @submit.prevent="sendChatMessage" class="flex flex-row m-4 px-4 mr-4" v-if="receiver">
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
import PrevRouteCapability from "../Mixins/PrevRouteCapability";

export default {
    name: "Chat",
    components: {AppLayout, Label},
    mixins: [
        PrevRouteCapability
    ],
    props: {
        'userId': {
            type: String
        },
        'currentUser': {},
        'receiver': {},
        'chatMessages': {},
        'contacts': {}
    },
    data() {
        return {
            form: this.$inertia.form({
                chatMessageInput: ''
            }),
        }
    },
    mounted() {
        const firstUserId = Math.min(+this.currentUser.id, +this.receiver.id);
        const secondUserId = Math.max(+this.currentUser.id, +this.receiver.id);

        console.log('WS connection', `messages.${firstUserId}.${secondUserId}`);

        Echo.channel(`messages.${firstUserId}.${secondUserId}`).listen('MessageReceived', (e) => {
            this.chatMessages.push(e.message);
            this.scrollToLastMessage();
        });

        this.scrollToLastMessage();
    },
    methods: {
        sendChatMessage() {
            this.form.post(route('send-chat-message', {userId: this.receiver.id}, this.form), {
                onSuccess: () => {
                    this.scrollToLastMessage();
                }
            });
        },
        scrollToLastMessage() {
            const messagesDivs = this.$el.getElementsByClassName('messages-div');
            const el = messagesDivs[messagesDivs.length - 1];
            if (el !== undefined) {
                setTimeout(function () {
                    el.scrollIntoView();
                });
            }
        },
        formatTime(createdAt) {
            return moment(createdAt).format('LTS');
        }
    }
}
</script>

<style scoped>

</style>
