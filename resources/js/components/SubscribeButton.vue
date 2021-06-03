<template>
    <div class="subscribe-button-container">
        <button class="btn btn-danger" v-if="status === false" @click="subscribe">Subscribe</button>
        <button class="btn btn-secondary" v-if="status === true" @click="unsubscribe">Unsubscribe</button>
    </div>
</template>

<script>
export default {
    props: [
        'channel_id',
        'counterElement',
    ],
    data() {
        return {
            status: null,
        };
    },
    created() {
        this.getStatus();
    },
    methods: {
        async getStatus() {
            try {
                const response = await axios.get(`/api/users/${this.channel_id}/substatus`);

                this.status = response.data.status;
            } catch (err) {
                console.error(err);
            }
        },
        async subscribe() {
            try {
                const response = await axios.post(`/api/users/${this.channel_id}/subscribe`);

                this.status = true;

                if (this.counterElement !== undefined) {
                    let current = parseInt($(this.counterElement).text());
                    let updated = current + 1;
                    $(this.counterElement).html(updated);
                }
            } catch (err) {
                console.error(err);

                if (err.response.status === 401) {
                    window.location = '/login';
                }
            }
        },
        async unsubscribe() {
            try {
                const response = await axios.post(`/api/users/${this.channel_id}/unsubscribe`);

                this.status = false;

                if (this.counterElement !== undefined) {
                    let current = parseInt($(this.counterElement).text());
                    let updated = current - 1;
                    $(this.counterElement).html(updated);
                }
            } catch (err) {
                console.error(err);

                if (err.response.status === 401) {
                    window.location = '/login';
                }
            }
        },
    },
};
</script>
