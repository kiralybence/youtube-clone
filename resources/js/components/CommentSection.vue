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
                    v-bind:class="{ 'comment-upvote-active': comment.rateStatus === 'upvoted' }"
                    v-on:click="rateComment(comment, 'upvote')"
                >
                    <i class="fas fa-thumbs-up"></i>
                </span>

                <!-- Comment points -->
                <span class="comment-points">
                    {{ Math.floor(Math.random() * 20) }}
                </span>

                <!-- Downvote button -->
                <span
                    class="comment-downvote"
                    v-bind:class="{ 'comment-downvote-active': comment.rateStatus === 'downvoted' }"
                    v-on:click="rateComment(comment, 'downvote')"
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
                        <span class="comment-upvote">
                            <i class="fas fa-thumbs-up"></i>
                        </span>

                        <!-- Reply points -->
                        <span class="comment-points">
                            {{ Math.floor(Math.random() * 20) }}
                        </span>

                        <!-- Downvote button -->
                        <span class="comment-downvote">
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

                    if (response.status === 200) {
                        this.comments = response.data;

                        this.comments = [];
                        response.data.forEach(comment => {
                            this.comments.push({
                                id: comment.id,
                                content: comment.content,
                                date: new Date(comment.created_at),
                                rateStatus: 'neutral', // TODO
                                user: comment.user,
                                replies: comment.comments,
                                // TODO: don't reset drafts on load
                                reply: {
                                    isOpen: false,
                                    draft: '',
                                }
                            });
                        });
                    }
                } catch (err) {
                    console.error(err);
                }
            },
            async sendComment(parent = null) {
                try {
                    let data;

                    if (parent === null) {
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

                    if (response.status === 200) {
                        await this.loadComments();

                        if (parent === null) {
                            this.newCommentDraft = '';
                        } else {
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
                        rating: rateType,
                    });

                    if (response.status === 200) {
                        await this.loadComments();
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
        },
    };
</script>
