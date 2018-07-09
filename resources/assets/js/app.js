
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
Vue.component('selector-input', require('./components/SelectorInput.vue'));


Vue.component('branches-dialog', require('./components/branches/Dialog.vue'));
Vue.component('branch-selector', require('./components/branches/Selector.vue'));


Vue.component('vendors-dialog', require('./components/vendors/Dialog.vue'));
// Vue.component('vendors-selector', require('./components/vendors/Selector.vue'));

Vue.component('zones-dialog', require('./components/zones/Dialog.vue'));


Vue.component('users-dialog', require('./components/users/Dialog.vue'));

Vue.component('payments-dialog', require('./components/payments/Dialog.vue'));

const app = new Vue({
    el: '#app'
});
