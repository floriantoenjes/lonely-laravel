<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Company Overview for {{ fromBackend }}
            </h2>
        </template>

        <div class="xl:flex flex-row">
            <div class="m-12 xl:mr-0 p-12 bg-white rounded">
                <h2 class="text-xl text-center">Our Company</h2>
                <p class="my-12">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor
                    invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo
                    duo
                    dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit
                    amet.
                    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut
                    labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores
                    et
                    ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem
                    ipsum
                    dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et
                    dolore
                    magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
                    Stet
                    clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
                    Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum
                    dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit
                    praesent
                    luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet,
                    consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna
                    aliquam
                    erat volutpat.
                    Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut
                    aliquip
                    ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse
                    molestie
                    consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio
                    dignissim
                    qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.
                    Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim
                    placerat
                    facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh
                    euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis
                    nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.
                    Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum
                    dolore eu feugiat nulla facilisis.
                    At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata
                    sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur</p>

                <div class="flex flex-row">
                    <div @click="cancel" class="mr-6">
                        <jet-button :type="'button'">Back to Dashboard</jet-button>
                    </div>

                    <div @click="sendPostRequest" class="mr-6">
                        <jet-button :type="'button'">Send Post Request</jet-button>
                    </div>

                    <div @click="alertMe" class="ml-auto"  v-if="backendData">
                        <jet-button :type="'button'">Alert me!</jet-button>
                    </div>
                </div>
            </div>

            <div class="m-12 p-12 bg-white rounded">
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor
                    invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo
                    duo
                    dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit
                    amet.
                    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut
                    labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores
                    et
                    ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem
                    ipsum
                    dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et
                    dolore
                    magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
                    Stet
                    clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
                    Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum
                    dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit
                    praesent
                    luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet,
                    consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna
                    aliquam
                    erat volutpat.
                </p>
            </div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from "../Layouts/AppLayout"
import JetButton from "./../Jetstream/Button"

export default {
    components: {
        AppLayout,
        JetButton
    },
    data() {
        return {
            backendData: ''
        }
    },
    methods: {
        alertMe() {
            console.log('Oh Junge!')
            alert('Yo Rin, what\'s poppin?')
        },
        cancel() {
            this.$inertia.get('/dashboard')
        },
        sendPostRequest() {
            axios.post('/company', { myName: 'Florian'}).then(
                response => {
                    console.log(response)
                    this.backendData = response.data
                }
            )
        }
    },
    props: [
        'fromBackend'
    ]
}
</script>

<style scoped>

</style>
