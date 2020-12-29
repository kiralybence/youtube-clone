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

// Set video quality
$('.quality-setter').on('click', function() {
    // Reset all buttons
    $('.quality-setter').removeClass('font-weight-bold');
    $('.quality-setter').removeClass('btn-dark');
    $('.quality-setter').addClass('btn-secondary');

    // Highlight currently selected button
    $(this).removeClass('btn-secondary');
    $(this).addClass('btn-dark');
    $(this).addClass('font-weight-bold');

    const url = $(this).data('url');
    const video = document.querySelector('#video-main');
    const source = document.querySelector('#video-main source');
    const currentTime = video.currentTime;
    const paused = video.paused;

    // Change the source url
    source.setAttribute('src', url);

    // Load the new video
    video.load();

    // Restore the previous timestamp
    video.currentTime = currentTime;

    // Restore play/pause
    if (paused) {
        video.pause();
    } else {
        video.play();
    }
});
