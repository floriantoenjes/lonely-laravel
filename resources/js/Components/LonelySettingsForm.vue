<template>
    <form class="flex flex-col" @submit.prevent="updateLonelySettings" id="lonelySettings">
        <div class="mb-4">
            <label for="placesAutocomplete" class="block mr-4 mb-4">Your Location:</label>
            <input id="placesAutocomplete" type="text" ref="placesAutocomplete" class="border rounded w-full"/>
        </div>

        <div class="mb-4">
            <label for="city" class="block mr-4 mb-4">City:</label>
            <input id="city" class="border rounded w-full" type="text" placeholder=" City" v-model="form.city">
        </div>

        <div class="mb-4">
            <label for="postcode" class="block mr-4 mb-4">Postcode:</label>
            <input id="postcode" class="border rounded w-full" type="text" placeholder=" Postcode"
                   v-model="form.postcode">
        </div>

        <div class="mb-4">
            <label for="address" class="block mr-4 mb-4">Address:</label>
            <input id="address" class="border rounded w-full" type="text" placeholder=" Address"
                   v-model="form.address">
        </div>

        <div class="mb-4">
            <label for="radius" class="block mr-4 mb-4">Radius in km:</label>
            <input id="radius" class="border rounded w-full" type="text" placeholder=" Radius"
                   v-model="form.radius">
        </div>

        <div class="mb-4">
            <label for="age-from" class="block mr-4 mb-4">Age from:</label>
            <input id="age-from" class="border rounded w-full" type="text" placeholder=" Age from"
                   v-model="form.ageFrom">
        </div>

        <div class="mb-4">
            <label for="age-to" class="block mr-4 mb-4">Age to:</label>
            <input id="age-to" class="border rounded w-full" type="text" placeholder=" Age to"
                   v-model="form.ageTo">
        </div>

        <button v-if="!lonely" type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8"
                :disabled="loading" :class="{ 'opacity-50 cursor-not-allowed': loading }">I am lonely today!
        </button>

        <button v-else-if="lonely" type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-8"
                :disabled="loading" :class="{ 'opacity-50 cursor-not-allowed': loading }">I am not lonely anymore!
        </button>

    </form>
</template>

<script>
export default {
    name: "LonelySettingsForm",
    props: {
        'loading': false,
        'lonely': {},
        'userLonelySettings': {},
    },
    data() {
        return {
            form: this.$inertia.form({
                city: this.userLonelySettings?.city,
                postcode: this.userLonelySettings?.postcode,
                address: this.userLonelySettings?.address,

                radius: this.userLonelySettings?.radius,
                ageFrom: this.userLonelySettings?.meet_up_age_from,
                ageTo: this.userLonelySettings?.meet_up_age_to,

                street_number: '',
                route: '',
                locality: '',
                postal_code: '',

            }, {
                resetOnSuccess: false
            }),
        }
    },
    mounted() {
        if (this.lonely) {
            this.disableSettingsForm();
        } else {
            this.enableSettingsForm();
        }

        this.$gmapApiPromiseLazy().then(() => {
            console.log(this.$refs.placesAutocomplete);

            this.autocomplete = new google.maps.places.Autocomplete(this.$refs.placesAutocomplete);
            this.autocomplete.setFields(["address_component"]);
            this.autocomplete.addListener('place_changed', () => {
                const place = this.autocomplete.getPlace();
                console.log(place);

                let street_number = '';
                let route = '';
                for (const component of place.address_components) {
                    const addressType = component.types[0];

                    console.log(this.form, addressType);

                    switch (addressType) {
                        case 'street_number':
                            street_number = component.long_name;
                            break;
                        case 'route':
                            route = component.long_name;
                            break;
                        case 'locality':
                            this.form.city = component.long_name;
                            break;
                        case 'postal_code':
                            this.form.postcode = component.long_name;
                            break;
                    }
                }
                this.form.address = `${route} ${street_number}`;
            })
        });
    },
    methods: {
        updateLonelySettings() {
            if (!this.lonely) {
                this.form.post(route('lonely-dashboard', this.form)).then(() => {
                    this.$emit('set-lonely');
                });
                this.disableSettingsForm();
            } else if (this.lonely) {
                this.form.post(route('lonely-no-more'));
                this.enableSettingsForm();
            }
        },
        enableSettingsForm() {
            const form = document.getElementById('lonelySettings');
            const elements = form.elements;
            for (const element of elements) {
                if (element.tagName.toLowerCase() !== 'button') {
                    element.readOnly = false;
                    element.disabled = '';
                }
            }
        },
        disableSettingsForm() {
            const form = document.getElementById('lonelySettings');
            const elements = form.elements;
            for (const element of elements) {
                if (element.tagName.toLowerCase() !== 'button') {
                    element.readOnly = true;
                    element.disabled = 'disabled';
                }
            }
        },
    }

}
</script>

<style scoped>

</style>
