<template>
    <!-- Comment section container -->
    <div>
        <!-- Comment counter -->
        <h2 class="h5 mb-3">{{ countComments() }} comments</h2>

        <!-- New comment -->
        <div class="mb-4">
            <!-- Input field -->
            <textarea
                class="form-control mb-2 shadow-sm"
                v-model="newCommentDraft"
            ></textarea>

            <!-- Send button -->
            <button
                class="btn btn-primary"
                v-on:click="sendComment()"
            >
                Send
            </button>
        </div>

        <!-- Comment section placeholder -->
        <p v-show="comments.length === 0">
            Cold is the void...
        </p>

        <!-- Comments -->
        <div
            v-for="comment in comments"
            class="comment-box"
        >
            <!-- Comment username -->
            <b>{{ comment.user.name }}</b>

            <!-- Comment date -->
            <span class="comment-date">
                {{ comment.date.toLocaleString() }}
            </span>

            <!-- Comment content -->
            <p class="comment-content">
                {{ comment.content }}
            </p>

            <!-- Buttons -->
            <div>
                <!-- Upvote button -->
                <span
                    class="comment-upvote"
                    v-bind:class="{ 'comment-upvote-active': comment.rateStatus === 'upvote' }"
                    v-on:click="rateComment(comment, comment.rateStatus !== 'upvote' ? 'upvote' : 'neutral')"
                >
                    <i class="fas fa-thumbs-up"></i>
                </span>

                <!-- Comment points -->
                <span class="comment-points">
                    {{ comment.points }}
                </span>

                <!-- Downvote button -->
                <span
                    class="comment-downvote"
                    v-bind:class="{ 'comment-downvote-active': comment.rateStatus === 'downvote' }"
                    v-on:click="rateComment(comment, comment.rateStatus !== 'downvote' ? 'downvote' : 'neutral')"
                >
                    <i class="fas fa-thumbs-down"></i>
                </span>

                <!-- Reply button -->
                <button
                    class="btn btn-primary btn-sm ml-3"
                    v-on:click="comment.reply.isOpen = true"
                    v-show="!comment.reply.isOpen"
                >
                    Reply
                </button>
            </div>

            <!-- New reply -->
            <div class="mt-4" v-show="comment.reply.isOpen">
                <!-- Input field -->
                <textarea
                    class="form-control mb-2 shadow-sm"
                    v-model="comment.reply.draft"
                ></textarea>

                <!-- Send button -->
                <button
                    class="btn btn-primary"
                    v-on:click="sendComment(comment)"
                >
                    Send
                </button>

                <!-- Cancel button -->
                <button
                    class="btn btn-secondary"
                    v-on:click="comment.reply.isOpen = false"
                >
                    Cancel
                </button>
            </div>

            <!-- Replies (container) -->
            <div
                class="mt-4"
                v-show="comment.replies.length > 0"
            >
                <!-- Reply -->
                <div
                    v-for="reply in comment.replies"
                    class="comment-box"
                >
                    <!-- Reply username -->
                    <b>{{ reply.user.name }}</b>

                    <!-- Reply date -->
                    <span class="comment-date">
                        {{ comment.date.toLocaleString() }}
                    </span>

                    <!-- Reply content -->
                    <p class="comment-content">
                        {{ reply.content }}
                    </p>

                    <!-- Buttons -->
                    <div>
                        <!-- Upvote button -->
                        <span
                            class="comment-upvote"
                            v-bind:class="{ 'comment-upvote-active': reply.rateStatus === 'upvote' }"
                            v-on:click="rateComment(reply, reply.rateStatus !== 'upvote' ? 'upvote' : 'neutral')"
                        >
                            <i class="fas fa-thumbs-up"></i>
                        </span>

                        <!-- Comment points -->
                        <span class="comment-points">
                            {{ reply.points }}
                        </span>

                        <!-- Downvote button -->
                        <span
                            class="comment-downvote"
                            v-bind:class="{ 'comment-downvote-active': reply.rateStatus === 'downvote' }"
                            v-on:click="rateComment(reply, reply.rateStatus !== 'downvote' ? 'downvote' : 'neutral')"
                        >
                            <i class="fas fa-thumbs-down"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'videoKey',
        ],
        data() {
            return {
                comments: [],
                newCommentDraft: '',
            };
        },
        created() {
            this.loadComments();
        },
        methods: {
            async loadComments() {
                try {
                    const response = await axios.get(`/api/video/${this.videoKey}/comments`);

                    // TODO: remove response checking, axios will throw an exception if request wasn't successful anyway
                    if (response.status === 200) {
                        this.comments = response.data;

                        this.comments = [];
                        response.data.forEach(comment => {
                            this.comments.push(this.createCommentFromResponse(comment));
                        });
                    }
                } catch (err) {
                    console.error(err);
                }
            },
            async sendComment(parent = null) {
                try {
                    let data;
                    const isReply = parent !== null;

                    if (!isReply) {
                        data = {
                            content: this.newCommentDraft,
                        };
                    } else {
                        data = {
                            content: parent.reply.draft,
                            parent: parent.id,
                        };
                    }

                    const response = await axios.post(`/api/video/${this.videoKey}/comments`, data);

                    // TODO: remove response checking, axios will throw an exception if request wasn't successful anyway
                    if (response.status === 201) {
                        if (!isReply) {
                            this.comments.unshift(this.createCommentFromResponse(response.data));

                            this.newCommentDraft = '';
                        } else {
                            parent.replies.push(response.data);

                            parent.reply.isOpen = false;
                            parent.reply.draft = '';
                        }
                    }
                } catch (err) {
                    console.error(err);
                }
            },
            async rateComment(comment, rateType = 'neutral') {
                try {
                    const response = await axios.post(`/api/comments/${comment.id}/rate`, {
                        rateType: rateType,
                    });

                    // TODO: remove response checking, axios will throw an exception if request wasn't successful anyway
                    if (response.status === 200) {
                        // Set the comment to neutral first
                        switch (comment.rateStatus) {
                            case 'upvote':
                                comment.points--;
                                break;

                            case 'downvote':
                                comment.points++;
                                break;

                            default:
                                break;
                        }
                        comment.rateStatus = 'neutral';

                        // And then apply the new rating
                        switch (rateType) {
                            case 'upvote':
                                comment.rateStatus = 'upvote';
                                comment.points++;
                                break;

                            case 'downvote':
                                comment.rateStatus = 'downvote';
                                comment.points--;
                                break;

                            default:
                                break;
                        }
                    }
                } catch (err) {
                    console.error(err);
                }
            },
            countComments() {
                let count = this.comments.length;

                this.comments.forEach(comment => {
                    count += comment.replies.length;
                });

                return count;
            },
            createCommentFromResponse(comment) {
                return {
                    id: comment.id,
                    content: comment.content,
                    date: new Date(comment.created_at),
                    points: comment.points,
                    rateStatus: comment.authUserRating,
                    user: comment.user,
                    replies: comment.comments.map(reply => {
                        return {
                            id: reply.id,
                            content: reply.content,
                            date: new Date(reply.created_at),
                            points: reply.points,
                            rateStatus: reply.authUserRating,
                            user: reply.user,
                        };
                    }),
                    reply: {
                        isOpen: false,
                        draft: '',
                    },
                };
            },
        },
    };
</script>
