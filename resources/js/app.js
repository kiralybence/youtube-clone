require('./bootstrap');

/**
 * Vue.js stuff
 */

window.Vue = require('vue');

import Vue from 'vue';

Vue.component('comment-section', require('./components/CommentSection.vue').default);

const app = new Vue({
    el: '#app',
});
