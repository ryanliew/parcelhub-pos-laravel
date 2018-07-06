
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

window.events = new Vue();
import VueNoty from 'vuejs-noty';

Vue.use(VueNoty);

window.flash = function(message, level = 'success'){
 	window.events.$emit('flash', {message, level});
};



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.component('flash', require('./components/Flash.vue'));
Vue.component('text-input', require('./components/TextInput.vue'));
Vue.component('textarea-input', require('./components/TextAreaInput.vue'));


Vue.component('branches-dialog', require('./components/branches/Dialog.vue'));
Vue.component('branch-selector', require('./components/branches/Selector.vue'));


const app = new Vue({
    el: '#app'
});
