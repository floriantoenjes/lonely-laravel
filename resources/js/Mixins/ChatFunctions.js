export default {
    methods: {
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
    }
}
