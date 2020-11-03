<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Activity: {{ activity.name }}
            </h2>
        </template>

        <div class="flex flex-col">
            <div class="bg-white m-4 p-16 overflow-scroll" style="height: 75vh">

                <h4 class="text-xl text-center" v-if="activity.activity_messages.length === 0">Be the first to write a message!</h4>

                <div v-for="activityMessage in activity.activity_messages" class="messages-div mb-4 overflow-scroll">
                    <div class="flex flex-row">
                        <div style="min-width: 5em; max-width: 5em">
                            <img class="h-16 rounded block mb-4 block m-auto" v-if="activityMessage.sender_id === currentUser.id" :src="currentUser.profile_photo_url">
                            <img class="h-16 rounded block mb-4 block m-auto" v-else :src="activityMessage.sender.profile_photo_url">
                        </div>
                        <div class="flex flex-col mb-4">
                            <div class="flex flex-row">
                                <p class="mr-4" v-if="activityMessage.sender_id === currentUser.id"><span
                                    class="font-bold text-lg">You</span> {{ formatTime(activityMessage.created_at) }}: </p>
                                <p class="mr-4" v-else><span class="font-bold text-lg">{{ activityMessage.sender.name }}</span>
                                    {{ formatTime(activityMessage.created_at) }}: </p>
                            </div>
                            <p>{{ activityMessage.activity_message }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="sendChatMessage" class="flex flex-row m-4 px-4 mr-4">
                <input id="chatInput" type="text" class="border rounded w-full px-4"
                       placeholder=" Your message goes here..."
                       v-model="form.activityMessageInput"
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
    name: "ActivityChat",
    components: {AppLayout, Label},
    props: {
        'activity': {},
        'currentUser': {},
    },
    data() {
        return {
            form: this.$inertia.form({
                activityMessageInput: ''
            }),
        }
    },
    mounted() {
        Echo.channel('activity-messages').listen('ActivityMessageReceived', (e) => {
            // this.chatMessages.push(e.message);
            console.log('activityMessage');
            this.scrollToLastMessage();
        });
        this.scrollToLastMessage();
    },
    methods: {
        sendChatMessage() {
            this.form.post(route('send-activity-message', { activityId: this.activity.id }, this.form), {
                onSuccess: () => {
                    this.scrollToLastMessage();
                }
            });
        },
        scrollToLastMessage() {
            const messagesDivs = this.$el.getElementsByClassName('messages-div');
            const el = messagesDivs[messagesDivs.length - 1];
            if (el !== undefined) {
                el.scrollIntoView();
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
