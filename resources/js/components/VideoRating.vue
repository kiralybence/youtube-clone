<template>
    <!-- Rating container -->
    <div class="video-rating">
        <!-- Like button -->
        <span
            class="video-like mr-1"
            v-bind:class="{ 'video-like-active': rating.status === 'like' }"
            v-on:click="rateVideo(rating.status !== 'like' ? 'like' : 'neutral')"
        >
            <i class="fas fa-thumbs-up"></i>
        </span>

        <!-- Like counter -->
        <span class="video-like-counter mr-2">
            {{ rating.likes }}
        </span>

        <!-- Dislike button -->
        <span
            class="video-dislike mr-1"
            v-bind:class="{ 'video-dislike-active': rating.status === 'dislike' }"
            v-on:click="rateVideo(rating.status !== 'dislike' ? 'dislike' : 'neutral')"
        >
            <i class="fas fa-thumbs-down"></i>
        </span>

        <!-- Dislike counter -->
        <span class="video-dislike-counter">
            {{ rating.dislikes }}
        </span>
    </div>
</template>

<script>
export default {
    props: [
        'videoKey',
    ],
    data() {
        return {
            rating: {
                status: 'neutral',
                likes: 0,
                dislikes: 0,
            },
        };
    },
    created() {
        this.loadrating();
    },
    methods: {
        async loadrating() {
            try {
                const response = await axios.get(`/api/video/${this.videoKey}`);

                this.rating.status = response.data.authUserRating;
                this.rating.likes = response.data.rating.likes;
                this.rating.dislikes = response.data.rating.dislikes;
            } catch (err) {
                console.error(err);
            }
        },
        async rateVideo(rateType = 'neutral') {
            try {
                const response = await axios.post(`/api/video/${this.videoKey}/rate`, {
                    rateType: rateType,
                });

                // Set the comment to neutral first
                switch (this.rating.status) {
                    case 'like':
                        this.rating.likes--;
                        break;

                    case 'dislike':
                        this.rating.dislikes--;
                        break;

                    default:
                        break;
                }
                this.rating.status = 'neutral';

                // And then apply the new rating
                switch (rateType) {
                    case 'like':
                        this.rating.status = 'like';
                        this.rating.likes++;
                        break;

                    case 'dislike':
                        this.rating.status = 'dislike';
                        this.rating.dislikes++;
                        break;

                    default:
                        break;
                }
            } catch (err) {
                console.error(err);
            }
        },
    },
};
</script>
