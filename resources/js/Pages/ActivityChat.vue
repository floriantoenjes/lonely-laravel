<template>
    <app-layout>
        <template #header>
            <div class="flex flex-row items-center">
                <inertia-link class="text-xl mr-8 text-blue-500 hover:text-black" :href="route('new-activity-form')">
                    <font-awesome-icon icon="arrow-left" size="lg"></font-awesome-icon>
                </inertia-link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Activity: {{ activity.name }}
                </h2>
            </div>
        </template>

        <div class="flex flex-col">
            <div class="flex flex-row">
                <div class="bg-white m-4 p-16 overflow-scroll w-2/4" style="height: 75vh">
                    <div v-if="hasUserJoined($page.user.id) && activity.activity_messages.length > 0">
                        <div v-for="activityMessage in activity.activity_messages"
                             class="messages-div mb-4 overflow-scroll">
                            <div class="flex flex-row">
                                <div style="min-width: 5em; max-width: 5em">
                                    <img class="h-16 rounded mb-4 block m-auto"
                                         v-if="activityMessage.sender_id === currentUser.id"
                                         :src="currentUser.profile_photo_url">
                                    <img class="h-16 rounded mb-4 block m-auto" v-else
                                         :src="activityMessage.sender.profile_photo_url">
                                </div>
                                <div class="flex flex-col mb-4">
                                    <div class="flex flex-row">
                                        <p class="mr-4" v-if="activityMessage.sender_id === currentUser.id"><span
                                            class="font-bold text-lg">You</span> {{
                                                formatTime(activityMessage.created_at)
                                            }}: </p>
                                        <p class="mr-4" v-else><span
                                            class="font-bold text-lg">{{ activityMessage.sender.name }}</span>
                                            {{ formatTime(activityMessage.created_at) }}: </p>
                                    </div>
                                    <p>{{ activityMessage.activity_message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="hasUserJoined($page.user.id) && activity.activity_messages.length === 0"
                         class="flex justify-center items-center h-full">
                        <h4 class="text-2xl">Be the first to write a message!</h4>
                    </div>
                    <div v-else class="flex justify-center items-center h-full">
                        <h4 class="text-2xl">Join this activity to organize a meetup!</h4>
                    </div>
                </div>

                <div class="bg-white m-4 p-16 overflow-scroll w-2/4 flex flex-col" style="height: 75vh">
                    <!-- TODO: Replace with begin-time -->
                    <h2 class="text-2xl mb-4 mr-4">{{ activity.name }} <span class="text-base"> at </span><span
                        class="text-blue-500 text-base">14:15 PM</span></h2>
                    <div>
                        <img class="h-16 rounded mb-4 mr-4" :src="activity.creator.profile_photo_url">
                    </div>
                    <h3 class="text-lg mb-8">created by {{ activity.creator.name }}</h3>
                    <h3 class="text-xl mb-4">Description:</h3>
                    <p class="mb-8">{{ activity.description }}</p>

                    <div v-if="joinedUsers.length > 0">
                        <h2 class="text-xl mb-4">Joined users</h2>
                        <ul class="list-disc list-inside">
                            <li v-for="joinedUser in joinedUsers">{{ joinedUser.name }}</li>
                        </ul>
                    </div>
                    <h2 v-else class="text-xl mb-4"><font-awesome-icon icon="frown-open" size="lg"></font-awesome-icon> No one has joined so far</h2>

                    <div class="mt-auto">
                        <button v-if="!hasUserJoined($page.user.id)" type="button"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                @click="joinActivity()">Join Activity
                        </button>
                        <button v-else type="button"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                @click="leaveActivity()">Leave Activity
                        </button>
                    </div>

                </div>
            </div>

            <form @submit.prevent="sendChatMessage" class="flex flex-row m-4 px-4 mr-4"
                  v-if="hasUserJoined($page.user.id)">
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
import Button from "../Jetstream/Button";

export default {
    name: "ActivityChat",
    components: {Button, AppLayout, Label},
    props: {
        'activity': {},
        'currentUser': {},
        'joinedUsers': {}
    },
    data() {
        return {
            form: this.$inertia.form({
                activityMessageInput: ''
            }),
        }
    },
    mounted() {
        Echo.channel(`activity-messages.${this.activity.id}`).listen('ActivityMessageReceived', (e) => {
            // this.chatMessages.push(e.message);
            console.log('activityMessage', e);
            console.log('activityMessage', e.activityMessage.activity_message);
            this.activity.activity_messages.push(e.activityMessage);
            this.scrollToLastMessage();
        });
        this.scrollToLastMessage();
    },
    methods: {
        sendChatMessage() {
            this.form.post(route('send-activity-message', {activityId: this.activity.id}, this.form), {
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
        },
        hasUserJoined(userId) {
            return this.joinedUsers.find(joinedUser => joinedUser.id === userId) !== undefined;
        },
        joinActivity() {
            axios.post(`/activity/${this.activity.id}/join`).then(
                response => {
                    if (response.data.joined) {
                        this.joinedUsers.push(this.currentUser);
                        setTimeout(this.scrollToLastMessage);
                    }
                });
        },
        leaveActivity() {
            axios.post(`/activity/${this.activity.id}/leave`).then(
                response => {
                    if (response.data.left) {
                        this.joinedUsers = this.joinedUsers.filter(joinedUser => joinedUser.id !== this.currentUser.id);
                    }
                });
        },
    }
}
</script>

<style scoped>

</style>
