<template>
    <form @submit.prevent="sendChatMessage" class="flex flex-row">
        <input id="chatInput" type="text" class="border rounded w-full" placeholder="Your message goes here..." v-model="chatMessage">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8">Send</button>
    </form>
</template>

<script>
import Label from "../Jetstream/Label";

export default {
    name: "Chat",
    components: {Label},
    data() {
      return {
          chatMessage: ''
      }
    },
    methods: {
        sendChatMessage() {
            alert(this.chatMessage);
        }
    }
}
</script>

<style scoped>

</style>
