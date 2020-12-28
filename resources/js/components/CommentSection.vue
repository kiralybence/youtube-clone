<template>
    <div>
        <h2 class="h5 mb-3">{{ comments.length }} comments</h2>
        <div class="mb-4">
            <textarea class="form-control mb-2 shadow-sm" v-model="newCommentDraft"></textarea>
            <button class="btn btn-primary" v-on:click="sendComment()">Send</button>
        </div>

        <p v-show="comments.length === 0">
            Cold is the void...
        </p>

        <div v-for="comment in comments" class="comment-box">
            <b>{{ comment.user.name }}</b> <span class="comment-date">{{ comment.date.toLocaleString() }}</span>
            <p class="comment-content">{{ comment.content }}</p>

            <div>
                <span class="comment-upvote"><i class="fas fa-thumbs-up"></i></span>
                <span class="comment-points">{{ Math.floor(Math.random() * 20) }}</span>
                <span class="comment-downvote"><i class="fas fa-thumbs-down"></i></span>
                <button class="btn btn-primary btn-sm ml-3" v-on:click="comment.reply.isOpen = true" v-show="!comment.reply.isOpen">Reply</button>
            </div>

            <div class="mt-4" v-show="comment.reply.isOpen">
                <textarea class="form-control mb-2 shadow-sm" v-model="comment.reply.draft"></textarea>
                <button class="btn btn-primary" v-on:click="sendComment(comment)">Send</button>
                <button class="btn btn-secondary" v-on:click="comment.reply.isOpen = false">Cancel</button>
            </div>

            <div class="mt-4" v-show="comment.children.length > 0">
                <div v-for="child in comment.children" class="comment-box">
                    <b>{{ child.user.name }}</b> <span class="comment-date">{{ comment.date.toLocaleString() }}</span>
                    <p class="comment-content">{{ child.content }}</p>

                    <div>
                        <span class="comment-upvote"><i class="fas fa-thumbs-up"></i></span>
                        <span class="comment-points">{{ Math.floor(Math.random() * 20) }}</span>
                        <span class="comment-downvote"><i class="fas fa-thumbs-down"></i></span>
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
                                user: comment.user,
                                children: comment.comments,
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
        },
    };
</script>
